<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Models\ShopItemProperty;
use App\Models\PropertyValueInt;
use Illuminate\Http\Request;
use App\Models\ShopItem;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ShopDiscountController;
use App\Models\Comment;
use App\Models\CommentShopItem;
use App\Models\Shop;
use App\Models\ShopItemAssociatedItem;

class ShopItemController extends Controller
{

    public static function quickBuy(ShopItem $shopItem)
    {

        if (!is_null($shopItem)) {

            $CartController = new CartController();
            $CartController->add($shopItem, 1);

            header("Location: /cart");
            exit();
        }
    }

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

        $Comments = Comment::select("comments.*")
            ->join("comment_shop_items", "comments.id", "=", "comment_shop_items.comment_id")
            ->where("comment_shop_items.shop_item_id", $shopItem->id)
            ->where("comments.active", 1)
            ->get();

        //габариты
        $aDimensions = [];
        $k = 0;
        // if ($shopItem->weight > 0) {
        //     $aDimensions[$k]["name"] = "Вес";
        //     $aDimensions[$k]["value"] = $shopItem->weight;
        //     $aDimensions[$k]["measure"] = "гр";
        //     $k++;
        // }
        if ($shopItem->width > 0) {
            $aDimensions[$k]["name"] = "Ширина";
            $aDimensions[$k]["value"] = $shopItem->width / 10;
            $aDimensions[$k]["measure"] = "см";
            $k++;
        }
        if ($shopItem->height > 0) {
            $aDimensions[$k]["name"] = "Высота";
            $aDimensions[$k]["value"] = $shopItem->height / 10;
            $aDimensions[$k]["measure"] = "см";
            $k++;
        }
        if ($shopItem->length > 0) {
            $aDimensions[$k]["name"] = "Глубина";
            $aDimensions[$k]["value"] = $shopItem->length / 10;
            $aDimensions[$k]["measure"] = "см";
            $k++;
        }

        $aProperties = [];
        foreach ($ShopItemProperties as $ShopItemProperty) {
            $aProperties[$ShopItemProperty->id] = [];
            $Shop_Item_List_Items = $ShopItemProperty->shopItemList->listItems->whereIn("id", $modListValues);
            foreach ($Shop_Item_List_Items as $Shop_Item_List_Item) {
                $aProperties[$ShopItemProperty->id][] = $Shop_Item_List_Item;
            }
        }

        $Return = [
            'aModProperties' => $ShopItemProperties,
            'aPropertyListItems' => $aProperties,
            'item' => $shopItem,
            'images' => $shopItem->getImages(),
            'breadcrumbs' => BreadcrumbsController::breadcrumbs(self::breadcrumbs($shopItem)),
            'Comments' => $Comments,
            'Dimensions' => $aDimensions,
            'shop' => Shop::get(),
            'imageMask' => $shopItem->name . ", цвет: черный, коричневый, синий, серый, зеленый, бежевый",
            'ShopItemAssociatedItems' => ShopItem::whereIn("shop_items.id", ShopItemAssociatedItem::select("shop_item_associated_id")->where("shop_item_id", $shopItem->id))->where("active", 1)->get(),
        ];

        switch ($shopItem::$priceView) {
            case 0:
                $Return["prices"] = ShopDiscountController::getModificationsPricesWithDiscounts($shopItem);
            break;
            case 1:

                if ($Modification = $shopItem->defaultModification()) {

                    $Return["default_modification_price"] = $Modification->price();
                    $Return["default_modification_old_price"] = $Modification->oldPrice();
                    $Return["Modification"] = $Modification;

                    $ListValues = [];
                    foreach (PropertyValueInt::select("property_value_ints.value")->where("property_value_ints.entity_id", $Modification->id)->get() as $PropertyValueInt) {
                        $ListValues[] = $PropertyValueInt->value;
                    }

                    $Return['aDefaultValues'] = $ListValues;
                }
                
            break;
        }

        Route::view($path, 'shop/item', $Return);
    }

    static public function shopItemValues($shopItem) : array
    {
        $aModValues = PropertyValueInt::select("property_value_ints.value")->whereIn("property_value_ints.entity_id", function ($query) use ($shopItem) {
            $query->select('id')->from('shop_it ems')->where("modification_id", $shopItem->id);
        })->whereNot("value", 0)->get()->toArray();

        $modListValues = [];
        foreach ($aModValues as $aModValue) {
            $modListValues[] = $aModValue["value"];
        }

        return $modListValues;
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
            $response["item"]["oldPrice"] = \App\Services\Helpers\Str::price($aShopItem->oldPrice());
            $response["item"]["image"] = $aShopItem->ShopModificationImage;

        }
        

        return response()->json($response);
    }

    public function saveComment(Request $request)
    {

        $client = Auth::guard('client')->user();

        $comment = new Comment();
        $comment->subject = $request->subject;
        $comment->text = $request->text;
        $comment->author = $request->author;
        $comment->email = $request->email;
        $comment->phone = $request->phone;
        $comment->grade = $request->grade;
        $comment->client_id = !is_null($client) ? $client->id : 0;
        $comment->active = 0;
        $comment->parent_id = $request->parent_id ?? 0;

        $comment->save();

        $CommentShopItem = new CommentShopItem();
        $CommentShopItem->shop_item_id = $request->shop_item_id;
        $CommentShopItem->comment_id = $comment->id;
        $CommentShopItem->save();

        return redirect()->back()->withSuccess("Комментарий был успешно добавлен! После проверки, он станет доступен на сайте.");
    }
}