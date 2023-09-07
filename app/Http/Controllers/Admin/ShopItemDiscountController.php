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
            'shop_item_id' => $request->shop_item_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->saveShopItemDiscount($request);
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
        return $this->saveShopItemDiscount($request, $shopItemDiscount);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopItemDiscount $shopItemDiscount)
    {
        $shopItemDiscount->delete();

        return redirect()->back()->withSuccess("Скидка была успешно удалена!");
    }

    public function saveShopItemDiscount(Request $request, $shopItemDiscount = false)
    {

        if (!$shopItemDiscount) {
            $shopItemDiscount = ShopItemDiscount::where("shop_item_id", $request->shop_item_id)->where("shop_discount_id", $request->shop_item_discount_id)->first();
        }

        switch ($request->action) {
            case '1':

                if (is_null($shopItemDiscount)) {
    
                    $ShopItemDiscount = new ShopItemDiscount();
                    $ShopItemDiscount->shop_discount_id = $request->shop_item_discount_id;
                    $ShopItemDiscount->shop_item_id = $request->shop_item_id;
                    $ShopItemDiscount->save();
        
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

                foreach (ShopItem::where("modification_id", $request->shop_item_id)->get() as $ShopItem) {
                    
                    if (is_null($ShopItemDiscount = ShopItemDiscount::where("shop_discount_id", $request->shop_item_discount_id)->where("shop_item_id", $ShopItem->id)->first())) {
                        $ShopItemDiscount = new ShopItemDiscount();
                        $ShopItemDiscount->shop_discount_id = $request->shop_item_discount_id;
                        $ShopItemDiscount->shop_item_id = $ShopItem->id;
                        $ShopItemDiscount->save();
                    }
                }

                $message = "Скидка была успешно добавлена к модификациям товара."; 

                if ($request->apply) {
                    return redirect()->to(route("shopItemDiscount.index") . "?shop_item_id=" . $request->shop_item_id)->withSuccess($message);
                } else {
                    return redirect()->back()->withSuccess($message);
                }

            break;
            case '3':

                if (!is_null($shopItemDiscount)) {
                    $shopItemDiscount->delete();

                    $message = "Скидка была успешно отменена!";

                    return redirect()->to(route("shopItemDiscount.index") . "?shop_item_id=" . $request->shop_item_id)->withSuccess($message);

                } else {
                    return redirect()->back()->withError("Скидка не может отменена, так-как не была прикреплена к товару!");
                }


            break;
            case '4':
                
                $ShopItemDiscounts = ShopItemDiscount::select("shop_item_discounts.*")
                    ->join("shop_items", "shop_items.id", "=", "shop_item_discounts.shop_item_id")
                    ->where("shop_items.modification_id", $shopItemDiscount->shop_item_id)
                    ->get();

                foreach ($ShopItemDiscounts as $ShopItemDiscount) {
                    $ShopItemDiscount->delete();
                }

                $message = "Скидки были успешно отменена у модификаций товара";

                if ($request->apply) {
                    return redirect()->to(route("shopItemDiscount.index") . "?shop_item_id=" . $request->shop_item_id)->withSuccess($message);
                } else {
                    return redirect()->back()->withSuccess($message);
                }

            break;
            
        }

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
