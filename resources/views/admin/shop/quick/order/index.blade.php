@extends("admin.main")

@section('title', 'Быстрые Заказы интернет-магазина')

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
                    <a href="{{ route("shop.shop-quick-order.create", ['shop' => $shop->id]) }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить</a>
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

                    {{ $quick_orders->links() }}
                </div>
            </div>
        </div>
    </div>

    
@endsection

