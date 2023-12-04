<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Str;
use App\Models\ShopItem;
use App\Models\ShopItemImage;
use App\Models\ShopCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\ShopGroup;
use App\Models\ShopItemProperty;
use App\Models\ShopItemListItem;
use App\Models\PropertyValueInt;
use App\Models\PropertyValueString;
use App\Models\PropertyValueFloat;
use Illuminate\Filesystem\Filesystem;
use App\Http\Controllers\Admin\SearchController;
use App\Services\Helpers\File;

class ShopItemController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parent = Arr::get($_REQUEST, 'parent_id', 0);

        $properties = self::getProperties($parent);

        return view('admin.shop.item.create', [
            'breadcrumbs' => ShopGroupController::breadcrumbs($parent > 0 ? ShopGroup::find($parent) : false, [], true),
            'parent_id' => $parent,
            'currencies' => ShopCurrency::orderBy('sorting', 'asc')->get(),
            'properties' => $properties,
            'lists' => self::getListItems($properties),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->saveShopItem($request);
    }

    public function show()
    {
        return redirect()->to(route("shop.index"));
    }

    public function index()
    {
        return redirect()->to(route("shop.index"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShopItem $shopItem)
    {

        $properties = self::getProperties($shopItem->shop_group_id);

        $aProperty_Value_Int = [];
        foreach(PropertyValueInt::where("entity_id", $shopItem->id)->get() as  $oProperty_Value_Int) {
            $aProperty_Value_Int[$oProperty_Value_Int->property_id][$oProperty_Value_Int->id] = $oProperty_Value_Int->value;
        }

        $aProperty_Value_String = [];
        foreach(PropertyValueString::where("entity_id", $shopItem->id)->get() as  $oProperty_Value_String) {
            $aProperty_Value_String[$oProperty_Value_String->property_id][$oProperty_Value_String->id] = $oProperty_Value_String->value;
        }

        $aProperty_Value_Float = [];
        foreach(PropertyValueFloat::where("entity_id", $shopItem->id)->get() as  $oProperty_Value_Float) {
            $aProperty_Value_Float[$oProperty_Value_Float->property_id][$oProperty_Value_Float->id] = $oProperty_Value_Float->value;
        }

        return view('admin.shop.item.edit', [
            'shopItem' => $shopItem,
            'images' => $shopItem->getImages(),
            'breadcrumbs' => ShopGroupController::breadcrumbs($shopItem->ShopGroup, [], true),
            'store_path' => Shop::$store_path,
            'currencies' => ShopCurrency::orderBy('sorting', 'asc')->get(),
            'properties' => self::getProperties($shopItem->shop_group_id),
            'property_value_ints' => $aProperty_Value_Int,
            'property_value_strings' => $aProperty_Value_String,
            'property_value_floats' => $aProperty_Value_Float,
            'lists' => self::getListItems($properties),
        ]);
    }

    public static function getProperties($shop_group_id, $type = false, $filter = false)
    {
        $properties = ShopItemProperty::join('shop_item_property_for_groups', 'shop_item_properties.id', '=', 'shop_item_property_for_groups.shop_item_property_id')
                        ->select('shop_item_properties.*')
                        ->where('shop_item_property_for_groups.shop_group_id', $shop_group_id)    
                        ->orderBy("shop_item_properties.sorting", "ASC")
                        ;
        if ($type) {
            $properties->where("type", $type);
        }

        if ($filter) {
            $properties->where("show_in_filter", 1);
        }

        return $properties->get();
    }

    public static function getListItems($properties)
    {
        $lists = [];
        foreach ($properties as $property) {
            if ($property->type == 4 && $property->shop_item_list_id > 0) {
                $lists[] = $property->shop_item_list_id;
            }
        }

        $aLists = [];
        foreach(ShopItemListItem::whereIn("shop_item_list_id", $lists)->get() as  $oShopItemList) {
            $aLists[$oShopItemList->shop_item_list_id][$oShopItemList->id] = $oShopItemList->value;
        }

        return $aLists;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopItem $shopItem)
    {

        return $this->saveShopItem($request, $shopItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ShopItem $shopItem)
    {

        $shopItem->delete();

        return redirect()->back()->withSuccess("Товар был успешно удален!");
    }

    public function saveShopItem (Request $request, $shopItem = false) 
    {

        $oShop = Shop::get();

        if (!$shopItem) {
            $shopItem = new ShopItem();
        }


        //$request->validate([
            // 'name' => ['required', 'string', 'max:255'],
            // 'seo_title' => ['required', 'string', 'max:255'],
            // 'seo_keywords' => ['required', 'string', 'max:255'],
            // 'path' => ['required', 'string', 'max:255'],
        //]);

        

        $shopItem->name = $request->name;
        $shopItem->description = $request->description ?? '';
        $shopItem->text = $request->text ?? '';
        $shopItem->active = $request->active == 'on' ? 1 : 0;
        $shopItem->indexing = $request->indexing == 'on' ? 1 : 0;
        $shopItem->shop_group_id = $request->shop_group_id ?? 0;
        $shopItem->sorting = $request->sorting ?? 0;
        $shopItem->marking = $request->marking;
        $shopItem->weight = $request->weight;
        $shopItem->width = $request->width;
        $shopItem->height = $request->height;
        $shopItem->length = $request->length;
        $shopItem->path = !empty(trim($request->path)) ? $request->path : Str::transliteration($request->name);
        $shopItem->seo_title = $request->seo_title ?? '';
        $shopItem->seo_description = $request->seo_description ?? '';
        $shopItem->seo_keywords = $request->seo_keywords ?? '';
        $shopItem->price = $request->price ?? 0;
        $shopItem->shop_currency_id = $request->shop_currency_id ?? 0;

        $shopItem->save();

        $this->setUrl($shopItem);

        if (isset($request->image)) {

            for ($i = 0; $i < count($request->image); $i++) {

                $oShopItemImage = new ShopItemImage();
                $oShopItemImage->shop_item_id   = $shopItem->id;
                $oShopItemImage->save();

                $shopItem->createDir();

                //сохраняем оригинал
                $request->image[$i]->storeAs($shopItem->path(), $request->image[$i]->getClientOriginalName());

                //переводим в webp либо возвращаем исходный
                $original = File::webpConvert(Storage::path($shopItem->path()), $request->image[$i]->getClientOriginalName());

                $fileInfo = File::fileInfoFromStr($original);

                //большое изображение
                $image_large = Image::make($original);
                if ($oShop->preserve_aspect_ratio == 1) {
                    $image_large->resize($oShop->image_large_max_width, $oShop->image_large_max_height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                } else {
                    $image_large->fit($oShop->image_large_max_width, $oShop->image_large_max_height);
                }
                $sImageLargeName = 'image_large'. $oShopItemImage->id .'.' . $fileInfo["extension"];
                $image_large->save(Storage::path($shopItem->path()) . $sImageLargeName);
                $oShopItemImage->image_large = $sImageLargeName;

                //превью
                $image_small = Image::make($original);
                if ($oShop->preserve_aspect_ratio_small == 1) {
                    $image_small->resize($oShop->image_small_max_width, $oShop->image_small_max_height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                } else {
                    $image_small->fit($oShop->image_small_max_width, $oShop->image_small_max_height);
                }
                $sImageSmallName = 'image_small'. $oShopItemImage->id .'.' . $fileInfo["extension"];
                $image_small->save(Storage::path($shopItem->path()) . $sImageSmallName);
                $oShopItemImage->image_small = $sImageSmallName;

                unlink($original);

                $oShopItemImage->save();

            }
        }

        $this->saveItemProperties($request, $shopItem);

        //скидка
        $ShopItemDiscountController = new ShopItemDiscountController();
        foreach ($shopItem->ShopItemDiscount as $ShopItemDiscount) {
            $ShopItemDiscountController->saveShopItemDiscount($ShopItemDiscount, $ShopItemDiscount->ShopDiscount, $shopItem);
        }

        $SearchController = new SearchController();
        $SearchController->indexingShopItem($shopItem, true);
        
        $message = "Товар был успешно сохранен!";

        if ($request->apply) {
            return redirect()->to(route("shop.index") . ($shopItem->shop_group_id > 0 ? '?parent_id=' . $shopItem->shop_group_id : ''))->withSuccess($message);
        } else {
           return redirect()->back()->withSuccess($message);
        }
    }

    public function setUrl(ShopItem $shopItem)
    {

        $shopItem->url = $shopItem->url();
        $shopItem->save();
    }

    public function saveItemProperties(Request $request, ShopItem $shopItem)
    {
        $shop_group_id = $shopItem->modification_id == 0 ? $shopItem->shop_group_id : ShopItem::find($shopItem->modification_id)->shop_group_id;
        foreach (self::getProperties($shop_group_id) as $property) {
        
            $property_id = 'property_' . $property->id;

            if (isset($request->$property_id)) {

                if (is_array($request->$property_id)) {
                    foreach ($request->$property_id as $Value) {

                        //if (!empty(trim($Value))) {

                            $oProperty_Value = ShopItemProperty::getObjectByType($property->type);        
                            $oProperty_Value->property_id = $property->id;
                            $oProperty_Value->entity_id = $shopItem->id;
                            $oProperty_Value->value = !empty($Value) ? $Value : ShopItemProperty::getDafaultValueByObject($oProperty_Value);
                            $oProperty_Value->save();
                        //}
                    }
                } else {
                    
                    //checkbox
                    $oProperty_Value = new PropertyValueInt();
                    
                    $oProperty_Value->property_id = $property->id;
                    $oProperty_Value->entity_id = $shopItem->id;
                    $oProperty_Value->value = 1;
                    $oProperty_Value->save();
                }
            }

            /* старые свойства */
            $oCreatedProperty_Value = ShopItemProperty::getObjectByType($property->type); 

            foreach ($oCreatedProperty_Value::where("property_id", $property->id)->where("entity_id", $shopItem->id)->get() as $Value) {
                $property_id = 'property_' . $property->id . '_' . $Value->id;
                $property_id_checkbox = 'property_' . $property->id;
                //если чексбокс
                if ($property->type == 3) {
                    if (!isset($request->$property_id_checkbox)) {
                        $Value->delete();
                    }
                } else {
                    if (isset($request->$property_id)) {
                        $Value->value = $request->$property_id;
                        $Value->save();
                    } else {
                        $Value->value = ShopItemProperty::getDafaultValueByObject($oCreatedProperty_Value);
                        $Value->save();
                    }
                }
            }
        }
    }


    public function deleteImage($id) 
    {

        $response = false;

        $oShopItemImage = ShopItemImage::find($id);
        if (!is_null($oShopItemImage)) {

            $oShopItemImage->delete();

            $response = true;
        }

        return response()->json($response);
    }

    public function deletePropertyValue($property, $id)
    {
        $response = false;

        $ShopItemProperty = ShopItemProperty::find($property);
        if (!is_null($ShopItemProperty)) {
            $oProperty_Value = ShopItemProperty::getObjectByType($ShopItemProperty->type); 
            $oValue = $oProperty_Value->find($id);
            $oValue->delete();
            $response = true;
        }

        return response()->json($response);
    }

    

}
