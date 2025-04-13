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
use App\Models\Page;
use App\Models\ShopItemShortcut;
use App\Http\Controllers\Admin\ModificationController;


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
            "BadgeClasses" => \App\Models\ShopItemShortcut::$BadgeClasses
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
            "mShopItems" => ShopItem::where("modification_id", $shopItem->id)->where("deleted", 0)->get(),
            "canonicalName" => !is_null($canonicalShopItem) ? $canonicalShopItem->name ." [". $canonicalShopItem->id ."] / ". $canonicalShopItem->ShopGroup->name : '',
            "BadgeClasses" => \App\Models\ShopItemShortcut::$BadgeClasses
        ]);
    }

    public static function getProperties($shop_group_id, $type = false, $filter = false)
    {
        $properties = ShopItemProperty::join('shop_item_property_for_groups', 'shop_item_properties.id', '=', 'shop_item_property_for_groups.shop_item_property_id')
                        ->select('shop_item_properties.*')
                        ->where("shop_item_properties.deleted", 0)
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
        foreach(ShopItemListItem::whereIn("shop_item_list_id", $lists)->where("deleted", 0)->get() as  $oShopItemList) {
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

        $shopItem->deleted = 1;
        $shopItem->save();

        return redirect()->back()->withSuccess("Товар был успешно удален!");
    }

    public function saveShopItem (Request $request, Shop $shop, $shopItem = false) 
    {

        $transfer_images_from = false;

        if (!$shopItem) {
            $shopItem = new ShopItem();
            $shopItem->save();

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

        $path = trim($request->path);


        $shopItem->name = trim($request->name);
        $shopItem->description = $request->description ?? '';
        $shopItem->text = $request->text ?? '';
        $shopItem->active = $request->active ?? 0;
        $shopItem->indexing = $request->indexing ?? 0;
        $shopItem->shop_group_id = $request->shop_group_id ?? 0;
        $shopItem->sorting = $request->sorting ?? 0;
        $shopItem->marking = $request->marking ?? Str::transliteration($request->name) ."-". time();
        $shopItem->weight = $request->weight;
        $shopItem->width = $request->width;
        $shopItem->height = $request->height;
        $shopItem->length = $request->length;
        $shopItem->path = !empty($path) ? $path : Str::transliteration($request->name . (!empty($request->marking) ? "-" . mb_strtolower(str_replace("_", "-", $request->marking)) : ''));
        $shopItem->seo_title = $request->seo_title ?? '';
        $shopItem->seo_description = $request->seo_description ?? '';
        $shopItem->seo_keywords = $request->seo_keywords ?? '';
        $shopItem->price = $request->price ?? 0;
        $shopItem->shop_currency_id = $request->shop_currency_id ?? 0;
        $shopItem->canonical = $request->canonical ?? 0;
        $shopItem->updated_at = date("Y-m-d H:i:s");
        $shopItem->link = $request->link;
        $shopItem->type = $request->type ?? 0;

        $shopItem->url = (!is_null($ShopGroup = $shopItem->ShopGroup) ? $ShopGroup->url . "/" : "") . $shopItem->path;

        if (!is_null(ShopItem::where("url", $shopItem->url)->whereNot("id", $shopItem->id)->first())) {
            return redirect()->to(route("shop.shop-item.edit", ["shop" => $shop->id, "shop_item" => $shopItem]))->withError("Товар с таким url уже существует - измените название или артикул");
        } 

        $shopItem->save();

        if ($transfer_images_from) {
            $shopItem->changeImagesDirFrom($transfer_images_from);
        }

        $this->saveItemProperties($request, $shopItem);

        $ModificationController = new ModificationController();
        
        foreach (ShopItem::where("modification_id", $shopItem->id)->get() as $mShopItem) {

            if ($request->apply_price_to_modifications) {
                $mShopItem->price = $shopItem->price;
            }

            $ModificationController->saveStaticModificaitonFields($mShopItem, $shopItem);

        }
        
        //скидка
        $ShopItemDiscountController = new ShopItemDiscountController();
        foreach ($shopItem->ShopItemDiscounts as $ShopItemDiscount) {
            $ShopItemDiscountController->saveShopItemDiscount($ShopItemDiscount, $ShopItemDiscount->ShopDiscount, $shopItem);
        }

        if ($request->shortcut_groups && count($request->shortcut_groups) > 0) {
            foreach ($request->shortcut_groups as $shortcut_group_id) {
                if (is_null($shopItemShortcut = ShopItemShortcut::where("shop_item_id", $shopItem->id)->where("shop_group_id", $shortcut_group_id)->first())) {
                    $shopItemShortcut = new ShopItemShortcut();
                    $shopItemShortcut->shop_item_id = $shopItem->id;
                    $shopItemShortcut->shop_group_id = $shortcut_group_id;
                    $shopItemShortcut->save();
                }
            }
        }

        $SearchController = new SearchController();
        $SearchController->indexingShopItem($shopItem, true);

        if (!is_null($shopGroup = $shopItem->shopGroup)) {
            $shopGroup->setSubCount();
        }
        
        $message = "Товар был успешно сохранен!";

        if ($request->apply) {
            return redirect()->to(route("shop.index", ["shop" => $shop->id]) . ($shopItem->shop_group_id > 0 ? '&parent_id=' . $shopItem->shop_group_id : ''))->withSuccess($message);
        } else {
            return redirect()->to(route("shop.shop-item.edit", ["shop" => $shop->id, "shop_item" => $shopItem]))->withSuccess($message);
        }
    }

    public function searchCanonical(Request $request, ShopItem $shopItem)
    {
        $aResult = [];

        if (!empty($term = $request->input('term'))) {

            $ShopItems = ShopItem::where('modification_id', 0)
                ->where("deleted", 0)
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

    public function deletePropertyValue(Request $request, ShopItemProperty $shopItemProperty)
    {
        $response = false;

        if ($request->id) {
            $oProperty_Value = ShopItemProperty::getObjectByType($shopItemProperty->type); 
            $oValue = $oProperty_Value->find($request->id);
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

    public function getShopItemGallery(ShopItem $shopItem)
    {
        return response()->view('admin.shop.item.gallery', [
            'images' => $shopItem->getImages(),
            'shopItem' => $shopItem,
        ]);
    }

    public function uploadShopItemImage(ShopItem $shopItem, Request $request)
    {
        $shopItem->createDir();
    
        $oShop = Shop::get();
    
        $allowedImageExt = ['jpg', 'png', 'jpeg', 'webp']; // Добавлены видеоформаты
    
        $extension = $request->file->getClientOriginalExtension();
        $getClientOriginalName = $request->file->getClientOriginalName();
    
        // Сохраняем оригинал
        if ($request->file->storeAs($shopItem->path(), $getClientOriginalName)) {
    
            logger()->info('Сохранение файла', [
                'path' => $shopItem->path(),
                'filename' => $getClientOriginalName
            ]);
    
            $fileInfo = File::fileInfoFromStr($getClientOriginalName);
            $path = $shopItem->path() . $getClientOriginalName;
    
            $oShopItemImage = new ShopItemImage();
            $oShopItemImage->shop_item_id = $shopItem->id;
            $oShopItemImage->save();
    
            if (in_array($extension, $allowedImageExt)) { 
                // Обработка изображений
                if (in_array($extension, ['jpg', 'png', 'jpeg', 'webp'])) { // Только для изображений
                    foreach (["large", "small"] as $format) {
                        $Image = Image::make(Storage::path($path));
    
                        $image_x_max_width = 'image_' . $format . '_max_width';
                        $image_x_max_height = 'image_' . $format . '_max_height';
    
                        if ($format == 'large' ? $oShop->preserve_aspect_ratio == 1 : $oShop->preserve_aspect_ratio_small == 1) {
                            $Image->resize($oShop->$image_x_max_width, $oShop->$image_x_max_height, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                        } else {
                            $Image->fit($oShop->$image_x_max_width, $oShop->$image_x_max_height);
                        }
    
                        $sName = 'image_' . $format . $oShopItemImage->id . '.' . $fileInfo["extension"];
    
                        $Image->save(Storage::path($shopItem->path()) . $sName);
    
                        if ($oShop->convert_webp == 1) {
                            File::webpConvert(Storage::path($shopItem->path()), $sName);
    
                            $sName = 'image_' . $format . $oShopItemImage->id . '.webp';
                        }
    
                        $format == 'large' ? $oShopItemImage->image_large = $sName : $oShopItemImage->image_small = $sName;
                    }
                }
            } else {
                // Обработка видео или других файлов
                $oShopItemImage->file = $getClientOriginalName;
            }
    
            $oShopItemImage->save();
    
            // Удаление оригинала (только если это не видео)
            if (!in_array($extension, ['mp4', 'webm', 'avi'])) {
                Storage::delete($path);
            }
    
            return response()->json(['message' => 'Файл успешно загружен', 'file' => $getClientOriginalName]);
        }
    
        return response()->json(['error' => 'Не удалось загрузить файл'], 400);
    }

    // public function uploadShopItemImage(ShopItem $shopItem, Request $request)
    // {
        
    //     $allowedImageExt = ['jpg', 'png', 'jpeg', 'webp'];

    //     $extension = $request->file->getClientOriginalExtension();

    //     if (!is_null($Informationsystem = $informationsystemItem->Informationsystem)) {

    //         $informationsystemItem->createDir();

    //         $storePath = "/" . $informationsystemItem->Informationsystem->path() .  $informationsystemItem->path();

    //         $getClientOriginalName = $request->file->getClientOriginalName();
            
    //         if ($request->file->storeAs($storePath, $getClientOriginalName)) {
    //             $fileInfo = File::fileInfoFromStr($getClientOriginalName);

    //             $path = $storePath . $getClientOriginalName;

    //             $oInformationsystemItemFile = new InformationsystemItemFile();
    //             $oInformationsystemItemFile->informationsystem_item_id = $informationsystemItem->id;
    //             $oInformationsystemItemFile->save();
                
    //             if (in_array($extension, $allowedImageExt)) {

    //                 foreach (["large", "small"] as $format) {
            
    //                     $Image = Image::make(Storage::path($path));
            
    //                     $image_x_max_width  = 'image_' . $format . '_max_width';
    //                     $image_x_max_height = 'image_' . $format . '_max_height';
            
    //                     if ($format == 'large' ? $Informationsystem->preserve_aspect_ratio == 1 : $Informationsystem->preserve_aspect_ratio_small == 1) {
    //                         $Image->resize($Informationsystem->$image_x_max_width, $Informationsystem->$image_x_max_height, function ($constraint) {
    //                             $constraint->aspectRatio();
    //                             $constraint->upsize();
    //                         });
    //                     } else {
    //                         $Image->fit($Informationsystem->$image_x_max_width, $Informationsystem->$image_x_max_height);
    //                     }
            
    //                     $sName = 'image_' . $format . $oInformationsystemItemFile->id .'.' . $fileInfo["extension"];
            
    //                     $Image->save(Storage::path($storePath) . $sName);
            
    //                     if ($Informationsystem->convert_webp == 1) {
                            
    //                         File::webpConvert(Storage::path($storePath), $sName);
            
    //                         $sName = 'image_' . $format . $oInformationsystemItemFile->id .'.webp';
    //                     }
            
    //                     $format == 'large' ? $oInformationsystemItemFile->image_large = $sName : $oInformationsystemItemFile->image_small = $sName;
            
    //                 }

    //                 Storage::delete($path);

    //             } else {

    //                 $oInformationsystemItemFile->file = $getClientOriginalName;
    //             }

    //             $oInformationsystemItemFile->save();
    //         }
    //     }
    // }

    
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

    public function getShortcutGroup(Request $request)
    {

        $aResult = [];

        if (!empty($term = $request->input('term'))) {

            foreach (ShopGroup::where("name", "LIKE", "%" . $term . "%")->get() as $ShopGroup) {
                if (!isset($request->shop_group_id) || ($ShopGroup->shop_group_id != $request->shop_group_id)) {

                    $value = $ShopGroup->name . " [" . $ShopGroup->id . "]";

                    if ($ShopGroup->parent_id > 0) {
                        if (!is_null($pShopGroup = ShopGroup::find($ShopGroup->parent_id))) {
                            $value = $pShopGroup->name . " / " . $value; 
                        }
                    }
                    
                    $aResult[] = ["value" => $value, "data" => $ShopGroup->id];
                }
            }
        }

        return response()->json($aResult);
    }

    public function deleteShortcutGroup(Request $request, shopItem $shopItem, shopGroup $shopGroup)
    {

        $aResult["responce"] = false;

        $deleted = ShopItemShortcut::where("shop_item_id", $shopItem->id)->where("shop_group_id", $shopGroup->id)->delete();

        if ($deleted > 0) {
            $aResult["responce"] = true;
        }

        return response()->json($aResult);
    }

}
