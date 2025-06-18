@extends("admin.main")

@section('title', 'Заказы интернет-магазина')

@section('breadcrumbs')

<div class="page-title-box d-flex flex-column">
    <div class="float-start">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)
                @if (!empty($breadcrumb["url"]))
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                @else 
                    <li class="breadcrumb-item">{{ $breadcrumb["name"] }}</li>
                @endif
            @endforeach
        </ol>
    </div>
</div>

@endsection

@section('content')

    @if (session('success'))
        <div class="alert alert-success border-0" role="alert">
            {{ session('success') }}
        </div>
    @endif
            
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route("shop-order.create") }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить</a>

                    <div>
                        <form>
                            <div class="mb-1 mt-1 position-relative">
                                <input type="text" value="{{ request()->global_search }}" name="global_search" class="form-control" placeholder="Поиск" >
                                @if (!empty(request()->global_search))<a class="clean-input" href="javascript:void(0)" onclick="$('[name=\'global_search\']').val(''); $(this).parents('form').submit()"><i class="las la-times-circle"></i></a>@endif
                            </div>
                        </form>
                    </div>

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

                                                    $personalization = "";
                                                    if (!empty($orderItem->logo)) {
                                                        if ($orderItem->logo == 1) {
                                                            $personalization .= "<div style='font-size:12px'>Без логотипа</div>";
                                                        }
                                                        if ($orderItem->logo == 2) {
                                                            $personalization .= "<div style='font-size:12px'>С логотипом мастера</div>";
                                                        }
                                                    }

                                                    if (!empty($orderItem->description)) {
                                                        $personalization .= "<div style='font-size:12px'>Персонализация: " . $orderItem->description . "</div>";
                                                    }

                                                    $popover .= "
                                                                    <tr>
                                                                        <td>" . $orderItem->id . "</td>
                                                                        <td>" . (str_replace('"', "", $orderItem->name)) . $personalization . "</td>
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

                    {{ $orders->links() }}
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