<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopItemListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ShopItemListItemController extends Controller
{

    public static $path = '/admin/shopItemListItem/'; 

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $list_id = Arr::get($_REQUEST, 'list_id', 0);

        if ($list_id > 0) {
            return view('admin.shop.item.list.item.index', [
                'breadcrumbs' => self::breadcrumbs(false, $list_id),
                'items' => ShopItemListItem::where("shop_item_list_id", $list_id)->get(),
                'list_id' => $list_id,
            ]);
        } 

        return redirect()->to(ShopItemListController::$path)->withError("Не передан #id списка!");

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $list_id = Arr::get($_REQUEST, 'list_id', 0);

        if ($list_id > 0) {
            return view('admin.shop.item.list.item.create', [
                'breadcrumbs' => self::breadcrumbs(true, $list_id),
                'list_id' => $list_id,
            ]);
        }

        return redirect()->to(ShopItemListController::$path)->withError("Не передан #id списка!");

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->saveShopItemListItem($request);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShopItemListItem $shopItemListItem)
    {
        return view('admin.shop.item.list.item.edit', [
            'breadcrumbs' => self::breadcrumbs(true, $shopItemListItem->shop_item_list_id),
            'list_item' => $shopItemListItem
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopItemListItem $shopItemListItem)
    {
        return $this->saveShopItemListItem($request, $shopItemListItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopItemListItem $shopItemListItem)
    {
        $shopItemListItem->delete();

        return redirect()->back()->withSuccess("Элемент списка был успешно удален!");
    }

    public function saveShopItemListItem(Request $request, $shopItemListItem = false)
    {
        if (!$shopItemListItem) {
            $shopItemListItem = new ShopItemListItem();
        }

        $shopItemListItem->value = $request->value;
        $shopItemListItem->description = $request->description ?? '';
        $shopItemListItem->sorting = $request->sorting ?? 0;
        $shopItemListItem->color = $request->color;
        $shopItemListItem->shop_item_list_id = $request->shop_item_list_id;
        $shopItemListItem->active = $request->active == 'on' ? 1 : 0;
        $shopItemListItem->save();

        $message = "Элемент списка был успешно сохранен!";

        if ($request->apply) {
            return redirect()->to(self::$path . '?list_id=' . $request->shop_item_list_id)->withSuccess($message);
        } else {
           return redirect()->back()->withSuccess($message);
        }

    }

    public static function breadcrumbs($lastItemIsLink = false, $list_id)
    {

        $aResult[3]["name"] = 'Элементы списка';
        if ($lastItemIsLink) {
            $aResult[3]["url"] = self::$path . '?list_id=' . $list_id;
        }

        return ShopItemListController::breadcrumbs(true) + $aResult;
    }
}
