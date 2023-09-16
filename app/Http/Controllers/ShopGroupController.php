<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Models\ShopGroup;
use App\Models\ShopItem;
use App\Models\Shop;
use App\Http\Controllers\Admin\ShopItemController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;


class ShopGroupController extends Controller
{

    static public function show($path, $shopGroup)
    {

        $oShop = Shop::get();

        if($groups = self::getChildGroups($shopGroup->id)) {
            $groups = self::ArrayMerge($groups);
        }  else {
            $groups = [$shopGroup->id];
        }
        $aProperties = [];
        foreach ($_REQUEST as $k => $value) {
            if (substr($k, 0, 8) == 'property') {
                $e = explode("_", $k);
                $aProperties[$e[1]][] = $e[2];
            }
        }

        $aShopItems = DB::table('shop_items')->select('shop_items.id')->whereIn("shop_items.shop_group_id", $groups)->where("active", 1);

        if (count($aProperties) > 0) {
            $aShopItems
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
        }

        $aShopItems->groupBy('shop_items.id');

        $ShopItems = $aShopItems->get();

        $shop_item_id = [];
        foreach ($ShopItems as $ShopItems) {
            $shop_item_id[] = $ShopItems->id; 
        }

        $oShopItems = ShopItem::whereIn("id", $shop_item_id);

        $sorting = Arr::get($_REQUEST, 'sorting', 0);

        switch ($sorting) {
            case 'old':
                $oShopItems->orderBy('created_at', 'ASC');
                $sorting = 'old';
            break;
            default:
                $oShopItems->orderBy('created_at', 'DESC');
                $sorting = 'new';
            break;
        }
        
        $oShopItems = $oShopItems->paginate($oShop->items_on_page);
        
        Route::view($path, 'shop/group', [
            'group' => $shopGroup,
            'menuGroups' => self::getChildGroups(0),
            'items' => $oShopItems,
            'properties' => ShopItemController::getProperties($shopGroup->id),
            'path' => "/" . $path,
            'filterProperties' => $aProperties,
            'sorting' => $sorting,
            'breadcrumbs' => BreadcrumbsController::breadcrumbs(self::breadcrumbs($shopGroup, [], false))
        ]);
        
    }


    public static function getGroupsTree($parent_id = 0, $aResult = [], $levels = 0)
    {

        foreach (ShopGroup::where("parent_id", $parent_id)->where('active', 1)->get() as $oShopGroup) {
            $aResult[] = $oShopGroup;
        }

        return $aResult;
    }

    public static function getChildGroups($group_id)
    {

        $result = [];
        $select = ShopGroup::where("parent_id", $group_id)->where('active', 1)->get();
        if (count($select) > 0) {
            foreach ($select as $oShopGroup) {
                $count = count($result);
                $result[$count]["id"] = $oShopGroup->id;
                $result[$count]["name"] = $oShopGroup->name;
                $result[$count]["parent_id"] = $oShopGroup->parent_id;
                $result[$count]["path"] = $oShopGroup->url();
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
            $Result["url"] = $shopGroup->url();
        }
        
        array_unshift($aResult, $Result);

        if ($shopGroup->parent_id > 0) {
            return self::breadcrumbs(ShopGroup::find($shopGroup->parent_id), $aResult);
        } else {

            return $aResult;
        }
    }

}