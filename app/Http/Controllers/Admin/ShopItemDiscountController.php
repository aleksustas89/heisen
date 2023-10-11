<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopItemDiscount;
use App\Models\ShopDiscount;
use App\Models\ShopItem;
use Illuminate\Http\Request;

class ShopItemDiscountController extends Controller
{

    public static $items_on_page = 15;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $ShopItem = ShopItem::find($request->shop_item_id);
        $ParentShopItem = $ShopItem->parentItemIfModification();

        $breadcrumbs = ShopGroupController::breadcrumbs($ParentShopItem->shop_group_id > 0 ? $ParentShopItem->ShopGroup : false, [], true);

        foreach (self::breadcrumbs($ShopItem) as $breadcrumb) {
            $breadcrumbs[] = $breadcrumb;
        }

        return view('admin.shop.item.discount.index', [
            'breadcrumbs' => $breadcrumbs,
            'itemDiscounts' => ShopItemDiscount::where("shop_item_id", $request->shop_item_id)->paginate(self::$items_on_page),
            'types' => ShopDiscount::getTypes(),
            'shop_item_id' => $request->shop_item_id,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        $shopItem = ShopItem::find($request->shop_item_id);

        $breadcrumbs = ShopGroupController::breadcrumbs($shopItem->shop_group_id > 0 ? $shopItem->ShopGroup : false, [], true);

        foreach (self::breadcrumbs($shopItem, true) as $breadcrumb) {
            $breadcrumbs[] = $breadcrumb;
        }

        return view('admin.shop.item.discount.create', [
            'breadcrumbs' => $breadcrumbs,
            'shopDiscounts' => ShopDiscount::get(),
            'shopItem' => $shopItem,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->save($request);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, ShopItemDiscount $shopItemDiscount)
    {

        $shopItem = ShopItem::find($shopItemDiscount->shop_item_id);
        $ParentShopItem = $shopItem->parentItemIfModification();

        $breadcrumbs = ShopGroupController::breadcrumbs($ParentShopItem->shop_group_id > 0 ? $ParentShopItem->ShopGroup : false, [], true);

        foreach (self::breadcrumbs($shopItem, true) as $breadcrumb) {
            $breadcrumbs[] = $breadcrumb;
        }

        return view('admin.shop.item.discount.edit', [
            'breadcrumbs' => $breadcrumbs,
            'shopDiscounts' => ShopDiscount::get(),
            'ItemDiscount' => $shopItemDiscount,
            'shop_item_id' => $request->shop_item_id,
    
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopItemDiscount $shopItemDiscount)
    {
        return $this->save($request, $shopItemDiscount);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopItemDiscount $shopItemDiscount)
    {

        $shopItem = $shopItemDiscount->ShopItem->parentItemIfModification();

        $shopItemDiscount->delete();

        $this->checkItemDiscounts($shopItem);
        
        return redirect()->back()->withSuccess("Скидка была успешно удалена!");
    }

    public function saveShopItemDiscount($ShopItemDiscount = false, ShopDiscount $ShopDiscount, ShopItem $ShopItem)
    {
        if (!$ShopItemDiscount) {
            $ShopItemDiscount = new ShopItemDiscount();
        }

        $ShopItemDiscount->shop_discount_id = $ShopDiscount->id;
        $ShopItemDiscount->shop_item_id = $ShopItem->id;
        $ShopItemDiscount->value = \App\Http\Controllers\ShopDiscountController::getPriceApplyDiscount($ShopItem, $ShopDiscount);
        $ShopItemDiscount->save();
    }

    public function save(Request $request, $shopItemDiscount = false)
    {

        if (!$shopItemDiscount) {
            $shopItemDiscount = ShopItemDiscount::where("shop_item_id", $request->shop_item_id)
                ->where("shop_discount_id", $request->shop_item_discount_id)->first();
        }

        switch ($request->action) {
            case '1':

                if (is_null($shopItemDiscount)) {

                    $ShopItem = ShopItem::find($request->shop_item_id);
   
                    $this->saveShopItemDiscount($ShopItemDiscount = false, ShopDiscount::find($request->shop_item_discount_id), $ShopItem);

                    $this->checkItemDiscounts($ShopItem->parentItemIfModification());
    
                    $message = "Скидка была успешно добавлена к товару";
    
                    if ($request->apply) {
                        return redirect()->to(route("shopItemDiscount.index") . "?shop_item_id=" . $request->shop_item_id)->withSuccess($message);
                    } else {
                        return redirect()->back()->withSuccess($message);
                    }
                
                } else {
                    return redirect()->back()->withError("Скидка уже добавлена к товару!");
                }


            break;
            case '2':

                $ShopDiscount = ShopDiscount::find($request->shop_item_discount_id);

                foreach (ShopItem::where("modification_id", $request->shop_item_id)->get() as $ShopItem) {
                    
                    if (is_null(ShopItemDiscount::where("shop_discount_id", $request->shop_item_discount_id)->where("shop_item_id", $ShopItem->id)->first())) {
        
                        $this->saveShopItemDiscount($ShopItemDiscount = false, $ShopDiscount, $ShopItem);
                    }
                }

                $this->checkItemDiscounts(ShopItem::find($request->shop_item_id));

                $message = "Скидка была успешно добавлена к модификациям товара."; 

                if ($request->apply) {
                    return redirect()->to(route("shopItemDiscount.index") . "?shop_item_id=" . $request->shop_item_id)->withSuccess($message);
                } else {
                    return redirect()->back()->withSuccess($message);
                }

            break;
            case '3':

                if (!is_null($shopItemDiscount)) {

                    $ShopItem = $shopItemDiscount->ShopItem->parentItemIfModification();

                    $shopItemDiscount->delete();

                    $this->checkItemDiscounts($ShopItem);

                    $message = "Скидка была успешно отменена!";

                    return redirect()->to(route("shopItemDiscount.index") . "?shop_item_id=" . $request->shop_item_id)->withSuccess($message);

                } else {
                    return redirect()->back()->withError("Скидка не может отменена, так-как не была прикреплена к товару!");
                }


            break;
            case '4':
                
                $ShopItemDiscounts = ShopItemDiscount::select("shop_item_discounts.*")
                    ->join("shop_items", "shop_items.id", "=", "shop_item_discounts.shop_item_id")
                    ->where("shop_items.modification_id", $request->shop_item_id)
                    ->get();

                foreach ($ShopItemDiscounts as $oShopItemDiscount) {
                    $oShopItemDiscount->delete();
                }

                $this->checkItemDiscounts(ShopItem::find($request->shop_item_id));

                $message = "Скидки были успешно отменены у модификаций товара";

                if ($request->apply) {
                    return redirect()->to(route("shopItemDiscount.index") . "?shop_item_id=" . $request->shop_item_id)->withSuccess($message);
                } else {
                    return redirect()->back()->withSuccess($message);
                }

            break;
            
        }

    }

    public function checkItemDiscounts(ShopItem $ShopItem)
    {
        $shopItemDiscountCount = ShopItemDiscount::where("shop_item_id", $ShopItem->id)
                                    ->orWhereIn("shop_item_id", function ($query) use ($ShopItem) {
                                        $query->selectRaw('shop_items.id')->from('shop_items')->where('modification_id', $ShopItem->id);
                                    })
                                    ->count();
        if ($shopItemDiscountCount > 0) {
            $ShopItem->discounts = 1;
        } else {
            $ShopItem->discounts = 0;
        }

        $ShopItem->save();
    }

    public static function breadcrumbs($ShopItem, $lastItemIsLink = false)
    {

        $aResult = [];

        if ($ShopItem->modification_id > 0) {

            $ParentShopItem = $ShopItem->parentItemIfModification();

            $Result["name"] = 'Модификации товара - ' . $ParentShopItem->name;
            $Result["url"] = route("modification.index") . "?shop_item_id=" . $ParentShopItem->id;

            $aResult[] = $Result;

        }

        $Result = [];

        $Result["name"] = 'Скидки товара - ' . $ShopItem->name;
        if ($lastItemIsLink) {
            $Result["url"] = route("shopItemDiscount.index") . "?shop_item_id=" . $ShopItem->id;
        }

        $aResult[] = $Result;
        
        return $aResult;
    }
}
