<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShopOrderItem;
use App\Models\ShopItemListItem;
use App\Models\PropertyValueInt;
use App\Models\ShopItem;
use PhpParser\Node\Expr\List_;

class StatisticController extends Controller
{

    public $color_shop_item_list_id = 195;

    public $color_property_id = 60;
    
    public function index(Request $request)
    {

        $ShopOrderItems = ShopOrderItem::selectRaw('shop_item_id, SUM(quantity) AS quantity')
                                ->groupBy("shop_item_id")
                                ->orderBy("quantity", "DESC");

        if (!empty($request->datetime_from) || !empty($request->datetime_to)) {
            $ShopOrderItems
                ->join("shop_orders", "shop_orders.id", "=", "shop_order_items.shop_order_id");

            if (!empty($request->datetime_from)) {
                $ShopOrderItems->where("shop_orders.created_at", ">=", date("Y-m-d H:i:s", strtotime($request->datetime_from)));
            }

            if (!empty($request->datetime_to)) {
                $ShopOrderItems->where("shop_orders.created_at", "<=", date("Y-m-d H:i:s", strtotime($request->datetime_to)));
            }

        }
                            
        if ($request->color) {
            $PropertyValueInt = PropertyValueInt::select("entity_id")
                                        ->distinct()
                                        ->where("property_id", $this->color_property_id)
                                        ->where("value", $request->color);

            $ShopOrderItems->whereIn("shop_item_id", $PropertyValueInt);

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

        return view("admin.shop.statistic.index", [
            "breadcrumbs" => $this->breadcrumbs(),
            "shopItems" => $aShopItems,
            "Colors" => $aColors,
            "current_color_id" => $request->color
        ]);
    }

    public function breadcrumbs()
    {
        $aResult[0]["name"] = 'Статистика';
        $aResult[0]["url"] = route("statistic.index");

        return $aResult;
    }
}
