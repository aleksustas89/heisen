<?php

namespace App\Http\Controllers\Shop\Payment\Handler;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShopPaymentSystem;
use App\Models\ShopOrder;

class ShopPaymentHandlerController extends Controller
{

    static public function factory(ShopPaymentSystem $ShopPaymentSystem)
	{
        if (class_exists($name = '\App\Http\Controllers\Shop\Payment\Handler\ShopPaymentHandler' . intval($ShopPaymentSystem->id) . 'Controller'))
        {
            return new $name();
        }

        return NULL;

	}
}