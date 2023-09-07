<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopItemList;
use Illuminate\Http\Request;

class ShopItemListController extends Controller
{

    public static $path = '/admin/shopItemList/'; 

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.shop.item.list.index', [
            'breadcrumbs' => self::breadcrumbs(),
            'lists' => ShopItemList::get(),
            'listItemPath' => ShopItemListItemController::$path
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.shop.item.list.create', [
            'breadcrumbs' => self::breadcrumbs(true),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->saveShopItemList($request);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShopItemList $shopItemList)
    {
        return view('admin.shop.item.list.edit', [
            'breadcrumbs' => self::breadcrumbs(true),
            'list' => $shopItemList,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopItemList $shopItemList)
    {
        return $this->saveShopItemList($request, $shopItemList);
    }

    public function saveShopItemList(Request $request, $shopItemList = false)
    {
        if (!$shopItemList) {
            $shopItemList = new ShopItemList();
        }

        $shopItemList->name = $request->name;
        $shopItemList->description = $request->description;
        $shopItemList->save();

        $message = "Список был успешно сохранен!";

        if ($request->apply) {
            return redirect()->to(self::$path)->withSuccess($message);
        } else {
           return redirect()->back()->withSuccess($message);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopItemList $shopItemList)
    {

        $shopItemList->delete();

        return redirect()->back()->withSuccess("Свойство было успешно удалено!");
    }

    public static function breadcrumbs($lastItemIsLink = false)
    {
        $aResult[2]["name"] = 'Списки';
        if ($lastItemIsLink) {
            $aResult[2]["url"] = self::$path;
        }

        return ShopItemPropertyController::breadcrumbs(true) + $aResult;
    }

}
