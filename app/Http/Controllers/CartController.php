<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Darryldecode\Cart\Cart;
use App\Models\Shop;
use App\Models\ShopItem;
use App\Models\ShopOrder;
use App\Models\CdekCity;
use App\Models\CdekOffice;
use App\Models\ShopOrderItem;
use App\Models\ShopCountryLocationCity;
use App\Models\ShopPaymentSystem;
use App\Models\ShopDelivery;
use App\Models\ShopCurrency;
use App\Models\ShopDeliveryFieldValue;
use App\Models\Ukassa;
use App\Models\UkassaOrder;
use Illuminate\Support\Facades\Mail;
use App\Models\Mail\SendOrder;
use App\Models\Mail\SendOrderAdmin;
use Illuminate\Support\Facades\Auth;
use YooKassa\Client;
use App\Services\Helpers\Guid;
use App\Models\ShopCartItem;
use App\Http\Controllers\Shop\Payment\Handler\ShopPaymentHandlerController;
use App\Models\Boxberry;

class CartController extends Controller
{

    protected static $_cookie_live = 3600 * 24 * 7;

    public function index()
    {

        $client = Auth::guard('client')->user();

        return view('shop.cart', [
            "Cart" => self::getCart(),
            "Cities" => ShopCountryLocationCity::get(),
            "Payments" => ShopPaymentSystem::where("deleted", 0)->orderBy("sorting", "ASC")->get(),
            'shopDeliveries' => ShopDelivery::where("deleted", 0)->orderBy("sorting", "ASC")->get(),
            "client" => $client,
            "Boxberry" => Boxberry::find(1)
        ]);
    }

    public function updateItemInCart(Request $request)
    {

        if ($cart_id = self::isSetCart()) {
            if (!is_null($ShopCartItem = ShopCartItem::where("cart_id", $cart_id)->where("id", request()->id)->first())) {
                $count = $ShopCartItem->count + request()->count;
                $ShopCartItem->count = $count > 0 ? $count : 1;
                $ShopCartItem->save();
            }
        }

        return self::getCartItemsTemplate(request()->littleCart ?? 0);
    }

    public function add(ShopItem $shopItem, $count)
    {
        if (!$cart_id = self::isSetCart()) {
            
            $cart_id = $this->setCart();
        }

        if (!is_null($ShopCartItem = ShopCartItem::where("cart_id", $cart_id)->where("shop_item_id", $shopItem->id)->first())) {
            $ShopCartItem->count = $ShopCartItem->count + $count;
        } else {
            $ShopCartItem = new ShopCartItem();
            $ShopCartItem->price = $shopItem->price();
            $ShopCartItem->old_price = $shopItem->oldPrice();
            $ShopCartItem->cart_id = $cart_id;
            $ShopCartItem->count = $count;
            $ShopCartItem->shop_item_id = $shopItem->id;
        }

        $ShopCartItem->save();

        return true;

    }

    public function addToCart(Request $request)
    {
        if (!is_null($ShopItem = ShopItem::find($request->shop_item_id))) {

            $this->add($ShopItem, $request->count);
    
            return response()->view("shop.add-cart-window", ["ShopItem" => $ShopItem]);
        }
    }



    public static function deleteFromCart()
    {

        if ($cart_id = self::isSetCart()) {
            if (!is_null($ShopCartItem = ShopCartItem::where("cart_id", $cart_id)->where("id", request()->id)->first())) {
                $ShopCartItem->delete();
            }
        }

        return self::getCartItemsTemplate(request()->littleCart ?? 0);
    }

    public static function getLittleCart()
    {

        return self::getCartItemsTemplate(request()->littleCart ?? 0);
    }

    public static function getCartItemsTemplate($littleCart = 0)
    {
        return response()->view('shop.cart-items', ["littleCart" => $littleCart]);
    }

    public static function getCart()
    {
        $aResult = false;

        $ShopCurrency = ShopCurrencyController::getCurrent();

        if ($cart_id = self::isSetCart()) {

            $ShopCartItems = ShopCartItem::select("shop_cart_items.*")
                        ->join("shop_items", "shop_cart_items.shop_item_id", "=", "shop_items.id")
                        ->where("shop_cart_items.cart_id", $cart_id)
                        ->where("shop_items.active", 1)
                        ->where("shop_items.deleted", 0)
                        ->get();

            $aResult["items"] = $ShopCartItems;

            $total = 0;
            $totalDiscount = 0;
            
            foreach ($ShopCartItems as $ShopCartItem) {
                if (!is_null($ShopItem = $ShopCartItem->ShopItem)) {
                    $total += $ShopItem->getPriceApplyCurrency($ShopCurrency) * $ShopCartItem->count;
                    $totalDiscount += $ShopCartItem->old_price > 0 ? $ShopCartItem->old_price - $ShopCartItem->price : 0;
                } else {
                    $ShopCartItem->delete();
                }
            }

            $aResult["totalPrice"] = $total;
            $aResult["countItems"] = count($aResult["items"]);
            $aResult["totalDiscount"] = $totalDiscount;

        } 
        
        return $aResult;

    }

    protected static function isSetCart()
    {
        return isset($_COOKIE["cart_id"]) ? $_COOKIE["cart_id"] : false;
    }

    protected function setCart()
    {
        $cart_id = uniqid();

        setcookie("cart_id", $cart_id, time() + (self::$_cookie_live), "/");

        return $cart_id;
    }


    public function getCities(Request $request)
    {

        $query = $request->input('query');

        $aResult["query"] = $query;

        $items = [];

        foreach (ShopCountryLocationCity::where("name", "like", "%" . $query . "%")->get() as $ShopCountryLocationCity) {
            $items[] = ["value" => $ShopCountryLocationCity->name .", ". $ShopCountryLocationCity->ShopCountryLocation->name, "data" => $ShopCountryLocationCity->id];
        }

        $aResult["suggestions"] = $items;


        return response()->json($aResult);
    }

    public function saveOrder(Request $request)
    {

        $getCart = self::getCart();

        if ($getCart && $getCart["totalPrice"] > 0) {
            
            $ShopOrder = new ShopOrder();

            $Fields = [
                'name' => ['required', function ($attribute, $value, $fail) {
    
                    if (preg_match("/[^(\w)|(\x7F-\xFF)|(\s)]/", $value)) 
                    {
                        $fail('Имя может содержать только русские / латинские символы, пробел, цифры и знак _');
                    }
                },],
                'surname' => ['required', function ($attribute, $value, $fail) {
    
                    if (preg_match("/[^(\w)|(\x7F-\xFF)|(\s)]/", $value)) 
                    {
                        $fail('Фамилия может содержать только русские / латинские символы, пробел, цифры и знак _');
                    }
                },],
                'phone' => ['required', function ($attribute, $value, $fail) {
                    $value = preg_replace("/[^,.0-9]/", '', $value);
                    if (strlen($value) < 11) {
                        $fail('The '.$attribute.' is invalid.');
                    }
                },],
            ];

            if ($request->shop_delivery_id == 7) {
                /*cdek*/
                $Fields['delivery_7_city_id'] = 'required';
    
                if ($request->delivery_7_delivery_type == 11) {
                    $Fields['delivery_7_office_id'] = 'required';
                } else if ($request->delivery_7_delivery_type == 15) {
                    $Fields['delivery_7_courier'] = 'required';
                }
            }

            if ($request->shop_delivery_id == 8) {
                $Fields['delivery_8_city'] = 'required';
            }
    
            if ($request->shop_delivery_id == 1) {
                $Fields['delivery_1_city'] = 'required';
                $Fields['delivery_1_office'] = 'required';
            }
    
            if (!empty($request->email)) {
                $Fields['email'] = 'required|email';
            }
    
            $request->validate($Fields);

            $ShopCurrency = ShopCurrency::where("default", 1)->first();

            $ShopOrder->shop_payment_system_id = $request->shop_payment_system_id ?? 0;
            $ShopOrder->shop_currency_id = $ShopCurrency->id ?? 0;
            $ShopOrder->shop_delivery_id = $request->shop_delivery_id ?? 0;

            if (!is_null($client = Auth::guard('client')->user())) {
                $ShopOrder->client_id = $client->id;;
            }

            $ShopOrder->name = $request->name;
            $ShopOrder->surname = $request->surname;
            $ShopOrder->email = $request->email;
            $ShopOrder->phone = $request->phone;
            $ShopOrder->city = $request->city;
            $ShopOrder->description = \App\Services\Helpers\Str::clean($request->description);
            $ShopOrder->not_call = $request->not_call ?? 0;
            $ShopOrder->guid = Guid::get();

            $ShopOrder->save();

            $weight = $length = $height = $width = 0;

            //foreach ($getCart as $CartItem) {
            foreach ($getCart["items"] as $ShopCartItem) {

                $name = '';

                $ShopItem = $ShopCartItem->ShopItem;

                if ($ShopItem->modification_id > 0) {
                    $name = implode(" ", $ShopItem->modificationName());
                } else {
                    $name = $ShopItem->name;
                }

                $ShopOrderItem = new ShopOrderItem();
                $ShopOrderItem->shop_item_id = $ShopCartItem->shop_item_id;
                $ShopOrderItem->shop_order_id = $ShopOrder->id;
                $ShopOrderItem->name = $name;
                $ShopOrderItem->quantity = $ShopCartItem->count;
                $ShopOrderItem->price = $ShopCartItem->price;
                $ShopOrderItem->old_price = $ShopCartItem->old_price > 0 ? $ShopCartItem->old_price : 0;
                $ShopOrderItem->save();


                //подсчет габаритов
                $ShopItem = ShopItem::find($ShopCartItem->shop_item_id)->parentItemIfModification();
                $weight += $ShopItem->weight * $ShopCartItem->count;
                $width = $ShopItem->width > $width ? $ShopItem->width : $width;
                $height = $ShopItem->height > $height ? $ShopItem->height : $height;
                $length += $ShopItem->length * $ShopCartItem->count;
            }

            $ShopOrder->weight = $weight;
            $ShopOrder->height = $height;
            $ShopOrder->length = $length;
            $ShopOrder->width = $width;
            


            $ShopOrder->save();

            //способы доставки
            if (!is_null($ShopDelivery = ShopDelivery::find($request->shop_delivery_id))) {
                foreach ($ShopDelivery->ShopDeliveryFields as $ShopDeliveryField) {
                    $key = "delivery_" . $request->shop_delivery_id . "_" . $ShopDeliveryField->field;
                    if (isset($request->$key)) {

                        $ShopDeliveryFieldValue = new ShopDeliveryFieldValue();
                        $ShopDeliveryFieldValue->shop_order_id = $ShopOrder->id;
                        $ShopDeliveryFieldValue->shop_delivery_field_id = $ShopDeliveryField->id;
                        $ShopDeliveryFieldValue->value = $request->$key;
                        $ShopDeliveryFieldValue->save();
                    }
                }
            }

            $this->clear();
            
            $Shop = Shop::get();

            //admin
            Mail::to($Shop->email)->send(new SendOrderAdmin($ShopOrder));

            //client
            if (!empty($ShopOrder->email)) {
                Mail::to($ShopOrder->email)->send(new SendOrder($ShopOrder));
            }

            return ShopPaymentHandlerController::factory($ShopOrder->ShopPaymentSystem)->execute($ShopOrder);

        } else {

            return $this->index();
        }
    }

    protected function clear()
    {
        if ($cart_id = self::isSetCart()) {
            ShopCartItem::where("cart_id", $cart_id)->delete();

            setcookie("cart_id", "", time() - 3600);
            unset($_COOKIE["cart_id"]);
        }
    }

    public function getCdekCities(Request $request)
    {
        $query = $request->input('query');

        $aResult["query"] = $query;

        $items = [];

        foreach (CdekCity::where("name", "like", "%" . $query . "%")->get() as $CdekCity) {
            $items[] = ["value" => $CdekCity->name .", ". $CdekCity->CdekRegion->name, "data" => $CdekCity->id];
        }

        $aResult["suggestions"] = $items;

        return response()->json($aResult);
    }

    public function getCdekOffices(Request $request)
    {

        $query = $request->input('query');

        $aResult["query"] = $query;

        $items = [];

        foreach (CdekOffice::where("name", "like", "%" . $query . "%")->where("cdek_city_id", $request->city_id)->where("active", 1)->get() as $CdekOffice) {
            $items[] = ["value" => $CdekOffice->name .", ". $CdekOffice->address_comment, "data" => $CdekOffice->id];
        }

        $aResult["suggestions"] = $items;

        return response()->json($aResult);
    }

    /**
     * @param ShopOrder
     * @return string link or false
    */
    public function preparePayment(ShopOrder $ShopOrder)
    {

        if (!is_null($Ukassa = Ukassa::find(1))) {

            $client  = new Client();
    
            $client->setAuth($Ukassa->shop_id, $Ukassa->token);

            $idempotenceKey = $ShopOrder->guid;

            $response = $client->createPayment(
                array(
                    'amount' => array(
                        'value' => $ShopOrder->getSum(),
                        'currency' => 'RUB',
                    ),
                    'payment_method_data' => array(
                        'type' => $ShopOrder->ShopPaymentSystem->description, // 'bank_card', //sbp
                    ),
                    'confirmation' => array(
                        'type' => 'redirect',
                        'return_url' => route("finish-order") . '?guid=' . $ShopOrder->guid,
                    ),
                    'description' => 'Заказ №' . $ShopOrder->id,
                ),
                $idempotenceKey
            );

            if (!is_null($confirmationUrl = $response->getConfirmation()->getConfirmationUrl())) {

                if (!is_null($CheckUkassaOrder = UkassaOrder::where("shop_order_id", $ShopOrder->id)->first())) {
                    $CheckUkassaOrder->delete();
                }

                $UkassaOrder = new UkassaOrder();
                $UkassaOrder->shop_order_id = $ShopOrder->id;
                $UkassaOrder->ukassa_result_uuid = $response->_id;
                $UkassaOrder->save();

                return $confirmationUrl;
            }
        }

        return false;
    }

    public function finishOrder(Request $request)
    {

        $status = 0;

        if ($request->guid) {
            $UkassaOrder = UkassaOrder::select("ukassa_orders.*")
                ->join("shop_orders", "shop_orders.id", "=", "ukassa_orders.shop_order_id")
                ->where("shop_orders.guid", $request->guid)
                ->first();

            if (!is_null($UkassaOrder)) {
                $payment = $this->getPaymentInfo($UkassaOrder->ukassa_result_uuid); 
        
                if ($payment && !is_null($ShopOrder = $UkassaOrder->ShopOrder)) {
                    if ($payment->_status == 'succeeded') {
                        $ShopOrder->paid = 1;
                        $ShopOrder->save();
                        $status = 1;
                    }
                }
            }

        }

        return view("shop.cart.paid", [
            "status" => $status, 
            "guid" => $request->guid, 
            "step" => $request->step ?? 0
        ]);
    }

    public function getPaymentInfo($ukassa_result_uuid)
    {
        if (!is_null($Ukassa = Ukassa::find(1))) {
            $client = new Client();
            $client->setAuth($Ukassa->shop_id, $Ukassa->token);
            return $client->getPaymentInfo($ukassa_result_uuid); 
        }
        
        return false;
    }
    

    
}
