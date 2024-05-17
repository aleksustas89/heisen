<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopItemList;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopItemListController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Shop $shop)
    {
        return view('admin.shop.item.list.index', [
            'breadcrumbs' => self::breadcrumbs(),
            'lists' => ShopItemList::get(),
            'shop' => $shop
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Shop $shop)
    {
        return view('admin.shop.item.list.create', [
            'breadcrumbs' => self::breadcrumbs(true),
            'shop' => $shop
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Shop $shop)
    {
        return $this->saveShopItemList($request, $shop);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop, ShopItemList $shopItemList)
    {
        return view('admin.shop.item.list.edit', [
            'breadcrumbs' => self::breadcrumbs(true),
            'list' => $shopItemList,
            'shop' => $shop
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shop $shop, ShopItemList $shopItemList)
    {
        return $this->saveShopItemList($request, $shop, $shopItemList);
    }

    public function saveShopItemList(Request $request, Shop $shop, $shopItemList = false)
    {
        if (!$shopItemList) {
            $shopItemList = new ShopItemList();
        }

        $shopItemList->name = $request->name;
        $shopItemList->description = $request->description;
        $shopItemList->save();

        $message = "Список был успешно сохранен!";

        if ($request->apply) {
            return redirect()->to(route('shop.shop-item-list.index', ['shop' => $shop->id]))->withSuccess($message);
        } else {
           return redirect()->back()->withSuccess($message);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop, ShopItemList $shopItemList)
    {

        $shopItemList->delete();

        return redirect()->back()->withSuccess("Свойство было успешно удалено!");
    }

    public static function breadcrumbs($lastItemIsLink = false)
    {
        $shop = Shop::get();

        $aResult[2]["name"] = 'Списки';
        if ($lastItemIsLink) {
            $aResult[2]["url"] = route('shop.shop-item-list.index', ['shop' => $shop->id]);
        }

        return ShopItemPropertyController::breadcrumbs(true) + $aResult;
    }

}
