<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Models\Shop;
use App\Models\ShopQuickOrder;

class HomeController extends Controller
{
    public function index()
    {

        return view('admin.home.index', [
            'shop' => Shop::get(),
            'orders' => ShopOrder::orderBy("created_at", "Desc")->where("deleted", 0)->paginate(10),
            'quick_orders' => ShopQuickOrder::where("deleted", 0)->orderBy("created_at", "Desc")->paginate(10),
        ]);
    }
}
