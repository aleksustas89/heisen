<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopItemProperty;
use App\Models\ShopItemList;
use Illuminate\Http\Request;
use App\Models\ShopGroup;
use App\Models\ShopItemPropertyForGroup;
use App\Models\Shop;

class ShopItemPropertyController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Shop $shop)
    {
        return view('admin.shop.item.property.index', [
            'breadcrumbs' => self::breadcrumbs(),
            'properties' => ShopItemProperty::get(),
            'types' => ShopItemProperty::types(),
            'shop' => $shop,

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Shop $shop)
    {
        return view('admin.shop.item.property.create', [
            "breadcrumbs" => self::breadcrumbs(true),
            'types' => ShopItemProperty::types(),
            'lists' => ShopItemList::get(),
            'groups' => ShopGroup::get(),
            'shop' => $shop,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Shop $shop)
    {
        return $this->saveShopItemProperty($request, $shop);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop, ShopItemProperty $shopItemProperty)
    {

        $ShopItemPropertyForGroup = new ShopItemPropertyForGroup();

        return view('admin.shop.item.property.edit', [
            'property' => $shopItemProperty,
            "breadcrumbs" => self::breadcrumbs(true),
            'types' => ShopItemProperty::types(),
            'lists' => ShopItemList::get(),
            'groups' => ShopGroup::get(),
            'properties_for_groups' => $ShopItemPropertyForGroup->getGroupsForProperty($shopItemProperty),
            'shop' => $shop,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shop $shop, ShopItemProperty $shopItemProperty)
    {

        return $this->saveShopItemProperty($request, $shop, $shopItemProperty);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop, ShopItemProperty $shopItemProperty)
    {
        $shopItemProperty->delete();

        return redirect()->back()->withSuccess("Свойство было успешно удалено!");
    }

    public function saveShopItemProperty(Request $request, Shop $shop, $shopItemProperty = false)
    {

        if (!$shopItemProperty) {
            $shopItemProperty = new ShopItemProperty();
        }

        $shopItemProperty->name = $request->name;
        $shopItemProperty->type = $request->type;
        $shopItemProperty->destination = $request->destination;
        $shopItemProperty->multiple = $request->multiple ?? 0;
        $shopItemProperty->show_in_item = $request->show_in_item ?? 0;
        $shopItemProperty->show_in_filter = $request->show_in_filter ?? 0;
        $shopItemProperty->shop_item_list_id = $request->type == 4 ? ($request->shop_item_list_id ?? 0) : 0;
        $shopItemProperty->sorting = $request->sorting ?? 0;
        $shopItemProperty->save();

        //доступность в группах
        ShopItemPropertyForGroup::where('shop_item_property_id', $shopItemProperty->id)->delete();

        if (!is_null($request->property_for_group)) {

            foreach ($request->property_for_group as $value) {
                $ShopItemPropertyForGroup = new ShopItemPropertyForGroup();
                $ShopItemPropertyForGroup->shop_group_id = $value;
                $ShopItemPropertyForGroup->shop_item_property_id = $shopItemProperty->id;
                $ShopItemPropertyForGroup->save();
            }
        }

        $message = "Свойство было успешно сохранено!";

        if ($request->apply) {
            return redirect()->to(route('shop.shop-item-property.index', ['shop' => $shop->id]))->withSuccess($message);
        } else {
           return redirect()->back()->withSuccess($message);
        }

    }

    public static function breadcrumbs($lastItemIsLink = false)
    {

        $shop = Shop::get();

        $aResult[1]["name"] = 'Свойства товаров';
        if ($lastItemIsLink) {
            $aResult[1]["url"] = route('shop.shop-item-property.index', ['shop' => $shop->id]);
        }

        return ShopController::breadcrumbs() + $aResult;
    }
}
