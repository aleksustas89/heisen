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
use App\Models\Boxberry;
use App\Models\BoxberrySender;
use App\Models\Client;
use App\Models\CdekOffice;


class ShopOrderController extends Controller
{

    public static $item_on_page = 100;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $ShopOrder = ShopOrder::orderBy("id", "Desc")->where("deleted", 0);

        if ($request->global_search) {
            $ShopOrder
                ->where("id", $request->global_search)
                ->orWhere("name", "LIKE", "%" . $request->global_search . "%")
                ->orWhere("surname", "LIKE", "%" . $request->global_search . "%")
                ->orWhere("email", $request->global_search)
                ->orWhere("phone", $request->global_search);
        }

        return view('admin.shop.order.index', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(),
            'orders' => $ShopOrder->paginate(self::$item_on_page),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.shop.order.create', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
            'shopDeliveries' => ShopDelivery::where("deleted", 0)->orderBy("sorting", "ASC")->get(),
            'cdekSender' => CdekSender::find(1),
            'CdekDimensions' => CdekDimension::orderBy("sorting", "ASC")->get(),
            "Boxberry" => Boxberry::find(1),
            "sCdekSender" => $this->getCdekSenderData()
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

    public function getCdekSender() 
    {
        return CdekSender::find(1);
    }

    public function getCdekSenderData() {

        $cdekSender = $this->getCdekSender();

        $sCdekSender = '<div>Город отправления: ' . $cdekSender->CdekCity->name .'</div>';
        $sCdekSender .= '<div>Тип: ' . CdekSender::$Types[$cdekSender->type]["name"] .'</div>';
        if (!is_null($cdekSender->CdekOffice) && $cdekSender->type == 0 && $cdekSender->cdek_office_id > 0) {
            $sCdekSender .= '<div>Офис: ' . $cdekSender->CdekOffice->name .'</div>';
        }
        if ($cdekSender->type == 1 && !empty($cdekSender->address)) {
            $sCdekSender .= '<div>Адрес: ' . $cdekSender->address .'</div>';
        }
        if (!empty($cdekSender->name)) {
            $sCdekSender .= '<div>Имя отправителя: ' . $cdekSender->name .'</div>';
        }

        return $sCdekSender;

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

        $BoxberrySender = BoxberrySender::find(1);

        $nextOrder = ShopOrder::where("id", ">", $shopOrder->id)->limit(1)->first();
        $prevOrder = ShopOrder::where("id", "<", $shopOrder->id)->orderBy("id", "DESC")->limit(1)->first();

        return view('admin.shop.order.edit', [
            'breadcrumbs' => ShopController::breadcrumbs() + self::breadcrumbs(true),
            'order' => $shopOrder,
            'shopDeliveries' => ShopDelivery::orderBy("sorting", "ASC")->get(),
            'aDeliveryValues' => $aDeliveryValues,
            'cdekSender' => $this->getCdekSender(),
            'CdekOrder' => CdekOrder::where("shop_order_id", $shopOrder->id)->first(),
            'CdekDimensions' => CdekDimension::orderBy("sorting", "ASC")->get(),
            "Boxberry" => Boxberry::find(1),
            "sCdekSender" => $this->getCdekSenderData(),
            "sBoxberrySender" => $BoxberrySender->name,
            "CdekOffices" => CdekOffice::get(),

            'nextOrder' => !is_null($nextOrder) ? route('shop-order.edit', $nextOrder->id) : '',
            'prevOrder' => !is_null($prevOrder) ? route('shop-order.edit', $prevOrder->id) : ''
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopOrder $shopOrder)
    {
        return self::saveOrder($request, $shopOrder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopOrder $shopOrder)
    {

        $shopOrder->deleted = 1;
        $shopOrder->save();

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
                //if (isset($request->$key)) {

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
                //}
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

    public function getClients(Request $request)
    {

        $aResult = [];


        if (!empty($term = $request->input('term'))) {

            foreach (Client::where('name', "like", "%" . $term . "%")
                        ->orWhere('surname', "like", "%" . $term . "%")
                        ->orWhere('email', "like", "%" . $term . "%")
                        ->orderBy("email", "DESC")->get() as $Client) {

                $aResult[] = ["value" => $Client->surname . " " . $Client->name . " [" . $Client->id . "]", "data" => $Client->id];
            }
        }

        return response()->json($aResult);
    }

    public function getOrders(Request $request)
    {

        $aResult["query"] = '';
        $aResult["suggestions"] = '';

        if (!empty($query = $request->input('query')) && $request->current_order != $query) {

            $aResult["query"] = $query;

            $items = [];

            foreach (ShopOrder::where('id', "like", "%" . $query . "%")->orderBy("id", "DESC")->get() as $ShopOrder) {

                $items[] = ["value" => "$ShopOrder->id", "data" =>  route('shop-order.edit', $ShopOrder->id)];
            }

            $aResult["suggestions"] = $items;
        }

        return response()->json($aResult);
    }
}
