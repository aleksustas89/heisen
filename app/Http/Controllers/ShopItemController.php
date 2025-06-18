<?php

namespace App\Http\Controllers;

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

    public static function getItem($shopItem)
    {
        $ParentItem = $shopItem->parentItemIfModification();

        $ShopItemProperties = ShopItemProperty::select("shop_item_properties.*")
                                    ->join("property_value_ints", "property_value_ints.property_id", "=", "shop_item_properties.id")
                                    ->whereIn("property_value_ints.entity_id", function ($query) use ($ParentItem) {
                                        $query->select('id')->from('shop_items')->where("modification_id", $ParentItem->id)->where("active", 1)->where("deleted", 0);
                                    })
                                    ->where('shop_item_properties.deleted', 0)
                                    ->groupBy("property_value_ints.property_id")
                                    ->whereNot("property_value_ints.value", 0)
                                    ->get();

                                    
        $aModValues = PropertyValueInt::select("property_value_ints.value")->whereIn("property_value_ints.entity_id", function ($query) use ($ParentItem) {
            $query->select('id')->from('shop_items')->where("modification_id", $ParentItem->id);
        })->whereNot("value", 0)->get()->toArray();

        $modListValues = [];
        foreach ($aModValues as $aModValue) {
            
            if (!array_search($aModValue["value"], $modListValues)) {
                $modListValues[] = $aModValue["value"];
            }
        }

        $Comments = Comment::select("comments.*")
            ->join("comment_shop_items", "comments.id", "=", "comment_shop_items.comment_id")
            ->where("comment_shop_items.shop_item_id", $ParentItem->id)
            ->where("comments.active", 1)
            ->where("comments.deleted", 0)
            ->get();

        //габариты
        $aDimensions = [];
        $k = 0;

        if ($ParentItem->width > 0) {
            $aDimensions[$k]["name"] = "Ширина";
            $aDimensions[$k]["value"] = (int)$ParentItem->width;
            $aDimensions[$k]["measure"] = "см";
            $k++;
        }
        if ($ParentItem->height > 0) {
            $aDimensions[$k]["name"] = "Высота";
            $aDimensions[$k]["value"] = (int)$ParentItem->height;
            $aDimensions[$k]["measure"] = "см";
            $k++;
        }
        if ($ParentItem->length > 0) {
            $aDimensions[$k]["name"] = "Глубина";
            $aDimensions[$k]["value"] = (int)$ParentItem->length;
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

        $ImgAltTitleColor = '';

        if (!is_null($PropertyValueColor = PropertyValueInt::where("entity_id", $shopItem->id)->where("property_id", 60)->first()) && $PropertyValueColor->value > 0) {
            $ImgAltTitleColor = ", " . mb_strtolower($PropertyValueColor->ShopItemListItem->value);
        }

        $ImgAltTitle = $ParentItem->name . " из натуральной кожи" . $ImgAltTitleColor . ". " . $ParentItem->ShopGroup->name;

        $Return = [
            'defaultModification' => ShopItem::where("modification_id", $ParentItem->id)->where("default_modification", 1)->first(),
            'aModProperties' => $ShopItemProperties,
            'aPropertyListItems' => $aProperties,
            'item' => $ParentItem,
            'images' => $ParentItem->getImages(),
            'breadcrumbs' => BreadcrumbsController::breadcrumbs(self::breadcrumbs($shopItem)),
            'Comments' => $Comments,
            'Dimensions' => $aDimensions,
            'shop' => Shop::get(),
            'imageMask' => $ImgAltTitle,
            'ShopItemAssociatedItems' => ShopItem::whereIn("shop_items.id", ShopItemAssociatedItem::select("shop_item_associated_id")->where("shop_item_id", $ParentItem->id))->where("active", 1)->get(),
            'shopItemShortcuts' => $ParentItem->ShopItemShortcuts
        ];



        $Return["default_modification_price"] = $shopItem->price();
        $Return["default_modification_old_price"] = $shopItem->oldPrice();
        $Return["Modification"] = $shopItem->modification_id > 0 ? $shopItem : false;

        $ListValues = [];
        foreach (PropertyValueInt::select("property_value_ints.value")->where("property_value_ints.entity_id", $shopItem->id)->where("value", ">", 0)->get() as $PropertyValueInt) {
            if (!in_array($PropertyValueInt->value, $ListValues)) {
                $ListValues[] = $PropertyValueInt->value;
            }
        }

        $Return['aDefaultValues'] = $ListValues;

        return $Return;
    }

    static public function show($shopItem, $default_modification_id = false)
    {

        return view('shop/item', self::getItem($shopItem, $default_modification_id));
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

        $breadcrumbs = ShopGroupController::breadcrumbs($shopItem->parentItemIfModification()->ShopGroup, []);

        return $breadcrumbs + [count($breadcrumbs) => ["name" => $shopItem->name]];
    }

    public function getModification(Request $request)
    {

        if ($request->shop_item_id) {

            $shopItem = ShopItem::find($request->shop_item_id);

            list($marking) = explode("_", $shopItem->marking);

            $aProperties = [];
            foreach ($request->all() as $k => $input) {
                $e = explode("_", $k);
                if (isset($e[1]) && $e[0] == 'property') {
                    $aProperties[$e[1]] = $input;
                }
            }

            //сначала проверяем, есть ли такой товар по цвету и артикулу
            $ShopItem = ShopItem::select("shop_items.*");
            $ShopItem
                ->join("property_value_ints", "property_value_ints.entity_id", "=", "shop_items.id")
                ->where("shop_items.marking", "LIKE", $marking . "%")
                ->where("shop_items.default_modification", 1)
                ->where("shop_items.deleted", 0)
                ->where(function($query) use ($aProperties) {
                    foreach ($aProperties as $k => $aProperty) {
                        $query->orWhere(function($query) use ($k, $aProperty) {
                            $query->where("property_value_ints.property_id", $k)
                                ->where("property_value_ints.value", $aProperty);
                        });
                    }
                })
                ->havingRaw('COUNT(property_value_ints.property_id) = ' . count($aProperties))->groupBy("shop_items.id");

            
            if (!is_null($aShopItem = $ShopItem->first())) {
                return response()->view('shop/item-content', self::getItem($aShopItem));
            }
   
            //ищем модификацию у основного товара
            $ShopItem = ShopItem::select("shop_items.*");
            $ShopItem
                ->join("property_value_ints", "property_value_ints.entity_id", "=", "shop_items.id")
                ->where("shop_items.modification_id", $request->shop_item_id)
                ->where("shop_items.deleted", 0)
                ->where(function($query) use ($aProperties) {
                    foreach ($aProperties as $k => $aProperty) {
                        $query->orWhere(function($query) use ($k, $aProperty) {
                            $query->where("property_value_ints.property_id", $k)
                                ->where("property_value_ints.value", $aProperty);
                        });
                    }
                })
                ->havingRaw('COUNT(property_value_ints.property_id) = ' . count($aProperties))->groupBy("shop_items.id");

            if (!is_null($aShopItem = $ShopItem->first())) {
                return response()->view('shop/item-content', self::getItem($aShopItem));
            }
        }
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