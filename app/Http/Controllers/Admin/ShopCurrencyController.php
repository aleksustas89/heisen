<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopCurrency;
use Illuminate\Http\Request;

class ShopCurrencyController extends Controller
{

    public static $path = '/admin/shopCurrency/'; 

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.shop.currency.index', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(false),
            'currencies' => ShopCurrency::orderBy('sorting', 'asc')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.shop.currency.create', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->saveCurrency($request);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShopCurrency $shopCurrency)
    {
        return view('admin.shop.currency.edit', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
            'currency' => $shopCurrency,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopCurrency $shopCurrency)
    {
        return $this->saveCurrency($request, $shopCurrency);
    }

    public function saveCurrency(Request $request, $shopCurrency = false)
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
        $shopCurrency->default = $request->default == 'on' ? 1 : 0;

        if ($request->default == 'on') {
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
            return redirect()->to(self::$path)->withSuccess($message);
        } else {
            return redirect()->back()->withSuccess($message);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopCurrency $shopCurrency)
    {
        $shopCurrency->delete();

        return redirect()->back()->withSuccess('Валюта была успешно удалена!');
    }

    public static function breadcrumbs($link = true)
    {

        $aResult[1]["name"] = 'Валюты';
        if ($link) {
            $aResult[1]["url"] = self::$path;
        }
        

        return $aResult;
    }
}
