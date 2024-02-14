<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Mail;
use App\Models\Mail\RestorePassword;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function showForm()
    {
        return view('client.restore');
    }

    public function restore(Request $request)
    {

        if (!is_null($Client = Client::where("email", $request->email)->first())) {

            $newPass = Str::random(8);

            $Client->password = Hash::make($newPass);
            $Client->save();

            Mail::to($Client->email)->send(new RestorePassword($Client, $newPass));

            return redirect()->back()->withSuccess("Мы выслали новый пароль на указанный E-mail");


        } else {

            return redirect()->back()->withError("Email не найден");
        }


    }
}