<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Models\ShopItemProperty;
use App\Models\PropertyValueInt;
use Illuminate\Http\Request;
use App\Models\ShopItem;

class ShopItemController extends Controller
{

    static public function show($path, $shopItem)
    {
        $ShopItemProperties = ShopItemProperty::select("shop_item_properties.*")
                                    ->join("property_value_ints", "property_value_ints.property_id", "=", "shop_item_properties.id")
                                    ->whereIn("property_value_ints.entity_id", function ($query) use ($shopItem) {
                                        $query->select('id')->from('shop_items')->where("modification_id", $shopItem->id);
                                    })
                                    ->groupBy("property_value_ints.property_id")
                                    ->whereNot("property_value_ints.value", 0)
                                    ->get();

                                    
        $aModValues = PropertyValueInt::select("property_value_ints.value")->whereIn("property_value_ints.entity_id", function ($query) use ($shopItem) {
            $query->select('id')->from('shop_items')->where("modification_id", $shopItem->id);
        })->whereNot("value", 0)->get()->toArray();

        $modListValues = [];
        foreach ($aModValues as $aModValue) {
            $modListValues[] = $aModValue["value"];
        }

        Route::view($path, 'shop/item', [
            'aModProperties' => $ShopItemProperties,
            'aModValues' => $modListValues,
            'item' => $shopItem,
            'breadcrumbs' => BreadcrumbsController::breadcrumbs(self::breadcrumbs($shopItem)),
        ]);
    }

    public static function breadcrumbs($shopItem)
    {

        $breadcrumbs = ShopGroupController::breadcrumbs($shopItem->ShopGroup, []);

        return $breadcrumbs + [count($breadcrumbs) => ["name" => $shopItem->name]];
    }

    public function getModification(Request $request)
    {

        $response = [];

        if ($request->shop_item_id) {

            $aProperties = [];
            foreach ($request->all() as $k => $input) {
                $e = explode("_", $k);
                if (isset($e[1]) && $e[0] == 'property') {
                    $aProperties[$e[1]] = $input;
                }
            }

            $ShopItem = ShopItem::select("shop_items.*");
            $ShopItem
                ->join("property_value_ints", "property_value_ints.entity_id", "=", "shop_items.id")
                ->where("shop_items.modification_id", $request->shop_item_id)
                ->where(function($query) use ($aProperties) {
                    foreach ($aProperties as $k => $aProperty) {
                        $query->orWhere(function($query) use ($k, $aProperty) {
                            $query->where("property_value_ints.property_id", $k)
                                ->where("property_value_ints.value", $aProperty);
                        });
                    }
                })
                ->havingRaw('COUNT(property_value_ints.property_id) = ' . count($aProperties))->groupBy("shop_items.id");

            $aShopItem = $ShopItem->first();

            $response["item"]["id"] = $aShopItem->id;
            $response["item"]["name"] = $aShopItem->name;
            $response["item"]["price"] = \App\Services\Helpers\Str::price($aShopItem->price());
            $response["item"]["image"] = $aShopItem->ShopModificationImage;

        }
        

        return response()->json($response);
    }
}