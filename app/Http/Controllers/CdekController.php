<?php

namespace App\Http\Controllers;
use App\Models\Cdek;
use App\Models\ShopOrder;
use App\Models\CdekSender;
use App\Models\ShopDeliveryFieldValue;
use App\Models\CdekOrder;
use App\Models\CdekOffice;
use App\Models\CdekCity;
use Illuminate\Http\Request;

class CdekController extends Controller
{

    public $Cdek = NULL;

    public function __construct()
    {
        $this->Cdek = Cdek::find(1);

        $this->Token();
    }

    /**
     * @return Cdek with fresh token
    */
    public function Token()
    {
        if (strtotime(date("Y-m-d H:i:s")) > strtotime('+1 hour', strtotime($this->Cdek->updated_at))) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.cdek.ru/v2/oauth/token?grant_type=client_credentials&client_id=' . $this->Cdek->client_id . '&client_secret=' . $this->Cdek->client_secret,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'client_id: ' . $this->Cdek->client_id,
                'client_secret: ' . $this->Cdek->client_secret
            ),
            ));
            
            $response = curl_exec($curl);

            $response = json_decode($response);
            
            curl_close($curl);
    

            if (isset($response->access_token)) {
                $this->Cdek->token = $response->access_token;
                $this->Cdek->save();
            }

            curl_close($curl);
        }
    }

    public function getRegions()
    {


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.cdek.ru/v2/location/regions?country_codes=RU',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->Cdek->token
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response);

        curl_close($curl);

        return !isset($response->requests->errors) ? $response : false;
    }

    public function getCities()
    {
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.cdek.ru/v2/location/cities?country_codes=RU',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->Cdek->token
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response);

        curl_close($curl);

        return !isset($response->requests->errors) ? $response : false;
    }

    public function getOffices($city_code)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.cdek.ru/v2/deliverypoints?city_code=' . $city_code,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $this->Cdek->token
          ),
        ));
        
        $response = curl_exec($curl);
        $response = json_decode($response);

        //dd($response);
        
        curl_close($curl);

        return !isset($response->requests->errors) ? $response : false;
    }

    public function getTariffCode(ShopOrder $ShopOrder, CdekSender $CdekSender)
    {
        $aSenderTypes = CdekSender::$Types[$CdekSender->type];
        /*тип*/
        $ShopDeliveryFieldValue = ShopDeliveryFieldValue::where("shop_delivery_field_id", 14)->where("shop_order_id", $ShopOrder->id)->first();

        $aReceive = [];
        switch ($ShopDeliveryFieldValue->value) {
            case 11:
                $aReceive = [136, 138];
            break;
            case 15:
                $aReceive = [137, 139];
            break;
        }

        foreach ($aSenderTypes["cdek_tariff_codes"] as $aSenderType) {
            if (in_array($aSenderType, $aReceive)) {
                return $aSenderType;
            }
        }
    }

    /**
     * return ShopOrder with filled options
     * or ''
    */
    public function createOrder(ShopOrder $ShopOrder, CdekSender $CdekSender, $step = 0)
    {

        if (is_null($CdekOrder = CdekOrder::where("shop_order_id", $ShopOrder->id)->first())) {
            $aData = [];

            $aData["number"] = "Заказ № " . $ShopOrder->id;
            $aData["comment"] = "Заказ № " . $ShopOrder->id;

            $TariffCode = $this->getTariffCode($ShopOrder, $CdekSender);

            //со склада
            if (in_array($TariffCode, [136, 137])) {
                $aData["shipment_point"] = $CdekSender->CdekOffice->code;
            }
    
            //на склад
            if (in_array($TariffCode, [136, 138])) {
                if (!is_null($ShopDeliveryFieldValue = ShopDeliveryFieldValue::where("shop_delivery_field_id", 17)->where("shop_order_id", $ShopOrder->id)->first())) {
                    if (!is_null($CdekOffice = CdekOffice::find($ShopDeliveryFieldValue->value))) {
                        $aData["delivery_point"] = $CdekOffice->code;
                    }
                }                
            }
    
            //с двери
            if (in_array($TariffCode, [138, 139])) {
                $aData["from_location"] = [];
                $aData["from_location"]["code"] = $CdekSender->cdek_city_id;
                $aData["from_location"]["city"] = $CdekSender->CdekCity->name;
                $aData["from_location"]["address"] = $CdekSender->address;
            }
    
            //до двери
            if (in_array($TariffCode, [137, 139])) {
                $aData["to_location"] = [];

                if (!is_null($ShopDeliveryFieldValue = ShopDeliveryFieldValue::where("shop_delivery_field_id", 16)->where("shop_order_id", $ShopOrder->id)->first())) {
                    if (!is_null($CdekCity = CdekCity::find($ShopDeliveryFieldValue->value))) {
                        $aData["to_location"]["code"] = $CdekCity->id;
                        $aData["to_location"]["city"] = $CdekCity->name;
                    }
                }

                if (!is_null($ShopDeliveryFieldValue = ShopDeliveryFieldValue::where("shop_delivery_field_id", 15)->where("shop_order_id", $ShopOrder->id)->first())) {
                    $aData["to_location"]["address"] = $ShopDeliveryFieldValue->value;
                }
                
            }
    
            $aData["recipient"]["name"] = implode(" ", [$ShopOrder->surname, $ShopOrder->name]);
    
            $number["number"] = preg_replace('![^0-9\+]+!', '', $ShopOrder->phone);;
    
            $aData["recipient"]["phones"][] = $number;
            
            $aData["sender"]["name"] = $CdekSender->name;
            $aData["tariff_code"] = $TariffCode;
    
            $package = [];
            $package["number"] = "order-" . $ShopOrder->id;

           if (!is_null($CdekDimension = $ShopOrder->CdekDimension)) {

                $package["weight"] = (int) $ShopOrder->CdekDimension->weight;
                $service['code'] = $CdekDimension->box_name;
                $service['parameter'] = 1;
                $aData["services"][] = $service;
                
           } else if ($ShopOrder->weight > 0 && $ShopOrder->width > 0 && $ShopOrder->height > 0 && $ShopOrder->length > 0) {

                $package["weight"] = (int) $ShopOrder->weight;
                $package["width"] = (int) $ShopOrder->width / 10;
                $package["height"] = (int) $ShopOrder->height / 10;
                $package["length"] = (int) $ShopOrder->length / 10;
           }

       


    
            foreach ($ShopOrder->ShopOrderItems as $ShopOrderItem) {
                
                $ShopItem = $ShopOrderItem->ShopItem->parentItemIfModification();
    
                $OrderItem = [];
                $OrderItem["ware_key"] = $ShopOrderItem->shop_item_id;
                $OrderItem["payment"]["value"] = request()->cash_on_delivery == 1 ? $ShopOrderItem->price : 0;
                $OrderItem["name"] = $ShopOrderItem->ShopItem->name;
                $OrderItem["cost"] = $ShopOrderItem->price;
                $OrderItem["amount"] = (int) $ShopOrderItem->quantity;
                $OrderItem["weight"] = (int) $ShopItem->weight;
                $OrderItem["url"] = "www.". env("APP_NAME") . $ShopItem->url;
    
                $package["items"][] = $OrderItem;
                
            }

            if (request()->delivery_price > 0) {
                $aData["delivery_recipient_cost"]["value"] = request()->delivery_price;
            }
    
            $aData["packages"][] = $package;

           // dd($aData);

            $curl = curl_init();
    
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.cdek.ru/v2/orders',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($aData, JSON_UNESCAPED_UNICODE),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $this->Cdek->token,
                    'Content-Type: application/json'
                ),
            ));
    
            $response = curl_exec($curl);
            $result = json_decode($response);

            curl_close($curl);
    
            if (isset($result->entity->uuid)) {
                $CdekOrder = new CdekOrder();
                $CdekOrder->uuid = $result->entity->uuid;
                $CdekOrder->shop_order_id = $ShopOrder->id;
                $CdekOrder->save();

                return $this->createReceipt($CdekOrder);
            }
        } else {

            if (strtotime('+1 hour', strtotime($CdekOrder->updated_at)) < strtotime(date("Y-m-d H:i:s")) || $step == 3) {
                $CdekOrder->receipt_uuid = '';
                $CdekOrder->url = '';
                $CdekOrder->save();
            }

            if (strtotime('+1 hour', strtotime($CdekOrder->updated_at)) > strtotime(date("Y-m-d H:i:s")) && !empty($CdekOrder->url)) {
                return $CdekOrder;
            } 

            //получаем ссылку
            return $this->createReceipt($CdekOrder);
        }
    }

    public function createReceipt(CdekOrder $CdekOrder)
    {

        if (empty($CdekOrder->receipt_uuid)) {
            $aData = [];
            $orders[]["order_uuid"] = $CdekOrder->uuid;
            $aData["orders"] = $orders;
            $aData["copy_count"] = 2;
    
            $curl = curl_init();
        
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.cdek.ru/v2/print/orders',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($aData, JSON_UNESCAPED_UNICODE),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $this->Cdek->token,
                    'Content-Type: application/json'
                ),
            ));
    
            $response = curl_exec($curl);
            $result = json_decode($response);
    
            curl_close($curl);
    
            if (isset($result->entity->uuid)) {
    
                $CdekOrder->receipt_uuid = $result->entity->uuid;
                $CdekOrder->save();
    
                return $this->getPrintLink($CdekOrder);
            }
        } else {
            return $this->getPrintLink($CdekOrder);
        }

        return false;
    }

    public function getPrintLink(CdekOrder $CdekOrder)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.cdek.ru/v2/print/orders/' . $CdekOrder->receipt_uuid,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $this->Cdek->token
          ),
        ));
        
        $response = curl_exec($curl);
        $result = json_decode($response);

        curl_close($curl);

        if (isset($result->entity->url)) {

            $CdekOrder->url = $result->entity->url;
            $CdekOrder->save();

            return $CdekOrder;
        }

        return '';
    }

    public function cdekCreateOrder(Request $request)
    {

        $result = false;

        if (!is_null($ShopOrder = ShopOrder::find($request->shop_order_id))) { 

            $aError["error"] = [];
            if ($request->cdek_dimension_id == 0 && (!$request->width > 0 && !$request->height > 0 && !$request->length > 0)) {
                $aError["error"][] = "Заполните поле упаковка либо заполните габариты! - " . $request->width;
            } else {
                $ShopOrder->cdek_dimension_id = $request->cdek_dimension_id;
            }

            if (empty($request->surname)) {
                $aError["error"][] = "Фамилия";
            } else {
                $ShopOrder->surname = $request->surname;
            }

            if (empty($request->name)) {
                $aError["error"][] = "Имя";
            } else {
                $ShopOrder->name = $request->name;
            }

            if (empty($request->phone)) {
                $aError["error"][] = "Телефон";
            } else {
                $ShopOrder->phone = $request->phone;
            }

            $ShopOrder->save();

            if (empty($request->delivery_7_city_id)) {
                $aError["error"][] = "Заполните поле Город";
            } else {

                self::SaveShopDeliveryFieldValue($ShopOrder->id, 16, $request->delivery_7_city_id);
                self::SaveShopDeliveryFieldValue($ShopOrder->id, 10, $request->delivery_7_city);
            }

            if (empty($request->delivery_7_city_id) && empty($request->delivery_7_courier)) {
                $aError["error"][] = "Заполните поле Отделение или Курьер";
            } else {

                if ($request->delivery_7_delivery_type == 11) {
                    self::SaveShopDeliveryFieldValue($ShopOrder->id, 11, $request->delivery_7_office);
                    self::SaveShopDeliveryFieldValue($ShopOrder->id, 17, $request->delivery_7_office_id);
                }

                if ($request->delivery_7_delivery_type == 15) {
                    self::SaveShopDeliveryFieldValue($ShopOrder->id, 15, $request->delivery_7_courier);
                }  
            }

            if ($ShopOrder->ShopOrderItems->count() == 0) {
                $aError["error"][] = "Нет товаров у заказа";
            }
    
            if (count($aError["error"]) > 0) {
                return response()->json($aError);
            }

            $CdekOrder = $this->createOrder($ShopOrder, CdekSender::find(1), $request->step);
            
            $result["id"] = isset($CdekOrder->id) ? $CdekOrder->id : '';
            $result["printUrl"] = isset($CdekOrder->id) ? route("printCdekOrder", $CdekOrder->id) : '';

        }

        return response()->json($result);
    }

    public static function SaveShopDeliveryFieldValue($shop_order_id, $field_id, $value)
    {
        if (is_null($ShopDeliveryFieldValue = ShopDeliveryFieldValue::where("shop_order_id", $shop_order_id)->where("shop_delivery_field_id", $field_id)->first())) {
            $ShopDeliveryFieldValue = new ShopDeliveryFieldValue();
        }

        $ShopDeliveryFieldValue->shop_order_id = $shop_order_id;
        $ShopDeliveryFieldValue->shop_delivery_field_id = $field_id;
        $ShopDeliveryFieldValue->value = $value;
        $ShopDeliveryFieldValue->save();
    }

    public function print(CdekOrder $CdekOrder)
    {
        
        if (!empty($CdekOrder->url)) {
            $curl = curl_init();
    
            curl_setopt_array($curl, array(
              CURLOPT_URL => $CdekOrder->url,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->Cdek->token
              ),
            ));
            
            $response = curl_exec($curl);
    
            if ($response) {
    
                header('Content-Type: application/pdf');
                echo $response;
            } else {
                echo "Сервер не отвечает, попробуйте позже";
            }
            
            curl_close($curl);
        } else {
            return false;
        }

    }

}