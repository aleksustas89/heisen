@extends("admin.main")

@section('title', 'Валюты')

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
                    <a href="{{ route('shop.shop-currency.create', ['shop' => $shop->id]) }}" class="btn btn-primary">Добавить</a>
                </div>

                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 1%">#ID</th>
                                <th class="d-mob-none" style="width: 40px" class="px-0 text-center">Код</th>
                                <th><span class="d-mob-none">Название</span></th>
                                <th>Курс</th>
                                <th width="40px" class="px-0 text-center"><span class="d-mob-none">Базовая</span></th>
                                <th class="controll-td"></th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($currencies as $currency)

                            <tr>
                                <td>
                                    {{ $currency->id }}
                                </td>

                                <td class="px-0 text-center d-mob-none">
                                    {{ $currency->code }}
                                </td>

                                <td>
                                    
                                    {{ $currency->name }}
                                </td>
                    
                                <td>
                                    {{ $currency->exchange_rate }}
                                </td>
                                <td class="px-0 text-center">
                                    @if ($currency->default == 1)
                                        <i data-feather="check" title="Базовая"></i>
                                    @else
                                        <i class="fa fa-lightbulb-o fa-inactive" title="Базовая"></i>
                                    @endif
                                </td>

                                <td class="td-actions">
                                    <a href="{{ route('shop.shop-currency.edit', ['shop' => $shop->id, 'shop_currency' => $currency->id]) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                    <form action="{{ route('shop.shop-currency.destroy', ['shop' => $shop->id, 'shop_currency' => $currency->id]) }}" method="POST" style="display:inline-block;">
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

    
@endsection