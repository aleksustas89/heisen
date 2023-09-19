<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Structure;
use App\Models\ShopGroup;
use App\Models\ShopItem;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public static function index()
    {

        $client = Auth::guard('client')->user();

        return view('home', [
            'structure' => Structure::where("path", "/")->first(),
            'groups' => ShopGroup::where("parent_id", 0)->where('active', 1)->inRandomOrder()->get(),
            'newItems' => ShopItem::where('active', 1)->where('modification_id', 0)->orderBy("id", "DESC")->limit(8)->get(),
            'client' => $client,
            'clientFavorites' => !is_null($client) ? $client->getClientFavorites() : [],
        ]);
    }
}
