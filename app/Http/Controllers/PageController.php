<?php

namespace App\Http\Controllers;
use App\Models\Page;
use App\Models\ShopItem;
use App\Models\ShopGroup;
use App\Models\Structure;
use App\Models\Informationsystem;
use App\Models\InformationsystemItem;
use App\Models\ShopFilter;
use App\Http\Controllers\ShopItemController;
use App\Http\Controllers\ShopGroupController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\InformationsystemController;
use App\Http\Controllers\InformationsystemItemController;

class PageController extends Controller
{
    public function index($path)
    {

        $url = "/" . $path;

        $Page = Page::where("entity_id",   ShopItem::select("id")->where("url", $url)->where("active", 1)->limit(1))
                    ->orWhere("entity_id", ShopGroup::select("id")->where("url", $url)->where("active", 1)->limit(1))
                    ->orWhere("entity_id", Structure::select("id")->where("url", $url)->where("active", 1)->limit(1))
                    ->orWhere("entity_id", ShopFilter::select("id")->where("url", $url)->limit(1))
                    // ->orWhere("entity_id", Informationsystem::select("id")->where("path", $path)->where("active", 1)->limit(1))
                    // ->orWhere("entity_id", InformationsystemItem::select("id")->where("url", $url)->where("active", 1)->limit(1))
                    ->first();

        if (!is_null($Page) && (
            !is_null($Page->Structure) || !is_null($Page->ShopGroup) || !is_null($Page->ShopItem) || !is_null($Page->ShopFilter))
        ) {

            switch ($Page->type) {
                case 0: 
                    return StructureController::show($Page->Structure);
                break;
                    
                case 1: 
                    return ShopGroupController::show($Page->ShopGroup);
                break;

                case 2:
                    return ShopItemController::show($Page->ShopItem);
                break;

                case 6:

                    if (!is_null($Page->ShopFilter) && !is_null($ShopGroup = $Page->ShopFilter->ShopGroup)) {
                        return ShopGroupController::show($ShopGroup, $Page->ShopFilter);
                    }
                break;

                // case 3:
                //     return InformationsystemController::show($Page->Informationsystem);
                // break;

                // case 5:
                //     echo 123;
                //     return InformationsystemItemController::show($Page->InformationsystemItem);
                // break;
            }

        } else {
            return abort(404);
        }
    }
}
