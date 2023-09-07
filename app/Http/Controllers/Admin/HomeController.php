<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {

        $posts_count = 10;

        return view('admin.home.index', [
            'posts_count' => $posts_count
        ]);
    }
}
