<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShopOrder;
use App\Models\PrOrder;


class PochtaRossiiController extends Controller
{

    public function createOrder(Request $request)
    {

        $response = false;

        if (!is_null($ShopOrder = ShopOrder::find($request->shop_order_id))) { 

            if (is_null(PrOrder::where("shop_order_id", $ShopOrder->id)->first())) {
                $curl = curl_init();

                $aData["address-type-to"] = "DEFAULT";
                $aData["mail-type"] = "POSTAL_PARCEL";
                $aData["mail-category"] = "ORDINARY";
                
                /*
                * https://otpravka.pochta.ru/specification#/dictionary-countries
                */
                $aData["mail-direct"] = 643; //rf
                $aData["mass"] = 300;
        
                $aData["index-to"] = $request->delivery_1_index;

                if ($request->delivery_1_region) {
                    $aData["region-to"] = trim($request->delivery_1_region . ' ' . ($request->delivery_1_area));
                }

                if ($request->delivery_1_city) {
                    $aData["place-to"] = $request->delivery_1_city;
                }

                if ($request->delivery_1_address) {
                    $aData["street-to"] = $request->delivery_1_address;
                }

                $aData["recipient-name"] = implode(" ", [$ShopOrder->surname, $ShopOrder->name]);
                $aData["tel-address"] = preg_replace('![^0-9\+]+!', '', $ShopOrder->phone);
                $aData["order-num"] = $ShopOrder->id;
                $aData["postoffice-code"] = "196606";
        
                $Result[] = $aData;
        
                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://otpravka-api.pochta.ru/2.0/user/backlog',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'PUT',
                  CURLOPT_POSTFIELDS => json_encode($Result, JSON_UNESCAPED_UNICODE),
                  CURLOPT_HTTPHEADER => array(
                    'Authorization: AccessToken NOFUuyEbpqrz9DnPAK2MUCeA40oPHNX5',
                    'X-User-Authorization: Basic eWVsbG93MTQ0MDQzMEBnbWFpbC5jb206U3VubGlnaHRfSmFuZTA5MzE0NDA0MzA=',
                    'Content-Type: application/json;charset=UTF-8'
                  ),
                ));
                
                $response = curl_exec($curl);
                
                curl_close($curl);  
                
                $aResponse = json_decode($response, true);

                if (isset($aResponse['orders'])) { 
                    $PrOrder = new PrOrder();
                    $PrOrder->shop_order_id = $ShopOrder->id;
                    $PrOrder->track = $aResponse['orders'][0]['barcode'];
                    $PrOrder->save();

                    $response = $aResponse['orders'][0]['barcode'];
                }

            } else {
                $response["error"] = "Отправление уже было создано";
            }
        } else {
            $response["error"] = "Заказ не найден";
        }

        return response()->json($response);
    }
}