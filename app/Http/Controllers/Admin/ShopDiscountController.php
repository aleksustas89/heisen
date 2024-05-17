<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopDiscount;
use App\Models\ShopItemDiscount;
use App\Models\PropertyValueInt;
use App\Models\ShopItemProperty;
use Illuminate\Http\Request;
use App\Models\ShopItemListItem;
use App\Models\ShopItem;
use App\Models\Shop;
use App\Http\Controllers\Admin\ShopItemDiscountController;

class ShopDiscountController extends Controller
{

    public static $items_on_page = 15;

    public function getShopItemPropertyLists()
    {
        return ShopItemProperty::where("type", 4)->get();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Shop $shop, Request $request)
    {

        $ShopDiscounts = ShopDiscount::orderBy("end_datetime", "Desc")->orderBy("id", "Desc");

        if ($request->global_search) {

            $ShopDiscounts->where("name", "LIKE", "%". trim($request->global_search) ."%");

            $SearchByItemName = ShopItemDiscount::join("shop_items", "shop_items.id", "=", "shop_item_discounts.shop_item_id")
                ->join("shop_item_language_entities", "shop_item_language_entities.shop_item_id", "=", "shop_items.modification_id")
                ->join("language_value_strings", "language_value_strings.language_entity_id", "=", "shop_item_language_entities.language_entity_id")
                ->where("language_value_strings.value", "LIKE", "%" . "Наом" . "%")
                ->groupBy("shop_item_discounts.shop_discount_id")
                ->get();

            $ids = [];

            foreach ($SearchByItemName as $ShopDiscount) {
                $ids[] = $ShopDiscount->shop_discount_id;
            }

            if (count($ids) > 0) {
                $ShopDiscounts->orWhereIn("id", $ids);
            }
        }

        return view('admin.shop.discount.index', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(),
            'discounts' => $ShopDiscounts->paginate(self::$items_on_page),
            'types' => ShopDiscount::getTypes(),
            'shop' => $shop,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Shop $shop)
    {
        return view('admin.shop.discount.create', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
            'types' => ShopDiscount::getTypes(),
            'propertys' => $this->getShopItemPropertyLists(),
            'shop' => $shop
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Shop $shop)
    {
        return $this->saveDiscount($request, $shop);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop, ShopDiscount $shopDiscount)
    {
        return view('admin.shop.discount.edit', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
            'discount' => $shopDiscount,
            'types' => ShopDiscount::getTypes(),
            'propertys' => $this->getShopItemPropertyLists(),
            'ShopItems' => ShopItem::select("shop_items.*")
                            ->join("shop_item_discounts", "shop_item_discounts.shop_item_id", "=", "shop_items.id")
                            ->where("shop_item_discounts.shop_discount_id", $shopDiscount->id)
                            ->get(),
            'shop' => $shop
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shop $shop, ShopDiscount $shopDiscount)
    {
        return $this->saveDiscount($request, $shop, $shopDiscount);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop, ShopDiscount $shopDiscount)
    {

        foreach ($shopDiscount->ShopItemDiscounts as $ShopItemDiscount) {
            $ShopItemDiscount->delete();
        }

        $shopDiscount->delete();

        return redirect()->back()->withSuccess("Скидка была успешно удалена!");
    }

    public function saveDiscount(Request $request, Shop $shop, $shopDiscount = false)
    {
        if (!$shopDiscount) {
            $shopDiscount = new shopDiscount();
        }

        $shopDiscount->name = $request->name;
        $shopDiscount->description = $request->description;
        $shopDiscount->start_datetime = date("Y-m-d H:i:s", strtotime($request->start_datetime));
        $shopDiscount->end_datetime = date("Y-m-d H:i:s", strtotime($request->end_datetime));
        $shopDiscount->active = $request->active;
        $shopDiscount->value = $request->value;
        $shopDiscount->type = $request->type;

        $shopDiscount->save();

        ShopItemDiscount::where('shop_discount_id', $shopDiscount->id)->delete();

        $ShopItemDiscountController = new ShopItemDiscountController();

        $itemsToSave = [];

        if ($request->applied && count($request->applied) > 0) {

            foreach ($request->applied as $value) {

                $itemsToSave[] = $value;
            }
        }

        if ($request->apply_discount && count($request->apply_discount) > 0) {

            foreach ($request->apply_discount as $value) {

                $itemsToSave[] = $value;
            }
        }

        foreach (array_unique($itemsToSave) as $id) {
            if (!is_null($ShopItem = ShopItem::find($id))) {
                $ShopItemDiscountController->saveShopItemDiscount($ShopItemDiscount = false, $shopDiscount, $ShopItem);
            }
        }

        $message = "Скидка была успешно сохранена!";

        if ($request->apply) {
            return redirect()->to(route("shop.shop-discount.index", ['shop' => $shop->id]))->withSuccess($message);
        } else {
            return redirect()->back()->withSuccess($message);
        }
    }

    public static function breadcrumbs($lastItemIsLink = false)
    {

        $shop = Shop::get();

        $Result[1]["name"] = 'Скидки';
        if ($lastItemIsLink) {
            $Result[1]["url"] = route("shop.shop-discount.index", ['shop' => $shop->id]);
        }
        
        return $Result;
    }

    public function listValues(Request $request)
    {

        $Result = '';

        if ($request->total_list_id) {
            foreach(ShopItemListItem::where("shop_item_list_id", $request->total_list_id)->where("active", 1)->orderBy("sorting", "ASC")->get() as $ShopItemListItem) {
                $Result .= "<option value=". $ShopItemListItem->id .">". $ShopItemListItem->value ."</option>";
            }
        }

        return response()->json($Result);
        
    }

    public function filter(Request $request)
    {

        $ShopItems = ShopItem::select("shop_items.*")->where("active", 1)->where("modification_id", 0);

        if (!empty($request->shop_item_name)) {
            $ShopItems->where("name", "LIKE", "%" . trim($request->shop_item_name) . "%");
        }

        if ($request->shop_group_id > 0) {
            $ShopItems->where("shop_group_id", $request->shop_group_id);
        }

        $aShopItems = [];
        $oShopItems = $ShopItems->get();
        foreach ($oShopItems as $Item) {
            $aShopItems[] = $Item->id;
        }

        $Modifications = ShopItem::select("shop_items.id")->where("active", 1)->whereIn("modification_id", $aShopItems);

        if (!empty($request->total_list_value) && !empty($request->total_list_id)) { 
            $Modifications->join("property_value_ints", "property_value_ints.entity_id", "=", "shop_items.id")
                            ->where("property_value_ints.property_id", $request->total_list_id)
                            ->where("property_value_ints.value", $request->total_list_value);
        }

        $aModifications = [];
        foreach ($Modifications->get() as $Item) {
            $aModifications[] = $Item->id;
        }


        return response()->view("admin.shop.discount.filter-items", [
            "ShopItems" => $oShopItems,
            "aModifications" => $aModifications,
        ]);
    }
}
