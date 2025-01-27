<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Models\Shop;
use App\Models\ShopQuickOrder;
use App\Models\ShopOrderItem;
use App\Models\PropertyValueInt;
use App\Models\ShopItemListItem;
use App\Models\Client;

class HomeController extends Controller
{

    public $color_property_id = 60;

    public $color_shop_item_list_id = 195;

    public function index()
    {

        $aColors = [];

        $get_colors_orders = $this->getColorsOrders();

        foreach (ShopItemListItem::where("shop_item_list_id", $this->color_shop_item_list_id)->where("deleted", 0)->get() as $Color) {
            $aColors[$Color->id]['name'] = $Color->value;
            $aColors[$Color->id]['color'] = $Color->color;
            $aColors[$Color->id]['count'] = isset($get_colors_orders[$Color->id]) ? $get_colors_orders[$Color->id] : 0;
        }

        $orders_sum = 0;
        $Orders = ShopOrder::where("deleted", 0)->get();
        foreach ($Orders as $Order) {
            $orders_sum += $Order->getSum();
        }

        return view('admin.home.index', [
            'shop' => Shop::get(),
            'orders' => ShopOrder::orderBy("created_at", "Desc")->where("deleted", 0)->paginate(10),
            'quick_orders' => ShopQuickOrder::where("deleted", 0)->orderBy("created_at", "Desc")->paginate(10),
            'colors' => $aColors,
            'clients_count' => Client::count(),
            'orders_sum' => $orders_sum,
            'orders_count' => $Orders->count()
        ]);
    }

    public function getColorsOrders()
    {

        $aResult = [];

        $ShopOrderItems = PropertyValueInt::selectRaw('property_value_ints.value, shop_order_items.shop_item_id, shop_order_items.shop_order_id, shop_order_items.quantity')
                                ->join("shop_order_items", "property_value_ints.entity_id", "=", "shop_order_items.shop_item_id")
                                ->join("shop_orders", "shop_orders.id", "=", "shop_order_items.shop_order_id")
                                ->where("property_value_ints.property_id", $this->color_property_id)
                                ->where("shop_orders.deleted", 0);

        foreach ($ShopOrderItems->get() as $ShopOrderItem) {
            if (isset($aResult[$ShopOrderItem->value])) {
                $aResult[$ShopOrderItem->value] = $aResult[$ShopOrderItem->value] + (int)$ShopOrderItem->quantity;
            } else {
                $aResult[$ShopOrderItem->value] = (int)$ShopOrderItem->quantity;
            }
        }

        return $aResult;
    }
}
