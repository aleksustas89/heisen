<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ShopGroup;
use App\Models\ShopItem;
use App\Models\ShopItemImage;
use App\Models\ShopModificationImage;
use App\Models\ShopItemList;
use App\Models\ShopItemListItem;
use App\Models\ShopItemProperty;
use App\Models\ShopItemPropertyForGroup;
use App\Models\PropertyValueInt;
use App\Models\PropertyValueString;
use App\Models\PropertyValueFloat;


class MigrateController extends Controller
{
    public function groups()
    {

        $groups = DB::select('select * from shop_groups2');

        foreach ($groups as $group) {

            $ShopGroup = new ShopGroup();
            $ShopGroup->id = $group->id;
            $ShopGroup->name = $group->name;
            $ShopGroup->description = $group->description;
            $ShopGroup->active = $group->active;
            $ShopGroup->parent_id = $group->parent_id;
            $ShopGroup->sorting = $group->sorting;
            $ShopGroup->path = $group->path;

            $ShopGroup->image_large = $group->image_large;
            $ShopGroup->image_small = $group->image_small;

            $ShopGroup->seo_title = $group->seo_title;
            $ShopGroup->seo_description = $group->seo_description;
            $ShopGroup->seo_keywords = $group->seo_keywords;

            $ShopGroup->save();

           // echo $group->name . "<br>";
        }

    }

    public function items()
    {
        $items = DB::select('select * from shop_items2');

        foreach ($items as $item) {
            $ShopItem = new ShopItem();
            $ShopItem->id = $item->id;
            $ShopItem->shop_group_id = $item->shop_group_id;
            $ShopItem->shop_currency_id = $item->shop_currency_id;
            $ShopItem->name = $item->name;
            $ShopItem->marking = $item->marking;
            $ShopItem->description = $item->description;
            $ShopItem->text = $item->text;
            //$ShopItem->image_large = $item->image_large;
            //$ShopItem->image_small = $item->image_small;
            $ShopItem->price = $item->price;
            $ShopItem->active = $item->active;
            $ShopItem->sorting = $item->sorting;
            $ShopItem->path = $item->path;

            $ShopItem->seo_title = $item->seo_title;
            $ShopItem->seo_description = $item->seo_description;
            $ShopItem->seo_keywords = $item->seo_keywords;
            $ShopItem->indexing = $item->indexing;
            $ShopItem->guid = $item->guid;
            $ShopItem->modification_id = $item->modification_id;

            $ShopItem->save();

            $ShopItemImage = new ShopItemImage();
            $ShopItemImage->shop_item_id = $ShopItem->id;
            $ShopItemImage->image_large = $item->image_large;
            $ShopItemImage->image_small = $item->image_small;
            $ShopItemImage->save();

        }
    }

    public function groupImages()
    {

        $dir = "C:\OSPanel\domains\heisen\public";

        foreach (ShopGroup::get() as $ShopGroup) {

            $url = "/shop_3/" . mb_substr($ShopGroup->id,0,1) . "/" . mb_substr($ShopGroup->id,1,1) . "/" . mb_substr($ShopGroup->id,2,1) ."/group_" . $ShopGroup->id . "/" . $ShopGroup->image_large;
        
            if (file_exists($dir . $url)) {

                if(!file_exists(($dir . "/shop/group_" . $ShopGroup->id))) {
                    mkdir($dir . "/shop/group_" . $ShopGroup->id, 0777, true);
                }

                $newfile = $dir . "/shop/group_" . $ShopGroup->id . "/" . $ShopGroup->image_large;

                if (!file_exists($newfile)) {
                    copy($dir . $url, $newfile);
                }
            }

            $url = "/shop_3/" . mb_substr($ShopGroup->id,0,1) . "/" . mb_substr($ShopGroup->id,1,1) . "/" . mb_substr($ShopGroup->id,2,1) ."/group_" . $ShopGroup->id . "/" . $ShopGroup->image_small;
        
            if (file_exists($dir . $url)) {

                if(!file_exists(($dir . "/shop/group_" . $ShopGroup->id))) {
                    mkdir($dir . "/shop/group_" . $ShopGroup->id, 0777, true);
                }
                
                $newfile = $dir . "/shop/group_" . $ShopGroup->id . "/" . $ShopGroup->image_small;

                copy($dir . $url, $newfile);
            }
        }
    }

    public function itemImages()
    {

        $dir = "C:\OSPanel\domains\heisen\public";

        foreach (ShopItem::where("modification_id", 0)->get() as $ShopItem) {

            $first = ShopItemImage::where("shop_item_id", $ShopItem->id)->first();

            $shop_group_id = $ShopItem->shop_group_id;

            if (!is_null($first)) {

                $url = "/shop_3/" . mb_substr($ShopItem->id,0,1) . "/" . mb_substr($ShopItem->id,1,1) . "/" . mb_substr($ShopItem->id,2,1) ."/item_" . $ShopItem->id . "/" . $first->image_large;
                

                if (file_exists($dir . $url)) {
    
                    if(!file_exists(($dir . "/shop/group_" . $shop_group_id))) {
                        mkdir($dir . "/shop/group_" . $shop_group_id, 0777, true);
                    }
    
                    if(!file_exists(($dir . "/shop/group_" . $shop_group_id . "/item_" . $ShopItem->id))) {
                        mkdir($dir . "/shop/group_" . $shop_group_id . "/item_" . $ShopItem->id, 0777, true);
                    }
    
                    $newfile = $dir . "/shop/group_" . $shop_group_id . "/item_" . $ShopItem->id ."/" . $first->image_large;


                    if (!file_exists($newfile)) {
                        copy($dir . $url, $newfile);
                    }
    
                    $newfile = $dir . "/shop/group_" . $shop_group_id . "/item_" . $ShopItem->id ."/" . $first->image_small;
    
                    if (!file_exists($newfile)) {
                        copy($dir . $url, $newfile);
                    }
                    
                }

            }
            
        }
    }

    public function modImages()
    {

        foreach (ShopItem::where("modification_id", ">", 0)->get() as $ShopItem) {

            $oParentItem = ShopItem::find($ShopItem->modification_id);
            if (!is_null($oParentItem)) {
                $first = ShopItemImage::where("shop_item_id", $oParentItem->id)->first();

                if (!is_null($first)) {
                    $ShopModificationImage = new ShopModificationImage();
                    $ShopModificationImage->shop_item_id = $ShopItem->id;
                    $ShopModificationImage->shop_item_image_id = $first->id;
                    $ShopModificationImage->save();
                }
    
            }
        }
    }

    public function additionalImages()
    {
        $dir = "C:\OSPanel\domains\heisen\public";

        foreach (ShopItem::where("done", 0)->where("modification_id", "=", 0)->limit(500)->get() as $ShopItem) {

            $images = DB::select('select * from property_value_files2 where entity_id =' . $ShopItem->id);

            foreach ($images as $image) {

                $ShopItemImage = new ShopItemImage();
                $ShopItemImage->shop_item_id = $ShopItem->id;
                
                $r = false;

                $url = "/shop_3/" . mb_substr($ShopItem->id,0,1) . "/" . mb_substr($ShopItem->id,1,1) . "/" . mb_substr($ShopItem->id,2,1) ."/item_" . $ShopItem->id . "/" . $image->file;
                
                if (file_exists($dir . $url)) {

                    $newfile = $dir . "/shop/group_" . $ShopItem->shop_group_id . "/item_" . $ShopItem->id ."/" . $image->file;


                    if (!file_exists($newfile)) {
                        copy($dir . $url, $newfile);

                        $ShopItemImage->image_large = $image->file;
                        $r = true;
                    }

                }


                $url = "/shop_3/" . mb_substr($ShopItem->id,0,1) . "/" . mb_substr($ShopItem->id,1,1) . "/" . mb_substr($ShopItem->id,2,1) ."/item_" . $ShopItem->id . "/" . $image->file_small;
                
                if (file_exists($dir . $url)) {

                    $newfile = $dir . "/shop/group_" . $ShopItem->shop_group_id . "/item_" . $ShopItem->id ."/" . $image->file_small;


                    if (!file_exists($newfile)) {
                        copy($dir . $url, $newfile);
                        $ShopItemImage->image_small = $image->file_small;
                        $r = true;
                    }

                }

                if($r) {
                    $ShopItemImage->save();
                }

            }

            $ShopItem->done = 1;
            $ShopItem->save();
            
        } 
    }

    public function listItems()
    {
        foreach (ShopItemList::get() as $ShopItemList) {
            $list_items = DB::select('select * from list_items where list_id=' . $ShopItemList->id);

            foreach ($list_items as $list_item) {
                $ShopItemListItem = new ShopItemListItem();
                $ShopItemListItem->id = $list_item->id;
                $ShopItemListItem->shop_item_list_id = $ShopItemList->id;
                $ShopItemListItem->value = $list_item->value;
                $ShopItemListItem->description = $list_item->description;
                $ShopItemListItem->active = $list_item->active;
                $ShopItemListItem->color = $list_item->color;
                $ShopItemListItem->sorting = $list_item->sorting;

                $ShopItemListItem->save();
            }
        }
    }

    public function properties()
    {
        
        $items = DB::select('select * from shop_item_properties2 where shop_id=3');

        foreach ($items as $item) {

            $property = DB::select('select * from properties where id=' . $item->property_id);

            if (!is_null($property)) {

                $type = -1;

                switch ($property[0]->type) {
                    case 0:
                        $type = 1;
                    break;
                    case 11:
                        $type = 2;
                    break;
                    case 1:
                        $type = 0;
                    break;
                    case 7:
                        $type = 3;
                    break;
                    case 3:
                        $type = 4;
                    break;
                }

                if ($type >= 0) {
                    $ShopItemProperty = new ShopItemProperty();
                    $ShopItemProperty->id = $property[0]->id;
                    $ShopItemProperty->name = $property[0]->name;
                    $ShopItemProperty->type = $type;
                    $ShopItemProperty->multiple = $property[0]->multiple;
                    $ShopItemProperty->shop_item_list_id  = $property[0]->list_id;
                    $ShopItemProperty->sorting = $property[0]->sorting;
                    $ShopItemProperty->show_in_item = $item->show_in_item;
                    $ShopItemProperty->save();
                }
            }
        }
    }

    public function propertiesForGroups()
    {
        
        foreach (DB::select('select * from shop_item_property_for_groups2 where shop_id=3') as $shop_item_property_for_group) {

            $property = DB::select('select * from shop_item_properties2 where id=' . $shop_item_property_for_group->shop_item_property_id);

            if (!is_null($property)) {

                $shopItemPropertyForGroup = new ShopItemPropertyForGroup();
                $shopItemPropertyForGroup->shop_group_id = $shop_item_property_for_group->shop_group_id;
                $shopItemPropertyForGroup->shop_item_property_id = $property[0]->property_id;
                $shopItemPropertyForGroup->save();
            }
        }
    }

    public function propertyValueInts()
    {
        foreach (ShopItemProperty::whereIn("type", [1,3,4])->get() as $ShopItemProperty) {

            foreach (DB::select('select * from property_value_ints2 where property_id=' . $ShopItemProperty->id) as $value) {
                $PropertyValueInt = new PropertyValueInt();
                $PropertyValueInt->property_id = $ShopItemProperty->id;
                $PropertyValueInt->entity_id  = $value->entity_id;
                $PropertyValueInt->value = $value->value;
                $PropertyValueInt->save();
            }
        }
    }

    public function propertyValueStrings()
    {
        foreach (ShopItemProperty::whereIn("type", [0])->get() as $ShopItemProperty) {

            foreach (DB::select('select * from property_value_strings2 where property_id=' . $ShopItemProperty->id) as $value) {
                $PropertyValueString = new PropertyValueString();
                $PropertyValueString->property_id = $ShopItemProperty->id;
                $PropertyValueString->entity_id  = $value->entity_id;
                $PropertyValueString->value = $value->value;
                $PropertyValueString->save();
            }
        }
    }

    public function propertyValueFloat()
    {
        foreach (ShopItemProperty::whereIn("type", [2])->get() as $ShopItemProperty) {

            foreach (DB::select('select * from property_value_floats2 where property_id=' . $ShopItemProperty->id) as $value) {
                $PropertyValueFloat = new PropertyValueFloat();
                $PropertyValueFloat->property_id = $ShopItemProperty->id;
                $PropertyValueFloat->entity_id  = $value->entity_id;
                $PropertyValueFloat->value = $value->value;
                $PropertyValueFloat->save();
            }
        }
    }

    public function modNames()
    {
        foreach (ShopItem::where("modification_id", ">", 0)->get() as $ShopItem) { 
            $e = explode(", цвет:", $ShopItem->name);
         
            if (count($e) > 1 && isset($e[1])) {

                if (!is_null($oParentItem = ShopItem::find($ShopItem->modification_id))) {
                    $name = trim($oParentItem->name) .", цвет: " . $e[1];

                    $ShopItem->name = $name;
                    $ShopItem->save();

                    //echo $name . "[" . $ShopItem->name . "]<br>";
                }

                // $name = trim($e[0]);

                // Цвет фурнитуры/Цвет фурнитуры/Цвет фурнитур
           
                //  $e2 = explode("/", $e[1]);
                // if (count($e2) > 1) {
                //     $name .=", цвет: " . $e2[0];
                // }
            }

            // $ShopItem->name = $name;
            // $ShopItem->save();
        }
    }

    public function itemNames()
    {
        foreach (ShopItem::get() as $ShopItem) { 
           
            $PropertyValueString = PropertyValueString::where("property_id", 124)->where("entity_id", $ShopItem->id)->first();
            if (!is_null($PropertyValueString)) {
                $ShopItem->name = $PropertyValueString->value;
                $ShopItem->save();
            }

            $PropertyValueString = PropertyValueString::where("property_id", 138)->where("entity_id", $ShopItem->id)->first();
            if (!is_null($PropertyValueString)) {
                $ShopItem->seo_title = $PropertyValueString->value;
                $ShopItem->save();
            }

            $PropertyValueString = PropertyValueString::where("property_id", 140)->where("entity_id", $ShopItem->id)->first();
            if (!is_null($PropertyValueString)) {
                $ShopItem->seo_description = $PropertyValueString->value;
                $ShopItem->save();
            }

            $select = DB::select('select * from property_value_texts2 where property_id=128 and entity_id=' . $ShopItem->id);

            if (!is_null($select) && isset($select[0])) {
                $ShopItem->description = $select[0]->value;
                $ShopItem->save();
            }
        }
    }

    public function groupNames()
    {
        foreach (ShopGroup::get() as $ShopGroup) { 

            $select = DB::select('select * from property_value_strings2 where property_id=126 and entity_id=' . $ShopGroup->id);

            if (!is_null($select)) {
                $ShopGroup->name = $select[0]->value;
                $ShopGroup->save();
            }

            $select = DB::select('select * from property_value_texts2 where property_id=132 and entity_id=' . $ShopGroup->id);

            if (!is_null($select) && isset($select[0])) {
                $ShopGroup->description = $select[0]->value;
                $ShopGroup->save();
            }

            $select = DB::select('select * from property_value_strings2 where property_id=134 and entity_id=' . $ShopGroup->id);

            if (!is_null($select) && isset($select[0])) {
                $ShopGroup->seo_title = $select[0]->value;
                $ShopGroup->save();
            }

            $select = DB::select('select * from property_value_strings2 where property_id=136 and entity_id=' . $ShopGroup->id);

            if (!is_null($select) && isset($select[0])) {
                $ShopGroup->seo_description = $select[0]->value;
                $ShopGroup->save();
            }
        }
    }

    public function normalizeValues()
    {
        foreach (ShopItem::get() as $ShopItem) { 
           
            $PropertyValueString = PropertyValueString::where("property_id", 87)->where("entity_id", $ShopItem->id)->first();
            if (!is_null($PropertyValueString)) {
               $e = explode("/", $PropertyValueString->value);
               if (count($e)> 1) {
                $PropertyValueString->value = $e[0];
                $PropertyValueString->save();
               }
            }

            $PropertyValueString = PropertyValueString::where("property_id", 84)->where("entity_id", $ShopItem->id)->first();
            if (!is_null($PropertyValueString)) {
               $e = explode("/", $PropertyValueString->value);
               if (count($e)> 1) {
                $PropertyValueString->value = $e[0];
                $PropertyValueString->save();
               }
            }

            $PropertyValueString = PropertyValueString::where("property_id", 85)->where("entity_id", $ShopItem->id)->first();
            if (!is_null($PropertyValueString)) {
               $e = explode("/", $PropertyValueString->value);
               if (count($e)> 1) {
                $PropertyValueString->value = $e[0];
                $PropertyValueString->save();
               }
            }

            $PropertyValueString = PropertyValueString::where("property_id", 86)->where("entity_id", $ShopItem->id)->first();
            if (!is_null($PropertyValueString)) {
               $e = explode("/", $PropertyValueString->value);
               if (count($e)> 1) {
                $PropertyValueString->value = $e[0];
                $PropertyValueString->save();
               }
            }
        }
    }

    public function currensies()
    {
        foreach (ShopItem::where("price", ">", 0)->get() as $ShopItem) { 

            if ($ShopItem->shop_currency_id == 4) {

                $ShopItem->price = $ShopItem->price * 2.70;
                $ShopItem->shop_currency_id = 1;
                $ShopItem->save();
            } else {
                echo $ShopItem->id .":". $ShopItem->price . "<br>";
            }
            

        }
    }
}