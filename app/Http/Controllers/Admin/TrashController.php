<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ShopItem;
use App\Models\ShopGroup;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\Structure;
use App\Models\Client;
use App\Models\User;
use App\Models\ShopQuickOrder;
use App\Models\ShopItemProperty;
use App\Models\ShopItemList;
use App\Models\ShopItemListItem;
use App\Models\ShopDelivery;
use App\Models\ShopCurrency;
use App\Models\ShopDeliveryField;
use App\Models\ShopDiscount;
use App\Models\ShopFilter;
use App\Models\ShopPaymentSystem;
use App\Models\Comment;

class TrashController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $models = $this->models();

        if ($request->operation && request()->model && isset($models[request()->model])) {

            $aIds = [];

            $message = '';

            if ($request->items) {
                foreach ($request->items as $key => $on) {
                    $aIds[] = $key;
                }
            }

            switch ($request->operation) {
                case 'restore':
                    if (count($aIds) > 0) {
                        $models[request()->model]["model"]->whereIn("id", $aIds)->update(['deleted' => 0]);
                    } else {
                        $models[request()->model]["model"]->update(['deleted' => 0]);
                    }

                    $message = 'Элементы были успешно восстановлены!';
                break;

                case 'delete':
                    if (count($aIds) > 0) {
                        $Model = $models[request()->model]["model"]->whereIn("id", $aIds);
                    } else {
                        $Model = $models[request()->model]["model"];
                    }

                    foreach ($Model->get() as $Model) {
                        $Model->delete();
                    }

                    $message = 'Элементы были успешно удалены!';
                break;
            }

            return redirect()->back()->withSuccess($message);
        }


        $Items = false;

        if (request()->model) {

            if (isset($models[request()->model])) {
                $Items = $models[request()->model]["model"]->paginate();
            }
        }

        return view("admin.trash.index", [
            "root" => !isset(request()->model) ? true : false,
            "breadcrumbs" => $this->breadcrumbs(),
            "Items" => $Items,
            "models" => $this->models()
        ]);
    }

    protected function models() 
    {
        return [
            "1" => [
                "name" => "Товары интернет-магазина",
                "model" => ShopItem::where("deleted", 1)->where("modification_id", 0),
                "fieldToShow" => "name"
            ],
            "2" => [
                "name" => "Модификации товаров",
                "model" => ShopItem::where("deleted", 1)->where("modification_id", ">", 0),
                "fieldToShow" => "name"
            ],
            "3" => [
                "name" => "Группы интернет-магазина",
                "model" => ShopGroup::where("deleted", 1),
                "fieldToShow" => "name"
            ],

            "4" => [
                "name" => "Заказы",
                "model" => ShopOrder::where("deleted", 1),
                "fieldToShow" => "name"
            ],
            "5" => [
                "name" => "Элементы заказов",
                "model" => ShopOrderItem::where("deleted", 1),
                "fieldToShow" => "name"
            ],
            "6" => [
                "name" => "Быстрые заказы",
                "model" => ShopQuickOrder::where("deleted", 1),
                "fieldToShow" => "name"
            ],
            "7" => [
                "name" => "Структура сайта",
                "model" => Structure::where("deleted", 1),
                "fieldToShow" => "name"
            ],
            "8" => [
                "name" => "Клиенты",
                "model" => Client::where("deleted", 1),
                "fieldToShow" => "name"
            ],
            "9" => [
                "name" => "Сотрудники",
                "model" => User::where("deleted", 1),
                "fieldToShow" => "name"
            ],
            "10" => [
                "name" => "Свойства товаров",
                "model" => ShopItemProperty::where("deleted", 1)
            ],
            "11" => [
                "name" => "Списки магазина",
                "model" => ShopItemList::where("deleted", 1),
                "fieldToShow" => "name"
            ],
            "12" => [
                "name" => "Элементы списков магазина",
                "model" => ShopItemListItem::where("deleted", 1),
                "fieldToShow" => "value"
            ],
            "13" => [
                "name" => "Валюты магазина",
                "model" => ShopCurrency::where("deleted", 1),
                "fieldToShow" => "name"
            ],
            "14" => [
                "name" => "Доставки",
                "model" => ShopDelivery::where("deleted", 1),
                "fieldToShow" => "name"
            ],
            "15" => [
                "name" => "Поля доставок",
                "model" => ShopDeliveryField::where("deleted", 1),
                "fieldToShow" => "caption"
            ],
            "16" => [
                "name" => "Скидки",
                "model" => ShopDiscount::where("deleted", 1),
                "fieldToShow" => "name"
            ],
            "17" => [
                "name" => "Статические фильтры",
                "model" => ShopFilter::where("deleted", 1),
                "fieldToShow" => "url"
            ],
            "18" => [
                "name" => "Платежные системы",
                "model" => ShopPaymentSystem::where("deleted", 1),
                "fieldToShow" => "name"
            ],
            "19" => [
                "name" => "Комментарии",
                "model" => Comment::where("deleted", 1),
                "fieldToShow" => "subject"
            ],

        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        dd(2222);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        dd(2222);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        dd(2222);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        dd(2222);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        dd(2222);
    }

    public function breadcrumbs()
    {

        $models = $this->models();

        $aResult[0]["name"] = 'Корзина';
        $aResult[0]["url"] = route("trash.index");

        if (request()->model && isset($models[request()->model])) {
            $aResult[1]["name"] = $models[request()->model]['name'];
        }

        return $aResult;
        
    }
}
