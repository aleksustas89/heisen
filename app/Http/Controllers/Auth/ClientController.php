<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use App\Models\ClientFavorite;
use App\Models\ShopItem;

class ClientController extends Controller
{

    public static $items_on_page = 15;

    protected function guard()
    {
        return Auth::guard('client');
    }

    static public function show()
    {

        return view('client.account', [
            "client" => Auth::guard('client')->user()
        ]);
    }

    static public function execute(Request $request)
    {

        $oClient = Auth::guard('client')->user();

        if (!is_null(Client::where("id", "!=", $oClient->id)->where("email", $request->email)->first())) {
            return redirect()->back()->withError("Такой email уже был зарегистрирован ранее!");
        }

        $oClient->name = $request->name;
        $oClient->surname = $request->surname;
        $oClient->email = $request->email;
        $oClient->phone = $request->phone;

        if (!empty($request->password) && $request->password == $request->password_confirmation) {
            $oClient->password = Hash::make($request->password);;
        }

        $oClient->save();

        return redirect()->back()->withSuccess("Данные были успешно измененны!");


    }

    public function logout()
    {
        Auth::guard('client')->logout();

        return redirect()->back();
    }

    public function orders()
    {
        return view('client.order', [
            "client" => Auth::guard('client')->user()
        ]);
    }

    public function favorites()
    {
        $client = Auth::guard('client')->user();
        $clientFavorites = !is_null($client) ? $client->getClientFavorites() : [];
        return view('client.favorite', [
            "client" => $client,
            'clientFavorites' => $clientFavorites,
            'shopItems' => count($clientFavorites) > 0 ? ShopItem::whereIn("id", $clientFavorites)->where("active", 1)->paginate(self::$items_on_page) : false,
        ]);
    }

    public function addFavorite(Request $request)
    {
        if (!is_null($Client = Auth::guard('client')->user())) {

            $ClientFavorite = ClientFavorite::where("shop_item_id", $request->shop_item_id)
                                                ->where("client_id", $Client->id)->first();
            if (is_null($ClientFavorite)) {
                $ClientFavorite = new ClientFavorite();
                $ClientFavorite->shop_item_id = $request->shop_item_id;
                $ClientFavorite->client_id = $Client->id;
                $ClientFavorite->save();

            } else {
                $ClientFavorite->delete();
                
            }

            return response()->json(["count" => ClientFavorite::where("client_id", $Client->id)->count()]);
        }
    }
}