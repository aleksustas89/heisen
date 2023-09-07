<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

//use Auth;

class ClientRegisterController extends Controller
{

    public function __construct()
    {
        //$this->middleware('guest:client');
    }


    public function showRegisterForm()
    {

        return view('client.register');
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($request->password == $request->password_confirmation) {

            $Client = new Client();
            $Client->email = $request->email;
            $Client->password = Hash::make($request->password);
            $Client->name = $request->name;
            $Client->save();

            Auth::guard('client')->login($Client);

            return redirect()->intended(route('client.result'));
        }



    }


}
