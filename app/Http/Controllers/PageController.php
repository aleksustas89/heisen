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
        $Informationsystem = Informationsystem::select("id")->where("path", $path)->where("active", 1)->limit(1);

        $Page = Page::where(function($query) use ($ShopItem) {
                        $query->where('entity_id', $ShopItem)
                            ->where('type', '=', Page::getType(ShopItem::class));
                    })

                    ->orWhere(function($query) use ($ShopGroup) {
                        $query->where('entity_id', $ShopGroup)
                              ->where('type', '=', Page::getType(ShopGroup::class));
                    })

                    ->orWhere(function($query) use ($Structure) {
                        $query->where('entity_id', $Structure)
                              ->where('type', '=', Page::getType(Structure::class));
                    })

                    ->orWhere(function($query) use ($ShopFilter) {
                        $query->where('entity_id', $ShopFilter)
                              ->where('type', '=', Page::getType(ShopFilter::class));
                    })

                    ->orWhere(function($query) use ($Informationsystem) {
                        $query
                            ->where("entity_id", $Informationsystem)
                            ->where("type", Page::getType(Informationsystem::class));
                    })
                    ->orWhere(function($query) use ($url) {
                        $query
                            ->where("entity_id", InformationsystemItem::select("id")->where("url", $url)->where("active", 1)->limit(1))
                            ->where("type", Page::getType(InformationsystemItem::class));
                    }) 
                    ->first();

        if (!is_null($Page) && (
            !is_null($Page->Structure) || !is_null($Page->ShopGroup) || !is_null($Page->ShopItem) || !is_null($Page->ShopFilter) || !is_null($Page->Informationsystem) || !is_null($Page->InformationsystemItem))
        ) {

            switch ($Page->type) {
                case Page::getType(Structure::class): 
                    return StructureController::show($Page->Structure);
                break;
                    
                case Page::getType(ShopGroup::class): 
                    return ShopGroupController::show($Page->ShopGroup);
                break;

                case Page::getType(ShopItem::class):
                    return ShopItemController::show($Page->ShopItem);
                break;

                case Page::getType(Informationsystem::class):
                    return InformationsystemController::show($Page->Informationsystem);
                break;

                case Page::getType(InformationsystemItem::class):
                    return InformationsystemItemController::show($Page->InformationsystemItem);
                break;

                case Page::getType(ShopFilter::class):

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
