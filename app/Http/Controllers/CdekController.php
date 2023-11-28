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
            CURLOPT_URL => 'https://api.cdek.ru/v2/oauth/token?grant_type=client_credentials&client_id=raHsvosp1lzzVdhtBeG5xxvdM8AcPIOJ&client_secret=2WrwnXn7Tr8gXhfsiwgb2k8cEGIiDTMw',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'client_id: raHsvosp1lzzVdhtBeG5xxvdM8AcPIOJ',
                'client_secret: 2WrwnXn7Tr8gXhfsiwgb2k8cEGIiDTMw'
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

    public function getOffices()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.cdek.ru/v2/deliverypoints',
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

            $package["weight"] = (int) $ShopOrder->CdekDimension->weight;
            $package["length"] = (int) $ShopOrder->CdekDimension->length / 10;
            $package["width"] = (int) $ShopOrder->CdekDimension->width / 10;
            $package["height"] = (int) $ShopOrder->CdekDimension->height / 10;
    
            foreach ($ShopOrder->ShopOrderItems as $ShopOrderItem) {
                
                $ShopItem = $ShopOrderItem->ShopItem->parentItemIfModification();
    
                $OrderItem = [];
                $OrderItem["ware_key"] = $ShopOrderItem->shop_item_id;
                $OrderItem["payment"]["value"] = 0;
                $OrderItem["name"] = $ShopOrderItem->ShopItem->name;
                $OrderItem["cost"] = $ShopOrderItem->price;
                $OrderItem["amount"] = (int) $ShopOrderItem->quantity;
                $OrderItem["weight"] = (int) $ShopItem->weight;
                $OrderItem["url"] = "www.". env("APP_NAME") . $ShopItem->url;
    
                $package["items"][] = $OrderItem;
                
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
            if ($ShopOrder->cdek_dimension_id == 0) {
                $aError["error"][] = "Заполните поле Упаковка";
            }

            if (empty($ShopOrder->surname)) {
                $aError["error"][] = "Фамилия";
            }
            if (empty($ShopOrder->name)) {
                $aError["error"][] = "Имя";
            }
            if (empty($ShopOrder->phone)) {
                $aError["error"][] = "Телефон";
            }

            if (is_null(ShopDeliveryFieldValue::where("shop_order_id", $ShopOrder->id)->where("shop_delivery_field_id", 16)->first())) {
                $aError["error"][] = "Заполните поле Город";
            }

            if (is_null(ShopDeliveryFieldValue::where("shop_order_id", $ShopOrder->id)->where("shop_delivery_field_id", 14)->first()) &&
                is_null(ShopDeliveryFieldValue::where("shop_order_id", $ShopOrder->id)->where("shop_delivery_field_id", 17)->first())) {
                    $aError["error"][] = "Заполните поле Отделение или Курьер";
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