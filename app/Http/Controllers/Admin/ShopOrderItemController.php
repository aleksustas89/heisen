<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use Illuminate\Http\Request;

class ShopOrderItemController extends Controller
{


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, ShopOrder $shopOrder)
    {
        return view('admin.shop.order.item.create', [
            'breadcrumbs' => ShopController::breadcrumbs() + ShopOrderController::breadcrumbs(true),
            'shopOrder' => $shopOrder,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ShopOrder $shopOrder)
    {
        return self::saveShopItem($request, $shopOrder);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShopOrder $shopOrder, ShopOrderItem $shopOrderItem)
    {
        return view('admin.shop.order.item.edit', [
            'breadcrumbs' => ShopController::breadcrumbs() + ShopOrderController::breadcrumbs(true),
            'orderItem' => $shopOrderItem,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopOrder $shopOrder, ShopOrderItem $shopOrderItem)
    {

        return self::saveShopItem($request, $shopOrder, $shopOrderItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopOrder $shopOrder, ShopOrderItem $shopOrderItem)
    {
        $shopOrderItem->delete();

        return redirect()->back()->withSuccess('Элемент заказа был успешно удален!');
    }

    public static function saveShopItem($request, ShopOrder $shopOrder, $shopOrderItem = false)
    {

        if (!$shopOrderItem) {
            $shopOrderItem = new shopOrderItem();
            $shopOrderItem->shop_order_id = $shopOrder->id;
        }

        $shopOrderItem->shop_item_id = $request->shop_item_id;
        $shopOrderItem->name = $request->name;
        $shopOrderItem->quantity = $request->quantity;
        $shopOrderItem->price = $request->price;
        $shopOrderItem->save();

        return redirect(route("shop-order.edit", $shopOrderItem->shop_order_id))->withSuccess('Данные были успешно измененны!');
    }

}
