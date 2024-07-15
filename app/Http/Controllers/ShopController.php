<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\ShopItem;
use App\Models\ShopCurrency;

class ShopController extends Controller
{

    public static function getObjectByPath()
    {

        $oShop = Shop::find(Shop::$shop_id);
        $aUrls = explode("/", request()->path());
        $count = count($aUrls);

        if ($oShop && $oShop->active == 1 && $count > 0 && $aUrls[0] == $oShop->path) {

            return self::getChildGroup(array_slice($aUrls, 1));        
        }

        return false;
    }

    public static function getChildGroup($aGroupUrls, $parent = 0, $level = 0)
    {    

        if (isset($aGroupUrls[$level])) {
            $ShopGroup = ShopGroup::where("active", 1)->where("path", $aGroupUrls[$level])->where("parent_id", $parent)->first();
            if (!is_null($ShopGroup) && $level < count($aGroupUrls) - 1) {
                $level++;
                return self::getChildGroup($aGroupUrls, $ShopGroup->id, $level);
            } else if (!is_null($ShopGroup) && $level == count($aGroupUrls) - 1) {
                return $ShopGroup;
            } else if ($level == count($aGroupUrls) - 1 && !is_null($ShopItem = ShopItem::where("active", 1)->where("path", $aGroupUrls[$level])->where("shop_group_id", $parent)->first())) {
                return $ShopItem;
            } else {
                return false;
            }
        }
    }

    public static function CurrentCurrency()
    {
        return ShopCurrency::where("default", 1)->first();
    }

    public static function buildGroupTree()
    {
        $aResult = [];
        foreach (ShopGroup::where("parent_id", 0)->where("active", 1)->where("hidden", 0)->orderBy("sorting", "ASC")->get() as $ShopGroup) {
            $aResult[$ShopGroup->id]["id"] = $ShopGroup->id;
            $aResult[$ShopGroup->id]["name"] = $ShopGroup->name;
            $aResult[$ShopGroup->id]["url"] = $ShopGroup->url;
            $aResult[$ShopGroup->id]["sub"] = [];
            foreach (ShopGroup::where("parent_id", $ShopGroup->id)->where("active", 1)->where("hidden", 0)->orderBy("sorting", "ASC")->get() as $sShopGroup) {

                $aResult[$ShopGroup->id]["sub"][$sShopGroup->id]["id"] = $sShopGroup->id;
                $aResult[$ShopGroup->id]["sub"][$sShopGroup->id]["name"] = $sShopGroup->name;
                $aResult[$ShopGroup->id]["sub"][$sShopGroup->id]["url"] = $sShopGroup->url;
            }
            
        }

        return $aResult;
    }
}
