<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopItem;
use App\Http\Controllers\ShopGroupController;

class ShopPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.shop.price.index', [
            'breadcrumbs' => ShopController::breadcrumbs(),

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shop $shop)
    {

        if ($request->shop_group_id == 0) {
            return redirect()->back()->withError("Не выбрана группа!");
        }

        if (!$request->value > 0) {
            return redirect()->back()->withError("Не задано значение!");
        }

        $groups = ShopGroupController::ArrayMerge(ShopGroupController::getChildGroups($request->shop_group_id));

        $groups[] += $request->shop_group_id;

        foreach (ShopItem::whereIn("shop_group_id", $groups)->get() as $ShopItem) {

            $this->changePrice($ShopItem, $request->type, $request->value);

            foreach (ShopItem::where("modification_id", $ShopItem->id)->get() as $Modification) {
                $this->changePrice($Modification, $request->type, $request->value);
            }
        }

        $message = "Цены товаров были успешно изменены!";

        if ($request->apply) {
            return redirect()->to(route("shop.shop-price.index", ['shop' => $shop->id]))->withSuccess($message);
        } else {
            return redirect()->back()->withSuccess($message);
        }
    }

    protected function changePrice(ShopItem $shopItem, $type, $value)
    {

        switch ($type) {
            case 0:
                $shopItem->price = $shopItem->price + (($shopItem->price * $value) / 100);
                break;
            case 1:
                $shopItem->price = $shopItem->price + $value;
                break;
        }

        $shopItem->save();
    }
}
