<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\ShopItem;
use App\Models\ShopCurrency;
use Illuminate\Http\Request;
use App\Models\ShopItemShortcut;

class ShopController extends Controller
{

    public static $items_on_page = 15;

    /**
     * Display a listing of the resource.
     */

    public function deletion(Request $request)
    {

        $count = 0;

        if ($request->shop_items) {
            foreach ($request->shop_items as $shop_item_id => $on) {

                if (!is_null($shopItem = ShopItem::find($shop_item_id))) {
                    $shopItem->deleted = 1;
                    $shopItem->save();
                    $count++;
                }
            }
        }

        if ($request->shop_groups) {
            foreach ($request->shop_groups as $shop_group_id => $on) {

                if (!is_null($shopGroup = ShopGroup::find($shop_group_id))) {
                    $shopGroup->deleted = 1;
                    $shopGroup->save();
                    $count++;
                }
            }
        }

        if ($count > 0) {
            return redirect()->back()->withSuccess("Успешно перемещено в корзину: ". $count ."!");
        } else {
            return redirect()->back()->withError("Элементы для удаления не были выбраны!");
        }

        
    }

    public function index(Request $request)
    {

        if ($request->operation == 'delete') {

            return $this->deletion($request);
        }

        $oShop = Shop::find(Shop::$shop_id);
        if (!is_null($oShop->id) && $oShop->active == 1) {

            $parent = $request->parent_id ?? 0;

            $aResult = [
                'shop_path' => $oShop->path,
                'parent' => $parent,
                'shop' => $oShop,
                'breadcrumbs' => ShopGroupController::breadcrumbs($parent > 0 ? ShopGroup::find($parent) : false, [], true),
                'global_search' => $request->global_search,
                "BadgeClasses" => \App\Models\ShopItemShortcut::$BadgeClasses
            ];

            if ($request->global_search) {
                $aResult['shopItems'] = ShopItem::where("modification_id", "=", 0)
                                                ->where(function($query) use ($request) {
                                                    $query->orWhere("id", "=", $request->global_search)
                                                        ->orWhere("name", "LIKE", "%" . $request->global_search . "%");
                                                })
                                                ->orderBy("sorting", "asc")
                                                ->orderBy("id", "desc")
                                                ->where("deleted", 0)
                                                ->paginate(self::$items_on_page);
                
                                                

            } else {
                                                
                $aResult['shopItems'] = ShopItem::where(function($query) use ($parent) {
                        $query
                            ->where("modification_id", "=", 0)
                            ->where("shop_group_id", $parent)
                            ->where("deleted", 0);
                    })
                    ->orWhereIn("id", ShopItemShortcut::select('shop_item_id')->where('shop_group_id', $parent))
                    ->orderBy("sorting", "asc")
                    ->orderBy("id", "desc")
                    ->paginate(self::$items_on_page);
                                        

                $aResult['shopGroups'] = ShopGroup::where('parent_id', $parent)
                                            ->where("deleted", 0)
                                            ->orderBy('sorting', 'desc')
                                            ->orderBy("id")
                                            ->get();
            }

            return view('admin.shop.index', $aResult);

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

        $shop->seo_group_title_template = $request->seo_group_title_template;
        $shop->seo_group_description_template = $request->seo_group_description_template;
        $shop->seo_group_keywords_template = $request->seo_group_keywords_template;
        $shop->seo_item_title_template = $request->seo_item_title_template;
        $shop->seo_item_description_template = $request->seo_item_description_template;
        $shop->seo_item_keywords_template = $request->seo_item_keywords_template;

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
        $shop->apply_items_price_to_modifications = $request->apply_items_price_to_modifications ?? 0;

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
