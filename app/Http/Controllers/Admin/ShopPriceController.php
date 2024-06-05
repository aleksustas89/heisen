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

    public function filter(Request $request)
    {
        $shopItems = ShopItem::where('modification_id', '=', 0)
                        ->where(function($query) use ($request) {
                            $query
                                ->where("name", "LIKE", "%" . $request->search . "%")
                                ->orWhere("marking", "LIKE", "%" . $request->search . "%")
                                ->orWhere("id", $request->search);
                        })
                        ->get();

        return response()->view("admin.shop.price.filter", [
            "shopItems" => $shopItems
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shop $shop)
    {

        $message = '';

        if ($request->type == 0) {

            $ids = [];

            if (count($request->shop_items) > 0) {
                foreach ($request->shop_items as $id => $on) {
                    $ids[] = $id; 
                }
    
                foreach (ShopItem::select("id")->whereIn("modification_id", $ids)->get() as $Modification) {
                    $ids[] = $Modification->id;
                }
    
                ShopItem::whereIn("id", $ids)->update(['price' => $request->new_price]);

                $message = "Количество обновленных товаров - " . count($request->shop_items);
            } else {
                return redirect()->back()->withError("Не были выбраны товары для изменения!");
            }

        } else {
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
        }

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
