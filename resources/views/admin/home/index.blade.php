@extends('admin/main')

@section('title', 'Главная')

@section('content')



<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">

                <h4 class="card-title">Последние заказы</h4>

                <a href="{{ route("shop-order.index") }}" class="btn btn-de-primary btn-sm">Все</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 1%">№</th>
                            <th style="width: 170px">Дата</th>
                            {{-- <th style="width: 40px" class="d-mob-none">Источник</th> --}}
                            <th class="d-mob-none">Фио</th>
                            <th style="width: 50px" class="d-mob-none"><i data-feather="list"></i></th>
                            <th class="d-mob-none" style="width: 120px">Доставка</th>
                            <th class="d-mob-none" style="width: 40px">-</th>
                            <th style="width: 100px">Сумма</th>
                            <th style="width: 100px" class="d-mob-none">Оплачено</th>
                            <th class="controll-td"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)

                            @php
                                $fio = implode(" ", [$order->surname, $order->name]);
                                $source = '';
                                if ($order->source_id > 0) {
                                    
                                    switch ($order->Source->service) {
                                        case 'google':
                                            $source = '<span title="google" class="badge badge-ico badge-blue white"><i class="fab fa-google"></i></span>';
                                        break;
                                    }
                                }

                                $sOrderSum = $order->getSum();
                                $sOrderCurrency = $order->ShopCurrency->name;

                            @endphp

                            <tr @class(["tr-paid" => $order->paid == 1 ? true : false])>
                                <td>
                                    {{ $order->id }}
                                </td>
                                <td style="width: 170px" >
                                    {{ date("d.m.Y H:i", strtotime($order->created_at)) }}
                                </td>
                                {{-- <td style="width: 40px" class="d-mob-none">
                                    {!! $source !!}
                                </td> --}}
                                <td class="d-mob-none">
                                    {{ $fio }}
                                </td>
                                <td class="d-mob-none">

                                    @php
                                        
                                        $popover = "<div class='card'>";

                                        $popover .= "<div class='card-header'><h4 class='card-title'>Заказ № ". $order->id ." </h4>";

                                        if (!empty($fio)) {
                                            $popover .= "<p class='text-muted mb-0'>
                                                        <b>Контактное лицо:</b>  ". $fio ."
                                                    </p>";
                                        }

                                        if (!empty($order->phone)) {
                                            $popover .= "<p class='text-muted mb-0'>
                                                            <b>Телефон:</b>  ". $order->phone ."
                                                        </p>";
                                        }

                                        if (!empty($order->email)) {
                                            $popover .= "<p class='text-muted mb-0'>
                                                            <b>E-mail:</b>  ". $order->email ."
                                                        </p>";
                                        }

                                        if ($order->shop_delivery_id > 0 && $order->ShopDelivery) {
                                            $popover .= "<p class='text-muted mb-0'>
                                                            <b>Способ доставки:</b>  ". $order->ShopDelivery->name ."
                                                        </p>";
                                        }

                                        
                                        if ($order->shop_payment_system_id > 0 && $order->ShopPaymentSystem) {
                                            $popover .= "<p class='text-muted mb-0'>
                                                            <b>Способ оплаты:</b>  ". $order->ShopPaymentSystem->name ."
                                                        </p>";
                                        }

                                        if (!empty($order->description)) {
                                            $popover .= "<p class='text-muted mb-0'>
                                                            <b>Описание заказа:</b>  ". $order->description ."
                                                        </p>";
                                        }

                                        if (!empty($order->delivery_information)) {
                                            $popover .= "<p class='text-muted mb-0'>
                                                            <b>Описание доставки:</b>  ". $order->delivery_information ."
                                                        </p>";
                                        }

                                        $popover .= "</div>";

                                        $popover .= "<div class='card-body'><div class='table-responsive'>
                                                         <table class='table table-striped table-sm' style='min-width:700px'>
                                                            <thead class='bordered-palegreen'>
                                                                <tr>
                                                                    <th>№</th>
                                                                    <th>Наименование</th>
                                                                    <th>Цена, ". $sOrderCurrency ."</th>
                                                                    <th>Кол-во</th>
                                                                    <th>Сумма, ". $sOrderCurrency ."</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>";
                                            foreach ($order->ShopOrderItems()->where("deleted", 0)->get()  as $orderItem) {
                                                $popover .= "
                                                                <tr>
                                                                    <td>" . $orderItem->id . "</td>
                                                                    <td>" . (str_replace('"', "", $orderItem->name)) . "</td>
                                                                    <td>" . $orderItem->price . "</td>
                                                                    <td>" . $orderItem->quantity . "</td>
                                                                    <td>" . App\Models\Str::price($orderItem->price * $orderItem->quantity) . "</td>
                                                                </tr>";
                                            }

                                            $popover .= "
                                                                <tr class='bg-black text-white'> 
                                                                    <td>&nbsp;</td>
                                                                    <td>&nbsp;</td>
                                                                    <td>&nbsp;</td>
                                                                    <td>Всего к оплате:</td>
                                                                    <td><b>" . App\Models\Str::price($sOrderSum) ."  ". $sOrderCurrency . "</b></td>
                                                                </tr>";


                                                $popover .= "</tbody>
                                                        <table>
                                                    </div></div>";

                                        $popover .= "</div></div>";
                                    @endphp

                                    <a class="pointer" data-container="body" data-toggle="popover" data-placement="left" data-content="{!! $popover !!}" data-original-title="" title="">
                                        <i data-feather="list"></i>
                                    </a>
                                    
                                </td>
                                <td class="d-mob-none" style="width: 120px">

                                    @if ($order->shop_delivery_id > 0)
                                        <span class="btn-outline-{{ $order->ShopDelivery->color }}" style="font-size:12px;">{{ $order->ShopDelivery->name }}</span>
                                    @endif
            
                                </td>
                                <td class="d-mob-none" style="width: 40px">
                                    @if ($order->shop_delivery_id > 0)
                                        @php
                                            switch ($order->shop_delivery_id) {
                                                case 1:
                                                    $Code = $order->PrOrder;
                                                break;
                                                case 7:
                                                    $Code = $order->CdekOrder;
                                                break;
                                                case 8:
                                                    $Code = $order->BoxberryOrder;
                                                break;
                                            }

                                        @endphp
                                        <a @if(!is_null($Code)) href="javascript:void(0)" onclick="Copy.init($(this), '{{ $Code->track }}')" @endif @class([
                                            "order-not-created" => empty($Code->track) ? true : false,
                                            "order-created" => !empty($Code->track) ? true : false,
                                            "position-relative"
                                        ])>
                                            
                                            <i class="las la-truck"></i>
                                        </a>
                                    @endif
                                </td>
                                <td style="width: 100px">
                                    {{ App\Models\Str::price($sOrderSum) }} {{ $sOrderCurrency }}
                                </td>
                                <td class="d-mob-none text-center" style="width: 100px">
                                    @if ($order->paid == 1)
                                        <i style="color: green; font-size: 22px;" class="las la-check-double"></i>
                                    @endif
                                </td>
                                <td class="td-actions">
                                    <a href="{{ route('shop-order.edit', $order->id) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                    <form action="{{ route('shop-order.destroy', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete($(this).parents('form'))" class="td-list-delete-btn">
                                            <i class="las la-trash-alt text-secondary font-16"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="card-title">Последние быстрые заказы</h4>

                <a href="{{ route("shop.shop-quick-order.index", ['shop' => $shop->id]) }}" class="btn btn-de-primary btn-sm">Все</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 1%">№</th>
                            <th style="width: 170px">Дата</th>
                            <th>Фио</th>
                            <th style="width: 200px" class="d-mob-none">Телефон</th>
                            <th class="d-mob-none">Товар</th>
                            <th class="controll-td"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quick_orders as $quick_order)

                        @if (!is_null($ShopItem = $quick_order->ShopItem))

                            @php
                            $ShopItem = $ShopItem->parentItemIfModification();

                            @endphp

                            <tr>
                                <td>
                                    {{ $quick_order->id }}
                                </td>
                                <td style="width: 170px" >
                                    {{ date("d.m.Y H:i", strtotime($quick_order->created_at)) }}
                                </td>
                                <td>
                                    {{ $quick_order->name }}
                                </td>
                                <td class="d-mob-none" style="width: 200px">
                                    {{ $quick_order->phone }}
                                </td>
                                <td class="d-mob-none">
                                    @if (!is_null($quick_order->ShopItem))
                                        {{ implode(", ", $quick_order->ShopItem->modificationName()) }}
                                        <a href="{{ $ShopItem->url }}" target="_blank">
                                            <i class="las la-external-link-alt"></i>
                                        </a>
                                    @endif
                                </td>
                                <td class="td-actions">
                                    <a href="{{ route('shop.shop-quick-order.edit', ['shop' => $shop->id, 'shop_quick_order' => $quick_order->id]) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                    <form action="{{ route('shop.shop-quick-order.destroy', ['shop' => $shop->id, 'shop_quick_order' => $quick_order->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete($(this).parents('form'))" class="td-list-delete-btn">
                                            <i class="las la-trash-alt text-secondary font-16"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        @endif

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@endsection

@section('css')
    <link rel="stylesheet" href="/assets/css/popover.css">
    <style>
        .paid {
            background: #22b783!important
        }
        .order-created {
            color: green;
        }
        .order-not-created {
            color: #cfcfcf;
        }
        .copied {
            position: absolute;
            margin: -15px 0 0 -13px;
            font-size: 12px;
            left: 0;
        }
    </style>
@endsection

@section('js')

    <script src="/assets/js/bootstrap.min.js"></script>
    <script>
        $('[data-toggle="popover"]').popover({
            html: true,
            trigger: "hover"
        });

        var Copy = {
            init: function(obj, text) {

                $("body").append("<div style='position:absolute; top:-1000px; '><input type='text' id='copyText' value='" + text + "'></div>");

                var copyText = document.getElementById("copyText");

                copyText.select();

                document.execCommand("copy");

                $("#copyText").parent().remove();

                obj.append('<span class="copied">Copied</span>');

                delay(function() {
                    $(".copied").remove();
                }, 1000);
            }
        }

    </script>
@endsection