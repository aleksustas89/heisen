<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Models\ShopGroup;
use App\Models\ShopItem;
use App\Models\Shop;
use App\Http\Controllers\Admin\ShopItemController;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Services\Helpers\Str;


class ShopGroupController extends Controller
{

    static public function prepareItems($shopGroup)
    {

        if($groups = self::getChildGroups($shopGroup->id)) {
            $groups = self::ArrayMerge($groups);
        }  else {
            $groups = [$shopGroup->id];
        }
        $aProperties = self::getProperties();

        $aShopItems = ShopItem::select('shop_items.*')->whereIn("shop_items.shop_group_id", $groups)->where("active", 1);

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

            $aShopItems->groupBy('shop_items.id');

        }

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

    static public function show($path, $shopGroup)
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

        $aProperties = self::getProperties();

        $oShopItems = self::prepareItems($shopGroup);
        
        Route::view($path, 'shop/group', [
            'group' => $shopGroup,
            'SubGroups' => ShopGroup::where("parent_id", $shopGroup->id)->where("active", 1)->get(),
            'items' => $oShopItems->paginate($oShop->items_on_page),
            'properties' => ShopItemController::getProperties($shopGroup->id, 4, true),
            'path' => "/" . $path,
            'filterProperties' => $aProperties,
            'sorting' => $sorting,
            'breadcrumbs' => BreadcrumbsController::breadcrumbs(self::breadcrumbs($shopGroup, [], false))
        ]);
        
        
    }

    public static function getProperties()
    {
        $aProperties = [];
        foreach ($_REQUEST as $k => $value) {
            if (substr($k, 0, 8) == 'property') {
                $e = explode("_", $k);
                $aProperties[$e[1]][] = $e[2];
            }
        }

        return $aProperties;
    }

    public static function getChildGroups($group_id)
    {

        $result = [];
        $select = ShopGroup::where("parent_id", $group_id)->where('active', 1)->get();
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

    public static function showTreeGroupsAsOptions()
    {

        echo view("admin.shop.group-options", ["groups" => ShopGroup::getGroupTree()]);
    }

    public function filter(Request $request)
    {

        $oShopItems = self::prepareItems(ShopGroup::find($request->shop_group_id));

        $count = $oShopItems->count();

        return response()->json(["button" => "Показать $count " . Str::plural($count, "товар", "товара", "товаров")]);
    }

}