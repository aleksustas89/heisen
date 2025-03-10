<?php

namespace App\Http\Controllers;

use App\Models\Informationsystem;

class InformationsystemController extends Controller
{

    public static function show($informationsystem)
    {

        return view('informationsystems/informationsystem_' . $informationsystem->id . '/index', [
            "informationsystem" => $informationsystem,
            "informationsystemItems" => $informationsystem->InformationsystemItems()->where("active", 1)->orderBy("created_at", "DESC")->paginate(),
            "seo_title" => !empty($informationsystem->seo_title) ? $informationsystem->seo_title : $informationsystem->name,
            "seo_description" => $informationsystem->seo_description,
            "seo_keywords" => $informationsystem->seo_keywords,
            "breadcrumbs" => BreadcrumbsController::breadcrumbs(self::breadcrumbs($informationsystem, false), [], false),
            'menuGroups' => \App\Http\Controllers\ShopController::buildGroupTree(),
        ]);
    }

    public static function getByPath()
    {
        return Informationsystem::wherePath(request()->path())->where("active", 1)->first();
    }

    public static function breadcrumbs($informationsystem, $firstElemIsActive = true)
    {

        $aResult = [];

        $Result["name"] = $informationsystem->name;

        if ($firstElemIsActive) {
            $Result["url"] = "/" . $informationsystem->path;
        }
        
        array_unshift($aResult, $Result);

        return $aResult;
    }

}
