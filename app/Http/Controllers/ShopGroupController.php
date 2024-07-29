<?php

namespace App\Http\Controllers;

use App\Models\ShopGroup;
use App\Models\ShopItem;
use App\Models\Shop;
use App\Http\Controllers\Admin\ShopItemController;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Services\Helpers\Str;
use App\Models\ShopItemShortcut;


class ShopGroupController extends Controller
{

    static public function prepareItems($shopGroup)
    {

        $aGroups = ShopGroupController::ArrayMerge(ShopGroupController::getChildGroups($shopGroup->id));
        if (!$aGroups) {
            $aGroups = [$shopGroup->id];
        }

        $aProperties = self::getProperties();

        $shopItems = ShopItem::select("shop_items.id")->where("shop_items.active", 1)->where("shop_items.deleted", 0)->where("shop_items.hidden", 0)->whereIn("shop_items.shop_group_id", $aGroups);
        $ShopItemShortcuts = ShopItemShortcut::select("shop_item_shortcuts.shop_item_id")->join("shop_items", "shop_items.id", "=", "shop_item_shortcuts.shop_item_id")->where("shop_items.active", 1)->where("shop_items.hidden", 0)->whereIn("shop_item_shortcuts.shop_group_id", $aGroups);

        $Modifications = ShopItem::select("shop_items.modification_id")->distinct()->where(function($query) use ($shopItems, $ShopItemShortcuts) {
                $query->whereIn("shop_items.modification_id", $shopItems)
                      ->orWhereIn("shop_items.modification_id", $ShopItemShortcuts);
        });

        if (count($aProperties) > 0) {
            $Modifications
                ->join('property_value_ints', 'property_value_ints.entity_id', '=', 'shop_items.id')
                ->where(function($query) use ($aProperties) {

                    foreach ($aProperties as $k => $aProperty) {
                        $query->orWhere(function($query) use ($k, $aProperty) {
                            $query->where("property_value_ints.property_id", $k)
                                  ->whereIn("property_value_ints.value", $aProperty);
                        });
                    }
                })
                ->havingRaw('COUNT(property_value_ints.property_id) = ' . count($aProperties));

            $Modifications->groupBy('shop_items.id');

        }

        $aShopItems = ShopItem::select('shop_items.*')->whereIn("shop_items.id", $Modifications)->distinct();

        $sorting = Arr::get($_REQUEST, 'sorting', 0);

        switch ($sorting) {
            case 'old':
                $aShopItems->orderBy('created_at', 'ASC');
                $sorting = 'old';
            break;
            default:
                $aShopItems->orderBy('created_at', 'DESC');
                $sorting = 'new';
            break;
        }

        return $aShopItems;
    }

    public static function getAjaxGroup($shop_group_id) {

        $oShop = Shop::get();

        $ShopGroup = ShopGroup::find($shop_group_id);

        $oShopItems = self::prepareItems($ShopGroup);

        return view('shop.ajax-group', [
            "items" => $oShopItems->paginate($oShop->items_on_page),
        ]);

    }

    static public function show($shopGroup, $ShopFilter = false)
    {

        $oShop = Shop::get();

        $sorting = Arr::get($_REQUEST, 'sorting', 0);

        switch ($sorting) {
            case 'old':
                $sorting = 'old';
            break;
            default:
                $sorting = 'new';
            break;
        }

        $aProperties = self::getProperties($ShopFilter);

        $oShopItems = self::prepareItems($shopGroup, $ShopFilter);
        
        return view('shop/group', [
            'shop' => $oShop,
            'group' => $shopGroup,
            'SubGroups' => ShopGroup::where("parent_id", $shopGroup->id)->where("active", 1)->where("deleted", 0)->get(),
            'items' => $oShopItems->paginate($oShop->items_on_page),
            'properties' => ShopItemController::getProperties($shopGroup->id, 4, true),
            'path' => $shopGroup->url,
            'filterProperties' => $aProperties,
            'sorting' => $sorting,
            'breadcrumbs' => BreadcrumbsController::breadcrumbs(self::breadcrumbs($shopGroup, [], false)),
            'shopFilter' => $ShopFilter
        ]);
        
        
    }

    public static function getProperties($ShopFilter = false)
    {
        $aProperties = [];

        if (!$ShopFilter) {
            foreach ($_REQUEST as $k => $value) {
                if (substr($k, 0, 8) == 'property') {
                    $e = explode("_", $k);
                    $aProperties[$e[1]][] = $e[2];
                }
            }
        } else {

            foreach ($ShopFilter->ShopFilterPropertyValues as $ShopFilterPropertyValue) {
                $aProperties[$ShopFilterPropertyValue->property_id][] = $ShopFilterPropertyValue->value;
            }
        }

        
        
        return $aProperties;
    }

    public static function getChildGroups($group_id)
    {

        $result = [];
        $select = ShopGroup::where("parent_id", $group_id)->where('active', 1)->where('deleted', 0)->get();
        if (count($select) > 0) {
            foreach ($select as $oShopGroup) {
                $count = count($result);
                $result[$count]["id"] = $oShopGroup->id;
                $result[$count]["sub"] = self::getChildGroups($oShopGroup->id);
            }
            return $result;
        } else {
            return false;
        }
    }

    public static function ArrayMerge($array){
        $arrOut = array();
        if (is_array($array)) {
            foreach($array as $key => $val){
                if(is_numeric($val)){
                     $arrOut[] = $val;
                }
                else{
                     $res = self::ArrayMerge($val);
                     foreach($res as $k => $v){
                         $arrOut[] = $v;
                     } 
                }
            }
        }

        return $arrOut;
    }

    public static function breadcrumbs($shopGroup, $aResult = [], $firstElemIsActive = true)
    {
        $Result["name"] = $shopGroup->name;

        if ($firstElemIsActive || count($aResult) > 0) {
            $Result["url"] = $shopGroup->url;
        }
        
        array_unshift($aResult, $Result);

        if ($shopGroup->parent_id > 0) {
            return self::breadcrumbs(ShopGroup::find($shopGroup->parent_id), $aResult);
        } else {

            return $aResult;
        }
    }

    public static function showTreeGroupsAsOptions($current = 0)
    {

        echo view("admin.shop.group-options", [
            "groups" => ShopGroup::getGroupTree(),
            "current" => $current
        ]);
    }

    public function filter(Request $request)
    {

        $oShopItems = self::prepareItems(ShopGroup::find($request->shop_group_id));

        $count = $oShopItems->count();

        return response()->json(["button" => "Показать $count " . Str::plural($count, "товар", "товара", "товаров")]);
    }

}