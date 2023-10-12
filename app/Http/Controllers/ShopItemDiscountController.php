<?php

namespace App\Http\Controllers;

use App\Models\ShopItem;
use Illuminate\Http\Request;

class ShopItemDiscountController extends Controller
{

    public static $items_on_page = 15;
    
    public function showItemWithDiscounts()
    {

        return view('shop.discounts', [
            "ShopItems" => self::prepareSql()->paginate(self::$items_on_page),
            "breadcrumbs" => [
                0 => [
                    "name" => "Главная",
                    "url" => '/'
                ],
                1 => [
                    "name" => "Товары со скидками",
                ],
            ]
        ]);
    }

    public static function countItemsWithDiscounts() 
    {
        return self::prepareSql()->count();
    }

    public static function prepareSql()
    {
        return ShopItem::where("discounts", 1)->where("active", 1);
    }

    public function showItemWithDiscountsAjax() {

        return view('shop.ajax-group', [
            "items" => self::prepareSql()->paginate(self::$items_on_page),
        ]);

    }
}
