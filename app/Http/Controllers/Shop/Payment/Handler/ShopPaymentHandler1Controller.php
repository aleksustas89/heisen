<?php

namespace App\Http\Controllers\Shop\Payment\Handler;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShopOrder;

class ShopPaymentHandler1Controller extends Controller
{
    public static function execute(ShopOrder $ShopOrder)
    {

        return view('shop.cart', [
            "success" => 1,
        ]);

    }
}