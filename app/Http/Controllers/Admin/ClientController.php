<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{

    public static $items_on_page = 15;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('admin.client.index', [
             'clients' => Client::where("deleted", 0)->orderBy("id", "DESC")->paginate(self::$items_on_page),
             'breadcrumbs' => self::breadcrumbs(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.client.create', [
            'breadcrumbs' => self::breadcrumbs(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
 
        return self::saveClient($request);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('admin.client.edit', [
            'client' => $client,
            'breadcrumbs' => self::breadcrumbs(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
  
        return self::saveClient($request, $client);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {

        $client->deleted = 1;
        $client->save();

        return redirect()->back()->withSuccess('Клиент был успешно перемещен в корзину!');
    }

    public function saveClient(Request $request, $client = false)
    {

        if (!$client) {
            $client = new Client();
        }

        $client->name = $request->name;
        $client->email = $request->email;

        if (!empty($request->password_first) && $request->password_first == $request->password_second) {
            $client->password = \Illuminate\Support\Facades\Hash::make($request->password_first);
        }

        $client->save();
        
        $text = 'Клиент был успешно сохранен!';

        if ($request->apply) {
            return redirect(route("client.index"))->withSuccess($text);
        } else {
            return redirect()->back()->withSuccess($text);
        }
    }

    

    public static function breadcrumbs()
    {

        $aResult[0]["name"] = 'Клиенты';
        $aResult[0]["url"] = route("client.index");

        return $aResult;
        
    }
}
