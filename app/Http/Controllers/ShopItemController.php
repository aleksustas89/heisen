<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

class ShopItemController extends Controller
{

    static public function show($path, $shopItem)
    {

        Route::view($path, 'shop/item', [
            'item' => $shopItem,
            'breadcrumbs' => BreadcrumbsController::breadcrumbs(self::breadcrumbs($shopItem)),
        ]);
    }

    public static function breadcrumbs($shopItem)
    {

        $breadcrumbs = ShopGroupController::breadcrumbs($shopItem->ShopGroup, []);

        return $breadcrumbs + [count($breadcrumbs) => ["name" => $shopItem->name]];
    }
}