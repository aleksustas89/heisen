<?php

namespace App\Http\Controllers;

use App\Models\InformationsystemItem;

class InformationsystemItemController extends Controller
{
    public static function show($informationsystemItem)
    {


        return view('informationsystems/informationsystem_' . $informationsystemItem->informationsystem_id . '/item', [
            "informationsystemItem" => $informationsystemItem,
            "seo_title" => !empty($informationsystemItem->seo_title) ? $informationsystemItem->seo_title : $informationsystemItem->name,
            "seo_description" => $informationsystemItem->seo_description,
            "seo_keywords" => $informationsystemItem->seo_keywords,
            "images" => $informationsystemItem->getImages(),
            'menuGroups' => \App\Http\Controllers\ShopController::buildGroupTree(),
            "breadcrumbs" => BreadcrumbsController::breadcrumbs(self::breadcrumbs($informationsystemItem), [], false),
        ]);
    }

    public static function getByPath()
    {
        return InformationsystemItem::whereUrl("/" . request()->path())->where("active", 1)->first();
    }

    public static function breadcrumbs($informationsystemItem)
    {

        
        $aResult = InformationsystemController::breadcrumbs($informationsystemItem->Informationsystem, true);

        $aResult[]["name"] = $informationsystemItem->name;

        return $aResult;
    }
}
