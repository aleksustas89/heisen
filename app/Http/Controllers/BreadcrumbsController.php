<?php

namespace App\Http\Controllers;

class BreadcrumbsController extends Controller
{

    public static function breadcrumbs($aResult = [])
    {

        $Result["name"] = "Главная";
        $Result["url"] = "/";

        array_unshift($aResult, $Result);

        return $aResult;
    }
}