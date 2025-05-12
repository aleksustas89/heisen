<?php

namespace App\Http\Controllers;
use App\Models\Cdek;
use App\Models\ShopOrder;
use App\Models\CdekSender;
use App\Models\ShopDeliveryFieldValue;
use App\Models\CdekOrder;
use App\Models\CdekOffice;
use Illuminate\Http\Request;

class CdekController extends Controller
{

    public $Cdek = NULL;

    protected static $tariff_code = 136;

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

    public function chooseOffice(Request $request)
    {

        if ($request->code && !is_null($CdekOffice = CdekOffice::whereCode($request->code)->first())) {

            return response()->json([
                "id" => $CdekOffice->id,
                "name" => $CdekOffice->name,
                "code" => $CdekOffice->code,
                "city" => $CdekOffice->CdekCity->name ?? '',
                "address_comment" => $CdekOffice->address_comment,
                "work_time" => $CdekOffice->work_time,
            ]);
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


    public function getCities($page = 0)
    {
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.cdek.ru/v2/location/cities?country_codes=RU&size=1000&page=' . $page,
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
        
        curl_close($curl);

        return !isset($response->requests->errors) ? $response : false;
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

            //со склада
            $aData["shipment_point"] = $CdekSender->CdekOffice->code;
            
            //на склад
            if (!is_null($ShopDeliveryFieldValue = ShopDeliveryFieldValue::where("shop_delivery_field_id", 17)->where("shop_order_id", $ShopOrder->id)->first())) {
                $aData["delivery_point"] = $ShopDeliveryFieldValue->value;
            }                
            
            $aData["tariff_code"] = self::$tariff_code;

    
            $aData["recipient"]["name"] = implode(" ", [$ShopOrder->surname, $ShopOrder->name]);
    
            $number["number"] = preg_replace('![^0-9\+]+!', '', $ShopOrder->phone);;
    
            $aData["recipient"]["phones"][] = $number;
            
            $aData["sender"]["name"] = $CdekSender->name;
            
            $sendePhone = [];
            $sendePhone["number"] = $CdekSender->phone;
            $sendePhone["additional"] = '';

            $aData["sender"]["phones"][] = $sendePhone;
            
    
            $package = [];
            $package["number"] = "order-" . $ShopOrder->id;

           if (!is_null($CdekDimension = $ShopOrder->CdekDimension)) {

                $package["weight"] = (int) $ShopOrder->CdekDimension->weight;
                $service['code'] = $CdekDimension->box_name;
                $service['parameter'] = 1;
                $aData["services"][] = $service;
                
           } else if ($ShopOrder->weight > 0 && $ShopOrder->width > 0 && $ShopOrder->height > 0 && $ShopOrder->length > 0) {

                $package["weight"] = (int) $ShopOrder->weight;
                $package["width"] = (int) $ShopOrder->width;
                $package["height"] = (int) $ShopOrder->height;
                $package["length"] = (int) $ShopOrder->length;
           }

            foreach ($ShopOrder->ShopOrderItems()->where("shop_order_items.deleted", 0)->get() as $ShopOrderItem) {
                
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

                $this->setTrack($CdekOrder);

                return $this->createReceipt($CdekOrder);
            } else {
                return $response;
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

            $this->setTrack($CdekOrder);

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

    public function deleteOrder(CdekOrder $CdekOrder)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.cdek.ru/v2/orders/' . $CdekOrder->uuid,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'DELETE',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $this->Cdek->token
          ),
        ));
        
        $response = curl_exec($curl);
        $result = json_decode($response);

        curl_close($curl);

        $CdekOrder->delete();

        return response()->json($result);
    }

    public function cdekCreateOrder(Request $request)
    {

        $result = false;

        if (!is_null($ShopOrder = ShopOrder::find($request->shop_order_id))) { 

            // if (!is_null($CdekOrder = CdekOrder::where("shop_order_id", $ShopOrder->id)->first())) {

            //     $this->deleteOrder($CdekOrder);
            // }


            $aError["error"] = [];
            if ((empty($request->cdek_dimension_id) || $request->cdek_dimension_id == 0) && ($request->width == 0 || $request->height == 0 || $request->length == 0)) {
                $aError["error"][] = "Заполните поле коробка либо заполните габариты!";
            } else {
                $ShopOrder->cdek_dimension_id = $request->cdek_dimension_id;
                $ShopOrder->width = $request->width;
                $ShopOrder->height = $request->height;
                $ShopOrder->length = $request->length;
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


            if ($ShopOrder->ShopOrderItems()->where("deleted", 0)->count() == 0) {
                $aError["error"][] = "Нет товаров у заказа";
            }
    
            if (count($aError["error"]) > 0) {
                return response()->json($aError);
            }

            $CdekOrder = $this->createOrder($ShopOrder, CdekSender::find(1), $request->step);
            
            $result["id"] = isset($CdekOrder->id) ? $CdekOrder->id : '';
            $result["printUrl"] = isset($CdekOrder->id) ? route("printCdekOrder", $CdekOrder->id) : '';
            $result["deleteOrder"] = isset($CdekOrder->id) ? route("deleteOrder", $CdekOrder->id) : '';

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

    public function setTrack (CdekOrder $CdekOrder)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.cdek.ru/v2/orders/' . $CdekOrder->uuid,
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

        if (isset($result->entity->cdek_number)) {

            $CdekOrder->track = $result->entity->cdek_number;
            $CdekOrder->save();
        }
    }

}