<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShopOrder;
use App\Models\Boxberry;
use App\Models\BoxberrySender;
use App\Models\BoxberryOrder;
use App\Models\ShopDeliveryFieldValue;

class BoxberryController extends Controller
{

    public function createOrder(Request $request)
    {

        $response = false;

        if (!is_null($ShopOrder = ShopOrder::find($request->shop_order_id))) { 

            if(!is_null($Boxberry = Boxberry::find(1))) {

                if(!is_null($BoxberrySender = BoxberrySender::find(1))) {

                    if (is_null(BoxberryOrder::where("shop_order_id", $ShopOrder->id)->first())) {

                        $Result["token"] = $Boxberry->api_token;
                        $Result["method"] = "ParselCreate";
    
                        $sData["order_id"] = (string) $ShopOrder->id;
    
                        $sData["payment_sum"] = $request->pod ? $ShopOrder->getSum() : 0;
                        
                        $sData["vid"] = 1;
    
                        $name1 = 0;
    
                        if (!is_null($ShopDeliveryFieldValue = ShopDeliveryFieldValue::where("shop_delivery_field_id", 21)->where("shop_order_id", $ShopOrder->id)->first())) {
                            $name1 = $ShopDeliveryFieldValue->value;
                        }
    
                        $aOffices = [
                            "name" => $name1,
                            "name1" => $BoxberrySender->boxberry_office_id 
                        ];
    
                        $sData["shop"] = $aOffices;
    
                        $aCustomer = [
                            "fio" => implode(" ", [$ShopOrder->surname, $ShopOrder->name]),
                            "phone" => $ShopOrder->phone,
                            "email" => $ShopOrder->email,
                        ];
                        
                        $sData["customer"] = $aCustomer;
    
                        $aItems = [];
    
                        foreach ($ShopOrder->ShopOrderItems as $ShopOrderItem) {
                        
                            $OrderItem = [];
                            $OrderItem["id"] = (string) $ShopOrderItem->shop_item_id;
                            $OrderItem["name"] = $ShopOrderItem->ShopItem->name;
                            $OrderItem["price"] = $ShopOrderItem->price;
                            $OrderItem["quantity"] = (int) $ShopOrderItem->quantity;
                            $OrderItem["nds"] = 0;
                            
                            $aItems[] = $OrderItem;  
                        }
    
                        $sData["items"] = $aItems;
    
                        $sData["weights"] = [
                            "weight" => (int)$ShopOrder->weight
                        ];

                        if ($request->boxberry_dimension_id > 0) {

                            switch ($request->boxberry_dimension_id) {

                                case '1':
                                    $sData["weights"]["x"] = 15;
                                    $sData["weights"]["y"] = 15;
                                    $sData["weights"]["z"] = 15;
                                    break;

                                case '2':
                                    $sData["weights"]["x"] = 20;
                                    $sData["weights"]["y"] = 20;
                                    $sData["weights"]["z"] = 20;
                                    break;

                                case '3':
                                    $sData["weights"]["x"] = 35;
                                    $sData["weights"]["y"] = 20;
                                    $sData["weights"]["z"] = 20;
                                    break;

                                case '4':
                                    $sData["weights"]["x"] = 35;
                                    $sData["weights"]["y"] = 30;
                                    $sData["weights"]["z"] = 25;
                                    break;

                                case '5':
                                    $sData["weights"]["x"] = 50;
                                    $sData["weights"]["y"] = 40;
                                    $sData["weights"]["z"] = 35;
                                    break; 
                            }
                        }
    
                        $Result["sdata"] = $sData;

    
                        $curl = curl_init();
    
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://api.boxberry.ru/json.php',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($Result, JSON_UNESCAPED_UNICODE),
                            CURLOPT_HTTPHEADER => array(
                                'Content-Type: application/json'
                            ),
                        ));
    
                        $response = curl_exec($curl);
    
                        curl_close($curl);         
    
                        $response = json_decode($response);
    
                        if (isset($response->track)) {
                            $BoxberryOrder = new BoxberryOrder();
                            $BoxberryOrder->shop_order_id = $ShopOrder->id;
                            $BoxberryOrder->track = $response->track;
                            $BoxberryOrder->url = $response->label;
                            $BoxberryOrder->save();
                        }
    
                    } else {
                        $response["err"] = "Отправление уже было создано";
                    }

                } else {
                    $response["err"] = "Отсутствует Boxberry отправитель";
                }
            } else {
                $response["err"] = "Отсутствует Boxberry модель";
            }

        } else {
            $response["err"] = "Заказ не найден";
        }

        return response()->json($response);
    }
}
