<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopQuickOrder;
use App\Models\Shop;
use Illuminate\Http\Request;


class ShopQuickOrderController extends Controller
{
    public static $item_on_page = 100;

    /**
     * Display a listing of the resource.
     */
    public function index(Shop $shop)
    {
        return view('admin.shop.quick.order.index', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(),
            'quick_orders' => ShopQuickOrder::orderBy("created_at", "Desc")->paginate(self::$item_on_page),
            'shop' => $shop
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Shop $shop)
    {
        return view('admin.shop.quick.order.create', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
            'shop' => $shop
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Shop $shop)
    {
        return $this->saveQuickOrder($request, $shop);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop, ShopQuickOrder $shopQuickOrder)
    {
        return view('admin.shop.quick.order.edit', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
            'shopQuickOrder' => $shopQuickOrder,
            'shop' => $shop
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shop $shop, ShopQuickOrder $shopQuickOrder)
    {
        return $this->saveQuickOrder($request, $shop, $shopQuickOrder);
    }

    public function saveQuickOrder(Request $request, Shop $shop, $shopQuickOrder = false)
    {
        if (!$shopQuickOrder) {
            $shopQuickOrder = new shopQuickOrder();
        }

        $shopQuickOrder->name = $request->name;
        $shopQuickOrder->phone = $request->phone;
        $shopQuickOrder->shop_item_id = $request->shop_item_id;
        $shopQuickOrder->save();

        if ($request->apply) {
            return redirect(route("shop.shop-quick-order.index", ['shop' => $shop->id]))->withSuccess('Данные были успешно сохраненны!');
        } else {
            return redirect(route("shop.shop-quick-order.edit", ['shop' => $shop->id, 'quick_order' => $shopQuickOrder->id]))->withSuccess('Данные были успешно сохраненны!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop, ShopQuickOrder $shopQuickOrder)
    {
        $shopQuickOrder->delete();

        return redirect()->back()->withSuccess('Заказ был успешно удален!');
    }

    public static function breadcrumbs($lastItemIsLink = false)
    {
        $Result[1]["name"] = 'Быстрые заказы';
        if ($lastItemIsLink) {
            $Result[1]["url"] = route("shop.shop-quick-order.index", ['shop' => Shop::get()->id]);
        }
        
        return $Result;
    }

    /**
     * frontend function to save for item page
    */
    public function save(Request $request)
    {


        $shopQuickOrder = new shopQuickOrder();
        $shopQuickOrder->name = $request->name;
        $shopQuickOrder->phone = $request->phone;
        $shopQuickOrder->shop_item_id = $request->shop_item_id;
        $shopQuickOrder->save();

        return response()->json("Спасибо! Наши менеджеры свяжутся с Вами в ближайшее время!");

    }
}
