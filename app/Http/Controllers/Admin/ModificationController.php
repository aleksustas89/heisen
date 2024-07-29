<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopItem;
use App\Models\Shop;
use App\Models\PropertyValueInt;
use Illuminate\Http\Request;
use App\Models\ShopItemProperty;
use App\Models\ShopItemListItem;
use App\Models\ShopModificationImage;
use App\Http\Controllers\ShopItemDiscountController;
use App\Models\Page;


class ModificationController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!is_null($ShopItem = ShopItem::find($request->shop_item_id))) {

            return view('admin.shop.modification.index', [
                'shopItems' => ShopItem::where("modification_id", $request->shop_item_id)->get(),
                'oShopItem' => $ShopItem,
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        if ($request->window) {
            return $this->createWindow($request);
        } else {
            $shopItem = ShopItem::find($request->shop_item_id);

            $properties = ShopItemController::getProperties($shopItem->shop_group_id, 4, false, 0, 1);

            return view('admin.shop.modification.create', [
                'properties' => $properties,
                'lists' => ShopItemController::getListItems($properties),
                'shop_item_id' => $shopItem->id,
            ]);
        }
    }

    public function createWindow(Request $request)
    {
        $aResult = [];

        $oShopItem = false;

        if ($request->shop_item_id && !is_null($oShopItem = \App\Models\ShopItem::find($request->shop_item_id))) {

            $properties = ShopItemController::getProperties($oShopItem->shop_group_id, 4, false, 0, 1);

            $aMatrix = [];
    
            foreach ($properties as $property) {
    
                $id = "property_" . $property->id;
    
                if ($property->type == 4 && isset($request->$id)) {
    
                    foreach ($request->$id as $k => $value) {
                        $aMatrix[$property->id][] = $value;
                    }
                }
            }
    
            if (count($aMatrix) == 1) {
    
                foreach ($aMatrix as $k => $aValues) {
            
                    $oShopItemProperty = \App\Models\ShopItemProperty::find($k);
            
                    foreach ($aValues as $Value) {
                        $id = count($aResult);
    
                        $oShopItemListItem = \App\Models\ShopItemListItem::find($Value);
    
                        $aResult[$id]["properties"][$oShopItemProperty->id] = $Value;
                        $aResult[$id]["name"] = $oShopItemProperty->name .": " . $oShopItemListItem->value;
                    }
            
                }
            
            } else if (count($aMatrix) > 1) {
                $firstKey = array_key_first($aMatrix);
            
                foreach ($aMatrix[$firstKey] as $aFirstValue) {
                
                    $oFirstProperty = \App\Models\ShopItemProperty::find($firstKey);
                    $oFirstListItem = \App\Models\ShopItemListItem::find($aFirstValue);
                
                    foreach ($aMatrix as $k => $aValues) {
                        if ($k != $firstKey) {
    
                            $oShopItemProperty = \App\Models\ShopItemProperty::find($k);
                
                            foreach ($aValues as $Value) {
    
                                $id = count($aResult);
                                
                                $oShopItemListItem = \App\Models\ShopItemListItem::find($Value);
    
                                $aResult[$id]["properties"][$oFirstProperty->id] = $aFirstValue;
                                $aResult[$id]["properties"][$oShopItemProperty->id] = $Value;
                                $aResult[$id]["name"] = $oFirstProperty->name . ": " . $oFirstListItem->value . ", " . $oShopItemProperty->name .": " . $oShopItemListItem->value;
                            }
                        }
                    }
                }
            }
        }

        return response()->view("admin.shop.modification.window", [
            "aResult" => $aResult,
            "ShopItem" => $oShopItem,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        if (!is_null($oShopItem = ShopItem::find($request->shop_item_id))) {

            $aItems = [];

            foreach ($request->all() as $k => $input) {
                $eKey = explode("_", $k);
                if (count($eKey) && $eKey[0] == 'item') {
                    switch ($eKey[2]) {
                        case 'price':
                            $aItems[$eKey[1]]["price"] = $input;
                        break;
                        case 'name':
                            $aItems[$eKey[1]]["name"] = $input;
                        break;
                        case 'property':
                            $aItems[$eKey[1]]["properties"][$eKey[3]] = $input;
                        break;
                        case 'image':
                            $aItems[$eKey[1]]["image"] = $input;
                        break;
                    }
                }
            }
    
            foreach ($aItems as $aItem) {
                $ShopItem = new ShopItem();
                $ShopItem->modification_id = $oShopItem->id;
                $ShopItem->price = $aItem["price"];
                $ShopItem->shop_currency_id = $oShopItem->shop_currency_id;
                $ShopItem->guid = \App\Services\Helpers\Guid::get();
                $ShopItem->save();

                $Page = new Page();
                $Page->entity_id = $ShopItem->id;
                $Page->type = 2;
                $Page->save();


                if (!empty($aItem["image"])) {
                    $ShopModificationImage = new ShopModificationImage();
                    $ShopModificationImage->shop_item_id = $ShopItem->id;
                    $ShopModificationImage->shop_item_image_id = $aItem["image"];
                    $ShopModificationImage->save();
                }

                foreach ($aItem["properties"] as $property_id => $value) {
                    $PropertyValueInt = new PropertyValueInt();
                    $PropertyValueInt->entity_id = $ShopItem->id;
                    $PropertyValueInt->property_id = $property_id;
                    $PropertyValueInt->value = $value;
                    $PropertyValueInt->save();
                }

                $this->saveStaticModificaitonFields($ShopItem, $oShopItem);
            }

            return response()->view('admin.shop.modification.index', [
                'shopItems' => ShopItem::where("modification_id", $request->shop_item_id)->get(),
                'oShopItem' => $oShopItem,
            ]);
        } 
    }

    public function saveStaticModificaitonFields($Modification, $ShopItem)
    {

        $aName = [];
        $aPath = [];

        foreach ($Modification->PropertyValueInts as $PropertyValueInt) {

            if ($PropertyValueInt->value > 0) {
                $aName[] = $PropertyValueInt->ShopItemProperty->name . ": " . $PropertyValueInt->ShopItemListItem->value;
                $aPath[] = $PropertyValueInt->ShopItemListItem->value;
            }
        }

        $Modification->path = \App\Services\Helpers\Str::transliteration(implode("-", $aPath));
        $Modification->url = $ShopItem->url . "-" . $Modification->path;
        $Modification->name = $ShopItem->name . ", " . implode(",", $aName);
        $Modification->save();
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShopItem $Modification)
    {

        $oShopItem = ShopItem::find($Modification->modification_id);

        $properties = ShopItemController::getProperties($oShopItem->shop_group_id, 4);

        $aProperty_Value_Int = [];
        foreach(PropertyValueInt::where("entity_id", $Modification->id)->get() as  $oProperty_Value_Int) {
            $aProperty_Value_Int[$oProperty_Value_Int->property_id][$oProperty_Value_Int->id] = $oProperty_Value_Int->value;
        }

        $breadcrumbs = ShopGroupController::breadcrumbs($oShopItem->shop_group_id > 0 ? $oShopItem->ShopGroup : false, [], false);

        $breadcrumbs[] = self::breadcrumbs($oShopItem, true);

        return view('admin.shop.modification.edit', [
            'breadcrumbs' => $breadcrumbs,
            'Modification' => $Modification,
            'modificationName' => $Modification->modificationName(),
            'shopItem' => $oShopItem,
            'properties' => $properties,
            'property_value_ints' => $aProperty_Value_Int,
            'lists' => ShopItemController::getListItems($properties),
        ]);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopItem $Modification)
    {

        $shop = Shop::get();

        $Modification->price = $request->price;

        $Modification->save();

        $ShopItemController = new \App\Http\Controllers\Admin\ShopItemController();

        $ShopItemController->saveItemProperties($request, $Modification);

        $ShopModificationImage = ShopModificationImage::where("shop_item_id", $Modification->id)->first();
        if (is_null($ShopModificationImage) && $request->shop_item_image_id > 0) {
            $ShopModificationImage = new ShopModificationImage();
            $ShopModificationImage->shop_item_id = $Modification->id;
        } else if($request->shop_item_image_id == 0 && !is_null($ShopModificationImage)) {
            $ShopModificationImage->delete();
        }

        if ($request->shop_item_image_id > 0) {
            $ShopModificationImage->shop_item_image_id = $request->shop_item_image_id;
            $ShopModificationImage->save();
        }

        //скидка
        $ShopItemDiscountController = new ShopItemDiscountController();
        foreach ($Modification->ShopItemDiscounts as $ShopItemDiscount) {
            $ShopItemDiscountController->saveShopItemDiscount($ShopItemDiscount, $ShopItemDiscount->ShopDiscount, $Modification);
        }

        $this->saveStaticModificaitonFields($Modification, ShopItem::find($Modification->modification_id));

        $message = "Модификация была успешно изменена!";
        if ($request->apply) {
            return redirect()->to(route("shop.shop-item.edit", ['shop' => $shop->id, 'shop_item' => $Modification->modification_id]))->withSuccess($message);
        } else {
            return redirect()->back()->withSuccess($message);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopItem $Modification)
    {

        $Modification->deleted = 1;
        $Modification->save();

        return response()->json(true);

    }

    public static function breadcrumbs($ShopItem, $lastItemIsLink = false)
    {

        $shop = Shop::get();

        $aResult["name"] = 'Модификации товара - ' . $ShopItem->name;
        if ($lastItemIsLink) {
            $aResult["url"] = route("shop.shop-item.edit", ['shop' => $shop->id, 'shop_item' => $ShopItem->id]);
        }
        
        return $aResult;
    }

    public function defaultModification(ShopItem $shopItem)
    {

        foreach (ShopItem::where("modification_id", $shopItem->modification_id)->get() as $oShopItem) {

            if ($oShopItem->id == $shopItem->id) {
                $oShopItem->default_modification = 1;
            } else {
                $oShopItem->default_modification = 0;
            }

            $oShopItem->save();
        }

        return response()->json(true);
    }
}
