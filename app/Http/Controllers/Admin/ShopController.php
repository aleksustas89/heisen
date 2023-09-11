<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\ShopItem;
use App\Models\ShopCurrency;
use Illuminate\Http\Request;

class ShopController extends Controller
{

    public static $items_on_page = 15;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $oShop = Shop::find(Shop::$shop_id);
        if (!is_null($oShop->id) && $oShop->active == 1) {

            $parent = $request->parent_id ?? 0;

            return view('admin.shop.index', [
                'shop_path' => $oShop->path,
                'shopGroups' => ShopGroup::where('parent_id', $parent)->orderBy('sorting', 'desc')->orderBy("id")->get(),
                'shopItems' => ShopItem::where('shop_group_id', $parent)->where("modification_id", "=", 0)->orderBy("sorting", "asc")->orderBy("id")->paginate(self::$items_on_page),
                'parent' => $parent,
                'breadcrumbs' => ShopGroupController::breadcrumbs($parent > 0 ? ShopGroup::find($parent) : false),
            ]);

        } else {
            return "The store is off";
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop)
    {
        return view('admin.shop.edit', [
            'shop' => $shop,
            'breadcrumbs' => self::breadcrumbs(),
            'currencies' => ShopCurrency::get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shop $shop)
    {

        $shop->name = $request->name;
        $shop->currency_id = $request->currency_id;
        $shop->description = $request->description;
        $shop->email = $request->email;
        $shop->active = $request->active;
        $shop->path = $request->path;
        $shop->items_on_page = $request->items_on_page;
        $shop->seo_title = $request->seo_title;
        $shop->seo_description = $request->seo_description;
        $shop->seo_keywords = $request->seo_keywords;

        $shop->image_large_max_width = $request->image_large_max_width;
        $shop->image_large_max_height = $request->image_large_max_height;
        $shop->image_small_max_width = $request->image_small_max_width;
        $shop->image_small_max_height = $request->image_small_max_height;
        $shop->preserve_aspect_ratio = $request->preserve_aspect_ratio;
        $shop->preserve_aspect_ratio_small = $request->preserve_aspect_ratio_small;
        $shop->group_image_large_max_width = $request->group_image_large_max_width;
        $shop->group_image_large_max_height = $request->group_image_large_max_height;
        $shop->group_image_small_max_width = $request->group_image_small_max_width;
        $shop->group_image_small_max_height = $request->group_image_small_max_height;
        $shop->preserve_aspect_ratio_group = $request->preserve_aspect_ratio_group;
        $shop->preserve_aspect_ratio_group_small = $request->preserve_aspect_ratio_group_small;

        $shop->save();

        $message = 'Настройки интернет-магазина были успешно обновлены!';

        if ($request->apply) {
            return redirect()->to(route("shop.index"))->withSuccess($message);
        } else {
            return redirect()->back()->withSuccess($message);
        }
    }

    public static function breadcrumbs()
    {
        $aResult[0]["name"] = 'Интернет-магазин';
        $aResult[0]["url"] = route("shop.index");

        return $aResult;
    }

}
