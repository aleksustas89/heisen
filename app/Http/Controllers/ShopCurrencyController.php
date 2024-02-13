<?php

namespace App\Http\Controllers;

use App\Models\ShopCurrency;

class ShopCurrencyController extends Controller
{

    protected static $_cookie_live = 3600 * 24 * 30;

    public static function set()
    {

        $aShopCurrencies = [];

        foreach (ShopCurrency::get() as $ShopCurrency) {
            $aShopCurrencies[] = $ShopCurrency->id;
        }

        if (!is_null(request()->currency) && in_array(request()->currency, $aShopCurrencies)) {
            setcookie("currency", request()->currency, time() + (self::$_cookie_live), "/");
        }
    }

    public static function getDefault()
    {
        return ShopCurrency::whereDefault(1)->first();
    }

    protected static function getById($id)
    {
        return $id > 0 ? ShopCurrency::whereId($id)->first() : false;
    } 

    public static function getCurrent()
    {
        if (request()->currency || isset($_COOKIE["currency"])) {
            return self::getById(request()->currency ?? $_COOKIE["currency"]);
        } else {
            return self::getDefault();
        }
    }
}
