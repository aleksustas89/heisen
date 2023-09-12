<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Structure;
use App\Models\ShopGroup;
use App\Models\ShopItem;

class HomeController extends Controller
{

    public static function index()
    {

        return view('home', [
            'structure' => Structure::where("path", "/")->first(),
            'groups' => ShopGroup::where("parent_id", 0)->where('active', 1)->inRandomOrder()->get(),
            'newItems' => ShopItem::where('active', 1)->where('modification_id', 0)->orderBy("id", "DESC")->limit(8)->get(),
        ]);
    }
}
