<?php

namespace App\Http\Controllers\Shop\Payment\Handler;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Http\Controllers\CartController;

class ShopPaymentHandler2Controller extends Controller
{
    public static function execute(ShopOrder $ShopOrder)
    {

        $CartController = new CartController();

        return view('shop.cart', [
            "success" => 1,
            "paymentUrl" => $CartController->preparePayment($ShopOrder),
        ]);

    }
}