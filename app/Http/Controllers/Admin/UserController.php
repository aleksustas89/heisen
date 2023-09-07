<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public static $items_on_page = 15;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('admin.user.index', [
             'users' => User::paginate(self::$items_on_page),
             'breadcrumbs' => self::getBreadcrumbs(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user.create', [
            'breadcrumbs' => self::getBreadcrumbs(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        return self::saveUser($request);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {

        return view('admin.user.edit', [
            'user' => $user,
            'breadcrumbs' => self::getBreadcrumbs(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        return self::saveUser($request, $user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {

        DB::delete('delete from model_has_roles where model_id=' . $user->id);

        return User::deleteUser($user);
    }

    public static function getBreadcrumbs()
    {

        $aResult[0]["name"] = 'Сотрудники';
        $aResult[0]["url"] = route("user.index");

        return $aResult;
        
    }

    public function saveUser(Request $request, $User = false)
    {

        $bNewUser = false;

        if (!$User) {
            $User = new User();
            $bNewUser = true;
        }

        $oSelect = $User::where("email", $request->email);

        if (!$bNewUser) {
            $oSelect->where("id", "!=", $User->id);
        } 

        if(count($oSelect->get())) {
            return redirect()->back()->withError("Такой E-mail уже зарегистрирован!");
        }

        $User->name = $request->name;
        $User->email = $request->email;
        if (!empty($request->password_first) && $request->password_first == $request->password_second) {
            $User->password = \Illuminate\Support\Facades\Hash::make($request->password_first);
            if (!$bNewUser) {
                $User->force_logout = 1;
            }
        }

        $User->active = $request->active;

        $User->save();

        if ($bNewUser) {
            DB::table('model_has_roles')->insert([
                'role_id' => 1,
                'model_type' => 'App\Models\User',
                'model_id' => $User->id
            ]);
        }

        $text = 'Сотрудник был успешно сохранен!';

        if ($request->apply) {
            return redirect(route("user.index"))->withSuccess($text);
        } else {
            return redirect()->back()->withSuccess($text);
        }
    }
}
