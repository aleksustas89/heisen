<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Structure;
use App\Models\ShopGroup;
use App\Models\ShopItem;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;

class HomeController extends Controller
{

    public static function index()
    {

        $client = Auth::guard('client')->user();

        

        return view('home', [
            'structure' => Structure::where("path", "/")->first(),
            'groups' => ShopGroup::where("parent_id", 0)->where('active', 1)->inRandomOrder()->get(),
            'newItems' => ShopItem::where('active', 1)->where('modification_id', 0)->inRandomOrder()->limit(15)->get(),
            'discountItems' => ShopItem::where('active', 1)->where('modification_id', 0)->where('discounts', 1)->inRandomOrder()->limit(15)->get(),
            'client' => $client,
            'bottom_text' => Shop::get()->description,
            'clientFavorites' => !is_null($client) ? $client->getClientFavorites() : [],
            'Comments' => Comment::where("active", 1)->where("parent_id", 0)->where("grade", 5)->inRandomOrder()->limit(15)->get(),
        ]);
    }
}
