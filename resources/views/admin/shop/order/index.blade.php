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
                    <a href="{{ route("shopOrder.create") }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 1%">№</th>
                                <th style="width: 170px">Дата</th>
                                <th style="width: 40px">Источник</th>
                                <th>Фио</th>
                                <th style="width: 50px"><i data-feather="list"></i></th>
                                <th style="width: 100px">Сумма</th>
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

                                <tr>
                                    <td>
                                        {{ $order->id }}
                                    </td>
                                    <td style="width: 170px" >
                                        {{ date("d.m.Y H:i", strtotime($order->created_at)) }}
                                    </td>
                                    <td style="width: 40px">
                                        {!! $source !!}
                                    </td>
                                    <td>
                                        {{ $fio }}
                                    </td>
                                    <td>

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
                                                foreach ($order->ShopOrderItems as $orderItem) {
                                                    $popover .= "
                                                                    <tr>
                                                                        <td>" . $orderItem->id . "</td>
                                                                        <td>" . $orderItem->name . "</td>
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
                                    <td style="width: 100px">
                                        {{ App\Models\Str::price($sOrderSum) }} {{ $sOrderCurrency }}
                                    </td>
                                    <td class="td-actions">
                                        <a href="{{ route('shopOrder.edit', $order->id) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                        <form action="{{ route('shopOrder.destroy', $order->id) }}" method="POST" class="d-inline">
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
@endsection

@section('js')

    <script src="/assets/js/bootstrap.min.js"></script>
    <script>
        $('[data-toggle="popover"]').popover({
            html: true,
            trigger: "hover"
        });
    </script>
@endsection