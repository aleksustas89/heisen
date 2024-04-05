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

        return ShopItem::select("shop_items.*")
                        ->whereIn("shop_items.id", function ($query) {
                            $query->selectRaw('IF(`modification_id` > 0, shop_items.modification_id, shop_items.id)')
                                ->from("shop_items")
                                ->join("shop_item_discounts", "shop_item_discounts.shop_item_id", "=", "shop_items.id")
                                ->join("shop_discounts", "shop_item_discounts.shop_discount_id", "=", "shop_discounts.id")
                                ->where("shop_discounts.start_datetime", "<=", date("Y-m-d H:i:s"))
                                ->where("shop_discounts.end_datetime", ">=", date("Y-m-d H:i:s"))
                                ->where("shop_items.active", 1)
                                ->where("shop_discounts.active", 1);
                        });
    }

    public function showItemWithDiscountsAjax() {

        return view('shop.ajax-group', [
            "items" => self::prepareSql()->paginate(self::$items_on_page),
        ]);

    }
}
