<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ShopQuickOrder;
use Illuminate\Http\Request;
use App\Models\Shop;
use Illuminate\Support\Facades\Mail;
use App\Models\Mail\SendQuickOrder;

class ShopQuickOrderController extends Controller
{
   
    /**
     * frontend function to save for item page
    */
    public function save(Request $request)
    {


        $shopQuickOrder = new shopQuickOrder();
        $shopQuickOrder->name = $request->name;
        $shopQuickOrder->phone = $request->phone;
        $shopQuickOrder->shop_item_id = $request->shop_item_id;
        $shopQuickOrder->save();

        $Shop = Shop::get();

        Mail::to($Shop->email)->send(new SendQuickOrder($shopQuickOrder));

        return response()->json('<div class="uk-alert-success uk-alert" uk-alert=""><p>Спасибо! Наши менеджеры свяжутся с Вами в ближайшее время!</p></div>');

    }
}
