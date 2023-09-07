<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
//use Auth;

class ClientLoginController extends Controller
{

    public function __construct()
    {

    }

    protected function guard()
    {
        return Auth::guard('client');
    }

    public function showLoginForm()
    {

        return view('client.login');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::guard('client')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {

            $client = Auth::guard('client')->user();

            Auth::guard('client')->login($client);

            return redirect()->intended(route('client.result'));

        } else {
            echo "неверные логин или пароль";
        }

        return redirect()->back()->withInput($request->only('email', 'remember'));

    }

    public function logout()
    {
        Auth::guard('client')->logout();

        return redirect()->back();
    }

    public function result()
    {

        return view('client.result');
    }
}
