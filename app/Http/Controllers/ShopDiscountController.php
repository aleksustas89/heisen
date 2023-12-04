<?php

namespace App\Http\Controllers;

use App\Models\ShopDiscount;
use App\Models\ShopItemDiscount;
use App\Models\ShopItem;
use Illuminate\Http\Request;

class ShopDiscountController extends Controller
{
    public static function getPriceApplyDiscount(ShopItem $ShopItem, ShopDiscount $ShopDiscount)
    {
        $Result = 0;

        switch ($ShopDiscount->type) {
            case 0:
                $Result = round($ShopItem->price - ($ShopDiscount->value * $ShopItem->price / 100));
            break;
            case 1:
                $Result = round($ShopItem->price - $ShopDiscount->value);
            break;
        }

        return $Result;
    }

    public static function getMaxDiscount(ShopItem $ShopItem)
    {
        $Value = false;
        $Discount = false;
        if ($itemDiscounts = self::getDiscountsForItemAndModifications($ShopItem)) {
            foreach ($itemDiscounts as $itemDiscount) {
                if ($itemDiscount->check()) {
                    $priceWithDiscount = ShopDiscountController::getPriceApplyDiscount($ShopItem, $itemDiscount);
                    if ((!$Value || $Value > $priceWithDiscount)) {
                        $Discount = $itemDiscount;
                        $Value = $priceWithDiscount;
                    }

                }
            }
        }

        return $Discount;
    }

    /**
     * get % for static discount
    */
    public static function getDiscountPercent(ShopItem $ShopItem, $price)
    {
        return round($price * 100 / $ShopItem->price);
    }

    /**
     * @param return array of discounts ShopDiscount linked with shop 
     * item or its modifications
    */
    public static function getDiscountsForItemAndModifications(ShopItem $ShopItem): array|bool
    {
        $aResult = false;

        $ShopItemDiscounts = ShopItemDiscount::where("shop_item_id", $ShopItem->id)->orWhereIn("shop_item_id", function ($query) use ($ShopItem) {
            $query->selectRaw('shop_items.id')->from('shop_items')->where("modification_id", $ShopItem->id);
        })->get();

        foreach ($ShopItemDiscounts as $ShopItemDiscount) {
            if (!isset($aResult[$ShopItemDiscount->shop_discount_id]) && $ShopItemDiscount->ShopDiscount->check()) {
                $aResult[$ShopItemDiscount->shop_discount_id] = $ShopItemDiscount->ShopDiscount;
            }
        }

        return $aResult ? array_unique($aResult) : false;
    }

    /**
     * @return array 
     * @param $ShopItem - parent ShopItem
     * prices of modifications 
    */
    public static function getModificationsPricesWithDiscounts(ShopItem $ShopItem) : array
    {
        $aResult = [];
 
        if ($ShopItem->modification_id == 0) {

            $Modifications = ShopItemDiscount::select("shop_item_discounts.value", "shop_items.price")
                                ->rightJoin("shop_items", "shop_items.id", "=", "shop_item_discounts.shop_item_id")
                                ->where("shop_items.modification_id", $ShopItem->id)->get();

            foreach ($Modifications as $Modification) {
                $aResult[] = !is_null($Modification->value) ? $Modification->value : $Modification->price;
            }
        }

        return array_unique($aResult);
    }

    public static function getShopItemPriceWithDiscount(ShopItem $ShopItem)
    {

        $aResult = [$ShopItem->price];

        foreach ($ShopItem->ShopItemDiscounts as $ShopItemDiscount) {
   
            if (!is_null($ShopItemDiscount->ShopDiscount) && $ShopItemDiscount->ShopDiscount->check()) {
          
                $aResult[] = $ShopItemDiscount->value;      
            }
        }

        return min($aResult);
    }
}
