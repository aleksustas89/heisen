<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Models\ShopDelivery;
use App\Models\ShopDeliveryFieldValue;
use Illuminate\Http\Request;
use App\Models\CdekSender;
use App\Models\CdekOrder;
use App\Models\CdekDimension;


class ShopOrderController extends Controller
{

    public static $item_on_page = 50;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('admin.shop.order.index', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(),
            'orders' => ShopOrder::orderBy("created_at", "Desc")->paginate(self::$item_on_page),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.shop.order.create', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
            'shopDeliveries' => ShopDelivery::orderBy("sorting", "ASC")->get(),
            'cdekSender' => CdekSender::find(1),
            'CdekDimensions' => CdekDimension::orderBy("sorting", "ASC")->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return self::saveOrder($request);
    }

    public function show()
    {
        return redirect()->to(route("shopOrder.index"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShopOrder $shopOrder)
    {

        $aDeliveryValues = [];
        foreach ($shopOrder->ShopDeliveryFieldValues as $ShopDeliveryFieldValue) {
            $aDeliveryValues[$ShopDeliveryFieldValue->shop_delivery_field_id] = $ShopDeliveryFieldValue->value;
        }

        return view('admin.shop.order.edit', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
            'order' => $shopOrder,
            'shopDeliveries' => ShopDelivery::orderBy("sorting", "ASC")->get(),
            'aDeliveryValues' => $aDeliveryValues,
            'cdekSender' => CdekSender::find(1),
            'CdekOrder' => CdekOrder::where("shop_order_id", $shopOrder->id)->first(),
            'CdekDimensions' => CdekDimension::orderBy("sorting", "ASC")->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopOrder $shopOrder)
    {
       // dd($request);
        return self::saveOrder($request, $shopOrder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopOrder $shopOrder)
    {

        foreach ($shopOrder->shopOrderItems as $shopOrderItem) {
            $shopOrderItem->delete();
        }

        $shopOrder->delete();

        return redirect()->back()->withSuccess('Заказ был успешно удален!');
    }

    public static function saveOrder($request, $shopOrder = false)
    {
        if (!$shopOrder) {
            $shopOrder = new shopOrder();
        }

        $shopOrder->shop_delivery_id = $request->shop_delivery_id;
        $shopOrder->client_id = $request->client_id;
        $shopOrder->shop_payment_system_id = $request->shop_payment_system_id;
        $shopOrder->shop_currency_id = $request->shop_currency_id;
        $shopOrder->name = $request->name;
        $shopOrder->surname = $request->surname;
        $shopOrder->patronymic = $request->patronymic;
        $shopOrder->email = $request->email;
        $shopOrder->phone = $request->phone;
        $shopOrder->description = $request->description;
        $shopOrder->delivery_information = $request->delivery_information;
        $shopOrder->guid = \App\Services\Helpers\Guid::get();

        $shopOrder->weight = $request->weight;
        $shopOrder->length = $request->length;
        $shopOrder->width = $request->width;
        $shopOrder->height = $request->height;

        $shopOrder->cdek_dimension_id = $request->cdek_dimension_id;

        $shopOrder->save();

        //доставка
        if (!is_null($ShopDelivery = ShopDelivery::find($request->shop_delivery_id))) {
            foreach ($ShopDelivery->ShopDeliveryFields as $ShopDeliveryField) {
                $key = "delivery_" . $request->shop_delivery_id . "_" . $ShopDeliveryField->field;
                if (isset($request->$key)) {

                    $ShopDeliveryFieldValue = ShopDeliveryFieldValue::where("shop_order_id", $shopOrder->id)
                                                                        ->where("shop_delivery_field_id", $ShopDeliveryField->id)->first();
                    if (!is_null($ShopDeliveryFieldValue)) {
                        $ShopDeliveryFieldValue->value = $request->$key;
                        $ShopDeliveryFieldValue->save();
                    } else {
                        $ShopDeliveryFieldValue = new ShopDeliveryFieldValue();
                        $ShopDeliveryFieldValue->shop_order_id = $shopOrder->id;
                        $ShopDeliveryFieldValue->shop_delivery_field_id = $ShopDeliveryField->id;
                        $ShopDeliveryFieldValue->value = $request->$key;
                        $ShopDeliveryFieldValue->save();
                    }
                }
            }
        }


        if ($request->apply) {
            return redirect(route("shop-order.index"))->withSuccess('Данные были успешно сохраненны!');
        } else {
            return redirect(route("shop-order.edit", $shopOrder->id))->withSuccess('Данные были успешно сохраненны!');
        }

    }

    public static function breadcrumbs($lastItemIsLink = false)
    {
        $Result[1]["name"] = 'Заказы';
        if ($lastItemIsLink) {
            $Result[1]["url"] = route("shop-order.index");
        }
        
        return $Result;
    }
}
