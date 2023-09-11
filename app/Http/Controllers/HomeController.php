<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    public static function index()
    {
        return view('home', [
            'groups' => ShopGroupController::getGroupsTree(),
        ]);
    }
}
