<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopDeliveryField;
use App\Models\ShopDelivery;
use Illuminate\Http\Request;
use App\Models\Shop;

class ShopDeliveryFieldController extends Controller
{

    public static $items_on_page = 15;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Shop $shop)
    {
        if ($request->shop_delivery_id) {
            return view('admin.shop.delivery.field.index', [
                'breadcrumbs' => ShopController::breadcrumbs() + ShopDeliveryController::breadcrumbs(true) + self::breadcrumbs(false, $request->shop_delivery_id),
                'fields' => ShopDeliveryField::where("shop_delivery_id", $request->shop_delivery_id)->paginate(self::$items_on_page),
                "shop_delivery_id" => $request->shop_delivery_id,
                'types' => ShopDeliveryField::getTypes(),
                'shop' => $shop
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Shop $shop)
    {
        return view('admin.shop.delivery.field.create', [
            'breadcrumbs' => ShopController::breadcrumbs() + ShopDeliveryController::breadcrumbs(true) + self::breadcrumbs(true, $request->shop_delivery_id),
            'types' => ShopDeliveryField::getTypes(),
            'shop_delivery_id' => $request->shop_delivery_id,
            'shop' => $shop
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Shop $shop)
    {
        return self::saveShopDeliveryField($request, $shop);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop, ShopDeliveryField $shopDeliveryField)
    {
        return view('admin.shop.delivery.field.edit', [
            'breadcrumbs' => ShopController::breadcrumbs() + ShopDeliveryController::breadcrumbs(true) + self::breadcrumbs(true, $shopDeliveryField->shop_delivery_id),
            'types' => ShopDeliveryField::getTypes(),
            'field' => $shopDeliveryField,
            'shop' => $shop,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shop $shop, ShopDeliveryField $shopDeliveryField)
    {
        return self::saveShopDeliveryField($request, $shop, $shopDeliveryField);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop, ShopDeliveryField $shopDeliveryField)
    {

        $shopDeliveryField->delete();

        return redirect()->back()->withSuccess("Поле было успешно удалено!");
    }

    public static function breadcrumbs($lastItemIsLink = false, $shop_delivery_id)
    {
        $shop = Shop::get();

        $name = 'Поля доставки';
        if ($shop_delivery_id) {
            $shopDelivery = ShopDelivery::find($shop_delivery_id);
            if (!is_null($shopDelivery->id)) {
                $name .= " '" . $shopDelivery->name . "'";
            }  
        }

        $Result[2]["name"] = $name;
        if ($lastItemIsLink) {
            $Result[2]["url"] = route("shop.shop-delivery-field.index", ['shop' => $shop->id]) . "?shop_delivery_id=" . $shop_delivery_id;
        }
        
        return $Result;
    }

    public function saveShopDeliveryField(Request $request, $shop, $shopDeliveryField = false)
    {
        if ($shopDeliveryField || (!$shopDeliveryField &&  self::checkDelivery($request->shop_delivery_id))) {
            if (!$shopDeliveryField) {
                $shopDeliveryField = new ShopDeliveryField();
                $shopDeliveryField->shop_delivery_id = $request->shop_delivery_id;
            }
    
            $shopDeliveryField->field = $request->field;
            $shopDeliveryField->caption = $request->caption;
            $shopDeliveryField->type = $request->type;
            $shopDeliveryField->sorting = $request->sorting;
    
            $shopDeliveryField->save();
    
            $text = 'Данные были успешно сохраненны!';
    
            if ($request->apply) {
                return redirect(route("shop.shop-delivery-field.index", ['shop' => $shop->id]) . "?shop_delivery_id=" . $shopDeliveryField->shop_delivery_id)->withSuccess($text);
            } else {
                return redirect()->back()->withSuccess($text);
            }
        }
    }

    protected static function checkDelivery($shop_delivery_id)
    {
        if (!is_null($shopDelivery = ShopDelivery::find($shop_delivery_id))) {
            return true;
        }

        return false;
    }
}
