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
    public function index()
    {
        return view('admin.shop.discount.index', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(),
            'discounts' => ShopDiscount::paginate(self::$items_on_page),
            'types' => ShopDiscount::getTypes(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.shop.discount.create', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
            'types' => ShopDiscount::getTypes(),
            'propertys' => $this->getShopItemPropertyLists(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->saveDiscount($request);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShopDiscount $shopDiscount)
    {
        return view('admin.shop.discount.edit', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
            'discount' => $shopDiscount,
            'types' => ShopDiscount::getTypes(),
            'propertys' => $this->getShopItemPropertyLists(),
            'ShopItems' => ShopItem::select("shop_items.*")
                            ->join("shop_item_discounts", "shop_item_discounts.shop_item_id", "=", "shop_items.id")
                            ->where("shop_item_discounts.shop_discount_id", $shopDiscount->id)
                            ->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopDiscount $shopDiscount)
    {
        return $this->saveDiscount($request, $shopDiscount);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopDiscount $shopDiscount)
    {

        foreach ($shopDiscount->ShopItemDiscount as $ShopItemDiscount) {
            $ShopItemDiscount->delete();
        }

        $shopDiscount->delete();

        return redirect()->back()->withSuccess("Скидка была успешно удалена!");
    }

    public function saveDiscount(Request $request, $shopDiscount = false)
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

        if ($request->applied && count($request->applied) > 0) {

            $ShopItemDiscountController = new ShopItemDiscountController();

            foreach ($request->applied as $value) {

                if (!is_null($ShopItem = ShopItem::find($value))) {
                    $ShopItemDiscountController->saveShopItemDiscount($ShopItemDiscount = false, $shopDiscount, $ShopItem);
                }
            }
        }

        $message = "Скидка была успешно сохранена!";

        if ($request->apply) {
            return redirect()->to(route("shopDiscount.index"))->withSuccess($message);
        } else {
            return redirect()->back()->withSuccess($message);
        }
    }

    public static function breadcrumbs($lastItemIsLink = false)
    {
        $Result[1]["name"] = 'Скидки';
        if ($lastItemIsLink) {
            $Result[1]["url"] = route("shopDiscount.index");
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

        $ShopItem = new ShopItem();

        if (!empty($request->shop_item_name)) {
            $ShopItem->where("name", $request->shop_item_name);
        }

        if ($request->shop_group_id > 0) {
            $ShopItem->where("shop_group_id", $request->shop_group_id);
        }

        return response()->view("admin.shop.discount.filter-items", ["ShopItems" => $ShopItem->get()]);
    }
}
