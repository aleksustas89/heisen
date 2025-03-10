<?php

namespace App\Http\Controllers\Shop\Payment\Handler;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;

class ShopPaymentHandler7Controller extends Controller
{
    public static function execute(ShopOrder $ShopOrder)
    {

        return view('shop.cart', [
            "success" => 1,
        ]);

    }
}