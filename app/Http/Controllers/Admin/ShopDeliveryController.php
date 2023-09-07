<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopDelivery;
use Illuminate\Http\Request;

class ShopDeliveryController extends Controller
{

    public static $items_on_page = 15;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.shop.delivery.index', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(),
            'deliveries' => ShopDelivery::paginate(self::$items_on_page)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.shop.delivery.create', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
    */
    public function edit(ShopDelivery $shopDelivery)
    {
        return view('admin.shop.delivery.edit', [
            'shopDelivery' => $shopDelivery,
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
        ]);
    }

    /**
     * Update the specified resource in storage.
    */
    public function update(Request $request, ShopDelivery $shopDelivery)
    {

        return $this->saveDelivery($request, $shopDelivery);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->saveDelivery($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopDelivery $shopDelivery)
    {

        foreach ($shopDelivery->ShopDeliveryFields as $ShopDeliveryField) {
            $ShopDeliveryField->delete();
        }

        $shopDelivery->delete();

        return redirect()->back()->withSuccess("Доставка была успешно удалена!");
    }

    public function saveDelivery(Request $request, $shopDelivery = false)
    {

        
        if (!$shopDelivery) {
            $shopDelivery = new ShopDelivery();
        }

        $shopDelivery->name = $request->name;
        $shopDelivery->description = $request->description;
        $shopDelivery->sorting = $request->sorting;
        $shopDelivery->color = $request->color;
        $shopDelivery->save();

        $text = 'Данные были успешно сохраненны!';

        if ($request->apply) {
            return redirect(route("shopDelivery.index"))->withSuccess($text);
        } else {
            return redirect()->back()->withSuccess($text);
        }
    }

    public static function breadcrumbs($lastItemIsLink = false)
    {
        $Result[1]["name"] = 'Доставки';
        if ($lastItemIsLink) {
            $Result[1]["url"] = route("shopDelivery.index");
        }
        
        return $Result;
    }
}
