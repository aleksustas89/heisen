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

class PageController extends Controller
{
    public function index($path)
    {

        $url = "/" . $path;

        $ShopItem = ShopItem::select("id")->where("url", $url)->where("active", 1)->where("deleted", 0)->limit(1);
        $ShopGroup = ShopGroup::select("id")->where("url", $url)->where("active", 1)->where("deleted", 0)->limit(1);
        $Structure = Structure::select("id")->where("url", $url)->where("active", 1)->where("deleted", 0)->limit(1);
        $ShopFilter = ShopFilter::select("id")->where("url", $url)->where("deleted", 0)->limit(1);

        $Page = Page::where(function($query) use ($ShopItem) {
                        $query->where('entity_id', $ShopItem)
                            ->where('type', '=', 2);
                    })

                    ->orWhere(function($query) use ($ShopGroup) {
                        $query->where('entity_id', $ShopGroup)
                              ->where('type', '=', 1);
                    })

                    ->orWhere(function($query) use ($Structure) {
                        $query->where('entity_id', $Structure)
                              ->where('type', '=', 0);
                    })

                    ->orWhere(function($query) use ($ShopFilter) {
                        $query->where('entity_id', $ShopFilter)
                              ->where('type', '=', 6);
                    })

                    ->first();

        // if (!is_null($Page) && $Page->type == 0 && is_null($Page->Structure)) {
        //     return StructureController::show($Page->Structure);
        // }

        // if (!is_null($Page) && $Page->type == 1 && is_null($Page->ShopGroup)) {
        //     return ShopGroupController::show($Page->ShopGroup);
        // }

        // if (!is_null($Page) && $Page->type == 2 && is_null($Page->ShopItem)) {
        //     return ShopItemController::show($Page->ShopItem);
        // }

        // if (!is_null($Page) && $Page->type == 6 && is_null($Page->ShopFilter) && !is_null($ShopGroup = $Page->ShopFilter->ShopGroup)) {
        //     return ShopGroupController::show($ShopGroup, $Page->ShopFilter);
        // }

        // return abort(404);

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

            }

        } else {
            return abort(404);
        }
    }
}
