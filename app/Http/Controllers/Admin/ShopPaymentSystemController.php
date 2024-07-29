<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopPaymentSystem;
use Illuminate\Http\Request;
use App\Models\Shop;


class ShopPaymentSystemController extends Controller
{

    public static $items_on_page = 15;

    /**
     * Display a listing of the resource.
     */
    public function index(Shop $shop)
    {
        return view('admin.shop.payment.index', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(),
            'PaymentSystems' => ShopPaymentSystem::where("deleted", 0)->paginate(self::$items_on_page),
            'shop' => $shop
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Shop $shop)
    {
        return view('admin.shop.payment.create', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(),
            'shop' => $shop,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->saveShopPaymentSystem($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(ShopPaymentSystem $shopPaymentSystem)
    {

        $shop = Shop::get();

        return redirect()->to(route("shop.shop-payment-system.index", ['shop' => $shop->id]));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop, ShopPaymentSystem $shopPaymentSystem)
    {

        return view('admin.shop.payment.edit', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(),
            'shop' => $shop,
            "shopPaymentSystem" => $shopPaymentSystem,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shop $shop, ShopPaymentSystem $shopPaymentSystem)
    {
        return $this->saveShopPaymentSystem($request, $shopPaymentSystem);
    }

    public function saveShopPaymentSystem($request, $shopPaymentSystem = false)
    {

        $shop = Shop::get();

        if (!$shopPaymentSystem) {
            $shopPaymentSystem = new ShopPaymentSystem();
            $shopPaymentSystem->save();
        }

        $shopPaymentSystem->name = $request->name;
        $shopPaymentSystem->sorting = $request->sorting;
        $shopPaymentSystem->active = $request->active;
        $shopPaymentSystem->save();

        $message = 'Платежная система была успешно обновлена!';
        
        if ($request->apply) {
            return redirect()->to(route("shop.shop-payment-system.index", ['shop' => $shop->id]))->withSuccess($message);
        } else {
            return redirect()->back()->withSuccess($message);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop, ShopPaymentSystem $shopPaymentSystem)
    {
        $shopPaymentSystem->deleted = 1;
        $shopPaymentSystem->save();

        return redirect()->back()->withSuccess("Платежная система была успешно перемещена в корзину!");
    }

    public static function breadcrumbs($lastItemIsLink = false)
    {
        $shop = Shop::get();
        
        $Result[1]["name"] = 'Платежные системы';
        if ($lastItemIsLink) {
            $Result[1]["url"] = route("shop.shop-payment.index", ['shop' => $shop->id]);
        }
        
        return $Result;
    }
}
