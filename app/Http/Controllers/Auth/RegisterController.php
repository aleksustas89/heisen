<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('guest');
    // }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:clients'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['required', function ($attribute, $value, $fail) {
                $value = preg_replace("/[^,.0-9]/", '', $value);
                if (strlen($value) < 11) {
                    $fail('The '.$attribute.' is invalid.');
                }
            },]
        ]);

        if ($request->password == $request->password_confirmation) {

            $Client = new Client();
            $Client->email = $request->email;
            $Client->password = Hash::make($request->password);
            $Client->name = $request->name;
            $Client->surname = $request->surname;
            $Client->phone = $request->phone;
            $Client->save();

            Auth::guard('client')->login($Client);

            return redirect()->intended(route('clientAccount'));
        }
    }

    public function registerForm()
    {
        return view('client.register');
    }
}
