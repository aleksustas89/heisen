<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopItem;
use App\Models\PropertyValueInt;
use Illuminate\Http\Request;
use App\Models\ShopItemProperty;
use App\Models\ShopItemListItem;
use App\Models\ShopModificationImage;


class ModificationController extends Controller
{

    public static $items_on_page = 15;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!is_null($ShopItem = ShopItem::find($request->shop_item_id))) {

            $breadcrumbs = ShopGroupController::breadcrumbs($ShopItem->shop_group_id > 0 ? $ShopItem->ShopGroup : false, [], true);

            $breadcrumbs[] = self::breadcrumbs($ShopItem);

            return view('admin.shop.modification.index', [
                'breadcrumbs' => $breadcrumbs,
                'shopItems' => ShopItem::where("modification_id", $request->shop_item_id)->paginate(self::$items_on_page),
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

            $properties = ShopItemController::getProperties($shopItem->shop_group_id);

            $breadcrumbs = ShopGroupController::breadcrumbs($shopItem->shop_group_id > 0 ? $shopItem->ShopGroup : false, [], true);

            $breadcrumbs[] = self::breadcrumbs($shopItem, true);
    
            return view('admin.shop.modification.create', [
                'breadcrumbs' => $breadcrumbs,
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

            $properties = ShopItemController::getProperties($oShopItem->shop_group_id, 4);

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
                        $aResult[$id]["name"] = $oShopItem->name . ", ". $oShopItemProperty->name .": " . $oShopItemListItem->value;
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
                                $aResult[$id]["name"] = $oShopItem->name . ", ". $oFirstProperty->name . ": " . $oFirstListItem->value . ", " . $oShopItemProperty->name .": " . $oShopItemListItem->value;
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
                $ShopItem->name = $aItem["name"];
                $ShopItem->price = $aItem["price"];
                $ShopItem->shop_currency_id = $oShopItem->shop_currency_id;
                $ShopItem->guid = \App\Services\Helpers\Guid::get();
                $ShopItem->path = \App\Services\Helpers\Str::transliteration(self::generateUrl($aItem["properties"]));

                $ShopItem->save();

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
            }

            return redirect()->to(route("modification.index") ."?shop_item_id=". $oShopItem->id)->withSuccess("Модификации были успешно созданы!");

        } else {
            return redirect()->back()->withErrors("Ошибка, id товара не было передано!");
        }
    }

    public static function generateUrl($aPropertiesValues) : string
    {
        $aPath = [];
        foreach ($aPropertiesValues as $property_id => $value) {
            if (!is_null($Property = ShopItemProperty::find($property_id)) && !is_null($ShopItemListItem = ShopItemListItem::find($value))) {
                $aPath[] = $Property->name . '-' . $ShopItemListItem->value;
            }
        }

        return implode("-", $aPath);
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

        $breadcrumbs = ShopGroupController::breadcrumbs($oShopItem->shop_group_id > 0 ? $oShopItem->ShopGroup : false, [], true);

        $breadcrumbs[] = self::breadcrumbs($oShopItem, true);

        return view('admin.shop.modification.edit', [
            'breadcrumbs' => $breadcrumbs,
            'Modification' => $Modification,
            'shopItem' => $oShopItem,
            'properties' => ShopItemController::getProperties($oShopItem->shop_group_id, 4),
            'property_value_ints' => $aProperty_Value_Int,
            'lists' => ShopItemController::getListItems($properties),
        ]);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopItem $Modification)
    {
        $Modification->name = $request->name;
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

        $message = "Модификация была успешно изменена!";
        if ($request->apply) {
            return redirect()->to(route("modification.index") . '?shop_item_id=' . $Modification->modification_id)->withSuccess($message);
        } else {
            return redirect()->back()->withSuccess($message);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopItem $Modification)
    {

        $Modification->delete();

        return redirect()->back()->withSuccess("Модификация была успешно удалена!");
    }

    public static function breadcrumbs($ShopItem, $lastItemIsLink = false)
    {
        $aResult["name"] = 'Модификации товара - ' . $ShopItem->name;
        if ($lastItemIsLink) {
            $aResult["url"] = route("modification.index") . "?shop_item_id=" . $ShopItem->id;
        }
        
        return $aResult;
    }
}
