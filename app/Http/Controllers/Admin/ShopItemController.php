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
use App\Models\ShopItemAssociatedGroup;
use App\Models\ShopItemAssociatedItem;


class ShopItemController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Shop $shop)
    {
        $parent = Arr::get($_REQUEST, 'parent_id', 0);

        $properties = self::getProperties($parent);

        return view('admin.shop.item.create', [
            'breadcrumbs' => ShopGroupController::breadcrumbs($parent > 0 ? ShopGroup::find($parent) : false, [], false),
            'parent_id' => $parent,
            'currencies' => ShopCurrency::orderBy('sorting', 'asc')->get(),
            'properties' => $properties,
            'lists' => self::getListItems($properties),
            'shop' => $shop,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Shop $shop)
    {
        return $this->saveShopItem($request, $shop);
    }

    public function show()
    {
        return redirect()->to(route("shop.index"));
    }

    public function index()
    {
        return redirect()->to(route("shop.index"));
    }

    public function copy(ShopItem $shopItem)
    {

        $shopItem->copy($shopItem->modification_id > 0 ? $shopItem->modification_id : 0);

        return redirect()->back()->withSucces("Элемент был успешно скопирован!");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop, ShopItem $shopItem)
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

        $canonicalShopItem = ShopItem::find($shopItem->canonical);
        
        return view('admin.shop.item.edit', [
            'shopItem' => $shopItem,
            'images' => $shopItem->getImages(),
            'breadcrumbs' => ShopGroupController::breadcrumbs($shopItem->ShopGroup, [], false),
            'store_path' => Shop::$store_path,
            'currencies' => ShopCurrency::orderBy('sorting', 'asc')->get(),
            'properties' => self::getProperties($shopItem->shop_group_id),
            'property_value_ints' => $aProperty_Value_Int,
            'property_value_strings' => $aProperty_Value_String,
            'property_value_floats' => $aProperty_Value_Float,
            'lists' => self::getListItems($properties),
            'shop' => $shop,
            "mShopItems" => ShopItem::where("modification_id", $shopItem->id)->get(),
            "canonicalName" => !is_null($canonicalShopItem) ? $canonicalShopItem->name ." [". $canonicalShopItem->id ."] / ". $canonicalShopItem->ShopGroup->name : ''
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
    public function update(Request $request, Shop $shop, ShopItem $shopItem)
    {

        return $this->saveShopItem($request, $shop, $shopItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Shop $shop, ShopItem $shopItem)
    {

        $shopItem->delete();

        return redirect()->back()->withSuccess("Товар был успешно удален!");
    }

    public function saveShopItem (Request $request, Shop $shop, $shopItem = false) 
    {

        $transfer_images_from = false;

        if (!$shopItem) {
            $shopItem = new ShopItem();
        } else {

            if ($request->shop_group_id != $shopItem->shop_group_id) {
                $transfer_images_from = $shopItem->shortPath();
            }
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
        $shopItem->active = $request->active ?? 0;
        $shopItem->indexing = $request->indexing ?? 0;
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
        $shopItem->canonical = $request->canonical ?? 0;
        $shopItem->updated_at = date("Y-m-d H:i:s");

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
                if ($shop->preserve_aspect_ratio == 1) {
                    $image_large->resize($shop->image_large_max_width, $shop->image_large_max_height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                } else {
                    $image_large->fit($shop->image_large_max_width, $shop->image_large_max_height);
                }
                $sImageLargeName = 'image_large'. $oShopItemImage->id .'.' . $fileInfo["extension"];
                $image_large->save(Storage::path($shopItem->path()) . $sImageLargeName);
                $oShopItemImage->image_large = $sImageLargeName;

                //превью
                $image_small = Image::make($original);
                if ($shop->preserve_aspect_ratio_small == 1) {
                    $image_small->resize($shop->image_small_max_width, $shop->image_small_max_height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                } else {
                    $image_small->fit($shop->image_small_max_width, $shop->image_small_max_height);
                }
                $sImageSmallName = 'image_small'. $oShopItemImage->id .'.' . $fileInfo["extension"];
                $image_small->save(Storage::path($shopItem->path()) . $sImageSmallName);
                $oShopItemImage->image_small = $sImageSmallName;

                unlink($original);

                $oShopItemImage->save();

            }
        }

        if ($transfer_images_from) {
            $shopItem->changeImagesDirFrom($transfer_images_from);
        }

        $this->saveItemProperties($request, $shopItem);

        if ($request->apply_price_to_modifications) {
            foreach (ShopItem::where("modification_id", $shopItem->id)->get() as $mShopItem) {
                $mShopItem->price = $shopItem->price;
                $mShopItem->save();
            }
        }

        //скидка
        $ShopItemDiscountController = new ShopItemDiscountController();
        foreach ($shopItem->ShopItemDiscounts as $ShopItemDiscount) {
            $ShopItemDiscountController->saveShopItemDiscount($ShopItemDiscount, $ShopItemDiscount->ShopDiscount, $shopItem);
        }

        $SearchController = new SearchController();
        $SearchController->indexingShopItem($shopItem, true);

        if (!is_null($shopGroup = $shopItem->shopGroup)) {
            $shopGroup->setSubCount();
        }
        
        $message = "Товар был успешно сохранен!";

        if ($request->apply) {
            return redirect()->to(route("shop.index") . ($shopItem->shop_group_id > 0 ? '?parent_id=' . $shopItem->shop_group_id : ''))->withSuccess($message);
        } else {
           return redirect()->back()->withSuccess($message);
        }
    }

    public function searchCanonical(Request $request, ShopItem $shopItem)
    {
        $aResult = [];

        if (!empty($term = $request->input('term'))) {

            $ShopItems = ShopItem::where('modification_id', 0)
                ->where(function($query) use ($term) {
                    $query->orWhere('name', "LIKE", "%" . $term ."%")
                            ->orWhere("id", $term)
                            ->orWhere("marking", "LIKE", "LIKE", "%" . $term ."%");
                })->get();

            foreach ($ShopItems as $shopItem) {
                $aResult[] = ["value" => $shopItem->name ." [". $shopItem->id ."] / ". $shopItem->ShopGroup->name, "data" => $shopItem->id];
            }
        }

        return response()->json($aResult);
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


    public function deleteImage(ShopItem $shopItem, ShopItemImage $shopItemImage) 
    {

        $response = false;

        if (!is_null($shopItemImage)) {

            $shopItemImage->delete();

            $response = true;
        }

        return response()->json($response);
    }

    public function deletePropertyValue(ShopItem $shopItem, ShopItemProperty $shopItemProperty, $value_id)
    {
        $response = false;

        if (!is_null($shopItemProperty)) {
            $oProperty_Value = ShopItemProperty::getObjectByType($shopItemProperty->type); 
            $oValue = $oProperty_Value->find($value_id);
            $oValue->delete();
            $response = true;
        }

        return response()->json($response);
    }

    public function sortShopItemImages(Request $request)
    {

        foreach (json_decode($request->images) as $key => $item_image_id) {
            if (!is_null($ShopItemImage = ShopItemImage::find($item_image_id))) {
                $ShopItemImage->sorting = $key;
                $ShopItemImage->save();
            }
        }

        return response()->json(true);
    }

    
    public function addAssociated(Request $request, shopItem $shopItem)
    {

        $ShopItemAssociatedGroups = [];
        foreach (ShopItemAssociatedGroup::select("shop_group_associated_id")->where("shop_item_id", $shopItem->id)->get() as $ShopItemAssociatedGroup) {
            $ShopItemAssociatedGroups[] = $ShopItemAssociatedGroup->shop_group_associated_id;
        }

        $ShopItemAssociatedItems = [];
        foreach (ShopItemAssociatedItem::select("shop_item_associated_id")->where("shop_item_id", $shopItem->id)->get() as $ShopItemAssociatedItem) {
            $ShopItemAssociatedItems[] = $ShopItemAssociatedItem->shop_item_associated_id;
        }

        return response()->view("admin.shop.item.associated.window", [
            "aShopGroups" => ShopGroup::where("parent_id", $request->shop_group_id ?? 0)->where("active", 1)->get(),
            "aShopItems" => ShopItem::where("shop_group_id", $request->shop_group_id ?? 0)->where("modification_id", 0)->where("active", 1)->get(),
            "aShopItem" => ShopItem::find($shopItem->id),

            "ShopItemAssociatedGroups" => $ShopItemAssociatedGroups,
            "ShopItemAssociatedItems" => $this->shopItemAssociatedItems($shopItem)
        ]);
    }

    public function shopItemAssociatedItems(shopItem $shopItem)
    {
        $ShopItemAssociatedItems = [];
        foreach (ShopItemAssociatedItem::select("shop_item_associated_id")->where("shop_item_id", $shopItem->id)->get() as $ShopItemAssociatedItem) {
            $ShopItemAssociatedItems[] = $ShopItemAssociatedItem->shop_item_associated_id;
        }

        return $ShopItemAssociatedItems;
    }

    public function saveAssociated(Request $request, shopItem $shopItem)
    {

        if (isset($request->associated_groups) && count($request->associated_groups) > 0) {
            foreach ($request->associated_groups as $group_id) {
                if (is_null(ShopItemAssociatedGroup::where("shop_item_id", $shopItem->id)->where("shop_group_associated_id", $group_id)->first())) {
                    $ShopItemAssociatedGroup = new ShopItemAssociatedGroup();
                    $ShopItemAssociatedGroup->shop_item_id = $shopItem->id;
                    $ShopItemAssociatedGroup->shop_group_associated_id = $group_id;
                    $ShopItemAssociatedGroup->save();
                }
            }
        }

        if(isset($request->associated_items) && count($request->associated_items) > 0) {
            foreach ($request->associated_items as $item_id) {
                if (is_null(ShopItemAssociatedItem::where("shop_item_id", $shopItem->id)->where("shop_item_associated_id", $item_id)->first())) {
                    $ShopItemAssociatedItem = new ShopItemAssociatedItem();
                    $ShopItemAssociatedItem->shop_item_id = $shopItem->id;
                    $ShopItemAssociatedItem->shop_item_associated_id  = $item_id;
                    $ShopItemAssociatedItem->save();
                }
            }
        }

        return response()->view("admin.shop.item.associated.list", ["shopItem" => $shopItem]);
    }

    public function deleteShopItemAssociatedGroup(Request $request, shopItem $shopItem, shopItemAssociatedGroup $shopItemAssociatedGroup)
    {

        $shopItemAssociatedGroup->delete();

        return response()->view("admin.shop.item.associated.list", ["shopItem" => $shopItem]);
    }

    public function deleteShopItemAssociatedItem(Request $request, shopItem $shopItem, shopItemAssociatedItem $shopItemAssociatedItem)
    {

       $shopItemAssociatedItem->delete();

        return response()->view("admin.shop.item.associated.list", ["shopItem" => $shopItem]);
    }

    public function searchShopItemFromAssosiated(Request $request, shopItem $shopItem) {

        $ShopItems = [];

        if (!empty($term = $request->input('term'))) {
            $ShopItems = ShopItem::select("shop_items.*")
                ->where("shop_items.modification_id", 0)
                ->where(function($query) use ($term) {
                    $query
                        ->where("shop_items.name", "LIKE", "%" . $term. "%")
                        ->orWhere("shop_items.marking", "LIKE", "%" . $term . "%");

                })
                ->groupBy("shop_items.id")
                ->get();
        }

        return response()->view("admin.shop.item.associated.search", [
            "ShopItems" => $ShopItems,
            "aShopItem" => $shopItem,
            "ShopItemAssociatedItems" => $this->shopItemAssociatedItems($shopItem)
        ]);
    }

}
