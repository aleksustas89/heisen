<?php

namespace App\Http\Controllers;

use App\Models\Informationsystem;
use App\Models\Tag;

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

    public function showTag(Tag $tag)
    {
        $informationsystem = Informationsystem::find(3);


        $InformationsystemItems = $informationsystem
                                        ->InformationsystemItems()
                                        ->select("informationsystem_items.*")
                                        ->join("informationsystem_item_tags", "informationsystem_item_tags.informationsystem_item_id", "=", "informationsystem_items.id")
                                        ->where("informationsystem_items.active", 1)
                                        ->where("informationsystem_item_tags.tag_id", $tag->id)
                                        ->orderBy("informationsystem_items.created_at", "DESC")
                                        ->paginate();

        $breadcrumb[1]["name"] = $tag->name;

        return view('informationsystems/informationsystem_' . $informationsystem->id . '/index', [
            "informationsystem" => $informationsystem,
            "informationsystemItems" => $InformationsystemItems,
            "seo_title" => !empty($informationsystem->seo_title) ? $informationsystem->seo_title : $informationsystem->name,
            "seo_description" => $informationsystem->seo_description,
            "seo_keywords" => $informationsystem->seo_keywords,
            "breadcrumbs" => BreadcrumbsController::breadcrumbs(self::breadcrumbs($informationsystem, true) + $breadcrumb, [], false),
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
