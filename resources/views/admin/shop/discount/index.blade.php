@extends("admin.main")

@section('title', 'Скидки интернет-магазина')

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
                <div class="card-header button-items">
                    <a href="{{ route("shop.shop-discount.create", ['shop' => $shop->id]) }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить</a>
                
                    <div>
                        <form>
                            <div class="mb-1 mt-1 position-relative">
                                <input type="text" value="{{ request()->global_search }}" name="global_search" class="form-control" placeholder="Поиск">
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
                                <th>Название</th>
                                <th class="d-none d-lg-inline-block" style="width: 250px">&nbsp;</th>
                                <th class="d-none d-lg-inline-block" style="width: 170px">Действует от</th>
                                <th class="d-none d-lg-inline-block" style="width: 170px">Действует до</th>
                                <th class="d-none d-lg-inline-block text-center" style="width: 40px"><i class="lar la-lightbulb font-20"></i></th>
                                <th style="width: 100px">Величина</th>
                                <th class="td-actions"></th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($discounts as $discount)

                                @php
                                    $isActive = $discount->active == 1 ? false : true;
                                    $outOfDate = $discount->check() ? false : true;
                                @endphp

                                <tr @class([
                                    'off' => $isActive,
                                    'outOfDate' => $outOfDate,
                                ])>
                                    <td>{{ $discount->id }}</td>
                                    <td class="td_editable">
                                        <span class="line-through-if-off" id="apply_check_shopDiscount_name_{{ $discount->id }}" class="editable">{{ $discount->name }}</span>
                                        @if($outOfDate)<i class="las la-hourglass-end"></i>@endif
                                    </td>
                                    <td class="d-none d-lg-inline-block" style="width: 250px">
                                        @php
                                        $ShopItemDiscounts = $discount->ShopItemDiscounts;
                                        $count = !is_null($ShopItemDiscounts) ? $ShopItemDiscounts->count() : 0;
                                        @endphp
                                        <button type="button" class="btn btn-primary btn-sm">
                                            Кол-во товаров со скидкой <span class="badge bg-light text-dark">{{ $count }}</span>
                                        </button>
                                    </td>
                                    <td class="d-none d-lg-inline-block" style="width: 170px">{{ \App\Services\Helpers\Str::datetime($discount->start_datetime) }}</td>
                                    <td class="d-none d-lg-inline-block" style="width: 170px">{{ \App\Services\Helpers\Str::datetime($discount->end_datetime) }}</td>
                                    <td class="text-center d-none d-lg-inline-block" style="width: 40px">
                                        <span onclick="toggle.init($(this))" @class([
                                            'pointer',
                                            'ico-inactive' => $isActive,
                                        ]) id="toggle_shopDiscount_active_{{ $discount->id }}">
                                
                                            <i class="lar la-lightbulb font-20"></i>
                                        </span>
                                    </td>
                                    <td class="td_editable" style="width: 100px"><span id="apply_check_shopDiscount_value_{{ $discount->id }}" class="editable">{{ $discount->value }}</span> {{ isset($types[$discount->type]) ? $types[$discount->type] : ''  }}</td>
                                    <td class="td-actions">
                                        <a href="{{ route('shop.shop-discount.edit', ['shop' => $shop->id, 'shop_discount' => $discount->id]) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                        <form action="{{ route('shop.shop-discount.destroy', ['shop' => $shop->id, 'shop_discount' => $discount->id]) }}" method="POST" class="d-inline">
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

                    {{ $discounts->links() }}
                </div>
            </div>
        </div>
    </div>

    
@endsection

@section("css")

<style>.outOfDate{opacity:0.4}</style>

@endsection