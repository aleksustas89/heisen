<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShopOrderItem;
use App\Models\ShopItemListItem;
use App\Models\PropertyValueInt;
use App\Models\ShopItem;


class StatisticController extends Controller
{

    public $color_shop_item_list_id = 195;

    public $color_property_id = 60;
    
    public function index(Request $request)
    {

        $ShopOrderItems = ShopOrderItem::selectRaw('shop_item_id, SUM(quantity) AS quantity')
                                ->join("shop_orders", "shop_orders.id", "=", "shop_order_items.shop_order_id")
                                ->where("shop_order_items.deleted", 0)
                                ->where("shop_orders.deleted", 0)
                                ->groupBy("shop_item_id");

        if (!empty($request->datetime_from) || !empty($request->datetime_to)) {


            if (!empty($request->datetime_from)) {
                $ShopOrderItems->where("shop_orders.created_at", ">=", date("Y-m-d H:i:s", strtotime($request->datetime_from)));
            }

            if (!empty($request->datetime_to)) {
                $ShopOrderItems->where("shop_orders.created_at", "<=", date("Y-m-d H:i:s", strtotime($request->datetime_to)));
            }

        }

        if (!empty($request->price_from) || !empty($request->price_to)) {
            if (!empty($request->price_from)) {
                $ShopOrderItems->where("shop_order_items.price", ">=", $request->price_from);
            }
            if (!empty($request->price_to)) {
                $ShopOrderItems->where("shop_order_items.price", "<=", $request->price_to);
            }
        }
                            
        if ($request->color) {
            $PropertyValueInt = PropertyValueInt::select("entity_id")
                                        ->distinct()
                                        ->where("property_id", $this->color_property_id)
                                        ->where("value", $request->color);

            $ShopOrderItems->whereIn("shop_item_id", $PropertyValueInt);
        }

        $aCheckedIds = [];

        if ($request->shop_items) {

            
            foreach ($request->shop_items as $id) {
                $aCheckedIds[] = $id;

                foreach (ShopItem::where("modification_id", $id)->where("deleted", 0)->where("active", 1)->get() as $shopItem) {
                    $aCheckedIds[] = $shopItem->id;
                }
            }

            $ShopOrderItems->whereIn("shop_item_id", $aCheckedIds);
        }

        $aShopItemMarkings = [];
        $aShopItems = [];

        foreach ($ShopOrderItems->get() as $key => $ShopOrderItem) {

            if (!is_null($shopItem = $ShopOrderItem->shopItem)) {

                list($marking) = explode("_", $shopItem->marking);

                if (!empty($marking)) {

                    $Color = PropertyValueInt::where("property_id", $this->color_property_id)
                        ->where("entity_id", $shopItem->id)
                        ->first();

                    if (isset($aShopItems[$marking]['quantity'])) {
                        $aShopItems[$marking]['quantity'] += (int)$ShopOrderItem->quantity;
                    } else {

                        $aShopItems[$marking]['name'] = $shopItem->parentItemIfModification()->name;
                        $aShopItems[$marking]['price'] = $shopItem->price;
                        $aShopItems[$marking]['quantity'] = (int)$ShopOrderItem->quantity;

                        $aShopItemMarkings[] = $marking;
                    }
                    
                    if (!is_null($Color) && $Color->value > 0) {

                        if (isset($aShopItems[$marking]["colors"][$Color->value])) {
                            $aShopItems[$marking]["colors"][$Color->value] += (int)$ShopOrderItem->quantity;
                        } else {
                            $aShopItems[$marking]["colors"][$Color->value] = (int)$ShopOrderItem->quantity;
                        }
                    }

                } else {
                    //echo $shopItem->id . " не имеют артикула! нужно исправить!<br>";
                }
            }
        }

        $aColors = [];

        foreach (ShopItemListItem::where("shop_item_list_id", $this->color_shop_item_list_id)->where("deleted", 0)->get() as $Color) {
            $aColors[$Color->id] = $Color;
        }

        $aQuantity = array_column($aShopItems, 'quantity');
        array_multisort($aQuantity, SORT_DESC, $aShopItems);

        return view("admin.statistic.index", [
            "breadcrumbs" => $this->breadcrumbs(),
            "shopItems" => $aShopItems,
            "Colors" => $aColors,
            "current_color_id" => $request->color,
            "groupShopItems" => $request->shop_group_id && count($aCheckedIds) > 0 ? ShopItem::where("shop_group_id", $request->shop_group_id)->where("active", 1)->where("deleted", 0)->get() : false,
            "aCheckedIds" => $aCheckedIds
        ]);
    }

    public function getGroupItems(Request $request)
    {

        return response()->view("admin.statistic.shop-items", [
            "groupShopItems" => ShopItem::where("shop_group_id", $request->shop_group_id)->where("active", 1)->where("deleted", 0)->get(),
            "aCheckedIds" => []
        ]);
    }

    public function breadcrumbs()
    {
        $aResult[0]["name"] = 'Статистика';
        $aResult[0]["url"] = route("statistic.index");

        return $aResult;
    }
}
