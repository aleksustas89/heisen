<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShopOrderItem;
use App\Models\ShopItemListItem;
use App\Models\PropertyValueInt;

class StatisticController extends Controller
{

    public $color_shop_item_list_id = 195;

    public $color_property_id = 60;
    
    public function index(Request $request)
    {

        $ShopOrderItems = ShopOrderItem::selectRaw('shop_item_id, SUM(quantity) AS total_quantity')
                                ->groupBy("shop_item_id")
                                ->orderBy("total_quantity", "DESC");
                            
        if ($request->color) {
            $PropertyValueInt = PropertyValueInt::select("entity_id")
                                        ->distinct()
                                        ->where("property_id", $this->color_property_id)
                                        ->where("value", $request->color);

            $ShopOrderItems->whereIn("shop_item_id", $PropertyValueInt);

        }



        return view("admin.shop.statistic.index", [
            "breadcrumbs" => $this->breadcrumbs(),
            "shopOrderItems" => $ShopOrderItems->paginate(),
            "colors" => ShopItemListItem::where("shop_item_list_id", $this->color_shop_item_list_id)->where("deleted", 0)->get(),
            "current_color_id" => $request->color
        ]);
    }

    public function breadcrumbs()
    {
        $aResult[0]["name"] = 'Статистика';
        $aResult[0]["url"] = route("statistic.index");

        return $aResult;
    }
}
