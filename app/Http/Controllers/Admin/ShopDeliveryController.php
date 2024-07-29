<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopDelivery;
use Illuminate\Http\Request;
use App\Models\Shop;

class ShopDeliveryController extends Controller
{

    public static $items_on_page = 15;

    /**
     * Display a listing of the resource.
     */
    public function index(Shop $shop)
    {
        return view('admin.shop.delivery.index', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(),
            'deliveries' => ShopDelivery::where("deleted", 0)->paginate(self::$items_on_page),
            'shop' => $shop
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Shop $shop)
    {
        return view('admin.shop.delivery.create', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
            'shop' => $shop
        ]);
    }

    /**
     * Show the form for editing the specified resource.
    */
    public function edit(Shop $shop, ShopDelivery $shopDelivery)
    {
        return view('admin.shop.delivery.edit', [
            'shopDelivery' => $shopDelivery,
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
            'shop' => $shop
        ]);
    }

    /**
     * Update the specified resource in storage.
    */
    public function update(Request $request, Shop $shop, ShopDelivery $shopDelivery)
    {

        return $this->saveDelivery($request, $shop, $shopDelivery);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Shop $shop)
    {
        return $this->saveDelivery($request, $shop);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop, ShopDelivery $shopDelivery)
    {

        $shopDelivery->deleted = 1;
        $shopDelivery->save();

        return redirect()->back()->withSuccess("Доставка была успешно перемещена в корзину!");
    }

    public function saveDelivery(Request $request, $shop, $shopDelivery = false)
    {

        
        if (!$shopDelivery) {
            $shopDelivery = new ShopDelivery();
        }

        $shopDelivery->name = $request->name;
        $shopDelivery->description = $request->description;
        $shopDelivery->sorting = $request->sorting;
        $shopDelivery->color = $request->color;
        $shopDelivery->save();

        $text = 'Данные были успешно сохраненны!';

        if ($request->apply) {
            return redirect(route("shop.shop-delivery.index", ['shop' => $shop->id]))->withSuccess($text);
        } else {
            return redirect()->back()->withSuccess($text);
        }
    }

    public static function breadcrumbs($lastItemIsLink = false)
    {
        $shop = Shop::get();
        
        $Result[1]["name"] = 'Доставки';
        if ($lastItemIsLink) {
            $Result[1]["url"] = route("shop.shop-delivery.index", ['shop' => $shop->id]);
        }
        
        return $Result;
    }
}
