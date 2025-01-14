<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopItemListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\Shop;

class ShopItemListItemController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index(Shop $shop)
    {

        $list_id = Arr::get($_REQUEST, 'list_id', 0);

        if ($list_id > 0) {
            return view('admin.shop.item.list.item.index', [
                'breadcrumbs' => self::breadcrumbs(false, $list_id),
                'items' => ShopItemListItem::where("shop_item_list_id", $list_id)->where("deleted", 0)->orderBy("sorting", "ASC")->get(),
                'list_id' => $list_id,
                'shop' => $shop,
            ]);
        } 

        return redirect()->to(route('shop.shop-item-list.index', ['shop' => $shop->id]))->withError("Не передан #id списка!");

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Shop $shop)
    {
        $list_id = Arr::get($_REQUEST, 'list_id', 0);

        if ($list_id > 0) {
            return view('admin.shop.item.list.item.create', [
                'breadcrumbs' => self::breadcrumbs(true, $list_id),
                'list_id' => $list_id,
                'shop' => $shop,
            ]);
        }

        return redirect()->to(route('shop.shop-item-list.index', ['shop' => $shop->id]))->withError("Не передан #id списка!");

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Shop $shop)
    {
        return $this->saveShopItemListItem($request, $shop);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop, ShopItemListItem $shopItemListItem)
    {
        return view('admin.shop.item.list.item.edit', [
            'breadcrumbs' => self::breadcrumbs(true, $shopItemListItem->shop_item_list_id),
            'list_item' => $shopItemListItem,
            'shop' => $shop,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shop $shop, ShopItemListItem $shopItemListItem)
    {
        return $this->saveShopItemListItem($request, $shop, $shopItemListItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop, ShopItemListItem $shopItemListItem)
    {
        $shopItemListItem->deleted = 1;
        $shopItemListItem->save();

        return redirect()->back()->withSuccess("Элемент списка был успешно перемещен в корзину!");
    }

    public function saveShopItemListItem(Request $request, Shop $shop, $shopItemListItem = false)
    {
        if (!$shopItemListItem) {
            $shopItemListItem = new ShopItemListItem();
        }

        $shopItemListItem->value = $request->value;
        $shopItemListItem->declension = $request->declension;
        $shopItemListItem->description = $request->description ?? '';
        $shopItemListItem->static_filter_path = $request->static_filter_path ?? '';
        $shopItemListItem->sorting = $request->sorting ?? 0;
        $shopItemListItem->color = $request->color;
        $shopItemListItem->shop_item_list_id = $request->shop_item_list_id;
        $shopItemListItem->active = $request->active ?? 0;
        $shopItemListItem->save();

        $message = "Элемент списка был успешно сохранен!";

        if ($request->apply) {
            return redirect()->to(route('shop.shop-item-list-item.index', ['shop' => $shop->id]) . '?list_id=' . $request->shop_item_list_id)->withSuccess($message);
        } else {
           return redirect()->back()->withSuccess($message);
        }

    }

    public static function breadcrumbs($lastItemIsLink = false, $list_id)
    {

        $shop = Shop::get();

        $aResult[3]["name"] = 'Элементы списка';
        if ($lastItemIsLink) {
            $aResult[3]["url"] = route('shop.shop-item-list-item.index', ['shop' => $shop->id]) . '?list_id=' . $list_id;
        }

        return ShopItemListController::breadcrumbs(true) + $aResult;
    }
}
