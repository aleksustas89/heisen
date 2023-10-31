<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Darryldecode\Cart\Cart;
use App\Models\Shop;
use App\Models\ShopItem;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopCountryLocationCity;
use App\Models\ShopPaymentSystem;
use App\Models\ShopDelivery;
use App\Models\ShopCurrency;
use App\Models\ShopDeliveryFieldValue;
use Illuminate\Support\Facades\Mail;
use App\Models\Mail\SendOrder;
use App\Models\Mail\SendOrderAdmin;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {

        $cartCollection = \App\Http\Controllers\CartController::getCart();
        $client = Auth::guard('client')->user();

        return view('shop.cart', [
            "cartCount" => $cartCollection ? $cartCollection->count() : 0,
            "Cities" => ShopCountryLocationCity::get(),
            "Payments" => ShopPaymentSystem::get(),
            'shopDeliveries' => ShopDelivery::get(),
            "client" => $client,
        ]);
    }

    public function addToCart(Request $request)
    {

        if (!$cart_id = self::isSetCart()) {
            
            $cart_id = uniqid();

            setcookie("cart_id", $cart_id);
        } 

        if (!is_null($ShopItem = ShopItem::find($request->id))) {

            \Cart::session($cart_id);

            $ShopItemImage = $ShopItem->getShopItemImage() ?? false;

            \Cart::session($cart_id)->add(array(
                'id' => $ShopItem->id,
                'name' => $ShopItem->name,
                'price' => $ShopItem->price(),
                'quantity' => $request->quantity ?? 1,
                'attributes' => [
                    "img" => $ShopItemImage ? $ShopItem->path() . $ShopItemImage->image_small : '',
                    "url" => $ShopItem->url(),
                    "oldPrice" => $ShopItem->oldPrice(),
                ],
            ));

            return response()->view("shop.add-cart-window", ["ShopItem" => $ShopItem]);
    
        }
    }

    public static function getTotalDiscount()
    {
        $discount = 0;
        if ($Cart = self::getCart()) {
            foreach ($Cart as $item) {
                $discount += $item["attributes"]["oldPrice"] > 0 ? $item["attributes"]["oldPrice"] - $item["price"] : 0;
            }
        }
        
        return $discount;
    }

    public static function deleteFromCart(Request $request)
    {
        if ($cart_id = self::isSetCart()) {
            \Cart::session($cart_id)->remove($request->id);
        }

        return self::getCartItemsTemplate($request->littleCart ?? 0);
    }

    public static function getLittleCart(Request $request)
    {
        return self::getCartItemsTemplate($request->littleCart ?? 0);
    }

    public static function getCartItemsTemplate($littleCart = 0)
    {
        return response()->view("shop.cart-items", ["littleCart" => $littleCart]);
    }

    public static function getCart()
    {
        if ($cart_id = self::isSetCart()) {
            $aCart = \Cart::session($cart_id)->getContent()->toArray();


            foreach ($aCart as $aCartItem) {

                $ShopItem = ShopItem::find($aCartItem["id"]);
                $current_price = $ShopItem->price();
                if ($current_price != $aCartItem["price"]) {

                    $attributes = $aCartItem["attributes"];
                    $attributes["priceChanged"] = 1;
                    
                    \Cart::session($cart_id)->update($aCartItem["id"], [
                        'price' => $current_price,
                        'attributes' => $attributes,
                    ]);
                }
            }

            return \Cart::session($cart_id)->getContent();
        } else {
            return false;
        }
    }


    public static function getTotal()
    {
        if ($cart_id = self::isSetCart()) {
            return \Cart::session($cart_id)->getSubTotal();
        } else {
            return false;
        }
    }

    protected static function clear()
    {
        if ($cart_id = self::isSetCart()) {
            \Cart::session($cart_id)->clear();
        }
    }

    protected static function isSetCart()
    {
        return isset($_COOKIE["cart_id"]) ? $_COOKIE["cart_id"] : false;
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

        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'city' => 'required',
            'phone' => ['required', function ($attribute, $value, $fail) {
                $value = preg_replace("/[^,.0-9]/", '', $value);
                if (strlen($value) < 11) {
                    $fail('The '.$attribute.' is invalid.');
                }
            },]
        ]);

        $ShopCurrency = ShopCurrency::where("default", 1)->first();

        $ShopOrder = new ShopOrder();
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
        $ShopOrder->description = $request->description;
        $ShopOrder->not_call = $request->not_call ?? 0;

        $ShopOrder->save();

        foreach (self::getCart() as $CartItem) {

            $ShopOrderItem = new ShopOrderItem();
            $ShopOrderItem->shop_item_id = $CartItem->id;
            $ShopOrderItem->shop_order_id = $ShopOrder->id;
            $ShopOrderItem->name = $CartItem->name;
            $ShopOrderItem->quantity = $CartItem->quantity;
            $ShopOrderItem->price = $CartItem->price;
            $ShopOrderItem->old_price = $CartItem->attributes["oldPrice"] > 0 ? $CartItem->attributes["oldPrice"] : 0;
            $ShopOrderItem->save();
        }

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

        self::clear();

        $Shop = Shop::get();

        //admin
        Mail::to($Shop->email)->send(new SendOrderAdmin($ShopOrder));

        //client
        Mail::to($ShopOrder->email)->send(new SendOrder($ShopOrder));

        return redirect()->back()->withSuccess("Спасибо! Ваш заказ оформлен!");
    }
}
