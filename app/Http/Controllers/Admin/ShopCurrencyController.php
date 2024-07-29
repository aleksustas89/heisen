<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopCurrency;
use Illuminate\Http\Request;
use App\Models\Shop;

class ShopCurrencyController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Shop $shop)
    {
        return view('admin.shop.currency.index', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(false),
            'currencies' => ShopCurrency::where("deleted", 0)->orderBy('sorting', 'asc')->get(),
            'shop' => $shop
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Shop $shop)
    {
        return view('admin.shop.currency.create', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
            'shop' => $shop
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Shop $shop)
    {
        return $this->saveCurrency($request, $shop);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop, ShopCurrency $shopCurrency)
    {
        return view('admin.shop.currency.edit', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
            'currency' => $shopCurrency,
            'shop' => $shop
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shop $shop, ShopCurrency $shopCurrency)
    {
        return $this->saveCurrency($request, $shop, $shopCurrency);
    }

    public function saveCurrency(Request $request, Shop $shop, $shopCurrency = false)
    {
        if (!$shopCurrency) {
            $shopCurrency = new ShopCurrency();
            $message = 'Валюта была успешно добавленна!';
        } else {
            $message = 'Валюта была успешно обновленна!';
        }

        $shopCurrency->name = $request->name;
        $shopCurrency->code = $request->code;
        $shopCurrency->exchange_rate = $request->exchange_rate;
        $shopCurrency->sorting = $request->sorting;
        $shopCurrency->default = $request->default;

        if ($request->default == 1) {
            foreach (ShopCurrency::whereNot("id", $shopCurrency->id)->get() as $oShopCurrency) {
                $oShopCurrency->default = 0;
                $oShopCurrency->save();
            }
            
            $shopCurrency->default = 1;
        } else {
            $shopCurrency->default = 0;
        }

        $shopCurrency->save();

        if ($request->apply > 0) {
            return redirect()->to(route('shop.shop-currency.index', ['shop' => $shop->id]))->withSuccess($message);
        } else {
            return redirect()->back()->withSuccess($message);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop, ShopCurrency $shopCurrency)
    {
        $shopCurrency->deleted = 1;
        $shopCurrency->save();

        return redirect()->back()->withSuccess('Валюта была успешно перемещена в корзину!');
    }

    public static function breadcrumbs($link = true)
    {

        $shop = Shop::get();

        $aResult[1]["name"] = 'Валюты';
        if ($link) {
            $aResult[1]["url"] = route('shop.shop-currency.index', ['shop' => $shop->id]);
        }
        

        return $aResult;
    }
}
