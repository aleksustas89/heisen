<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopDiscount;
use Illuminate\Http\Request;

class ShopDiscountController extends Controller
{

    public static $items_on_page = 15;

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
}
