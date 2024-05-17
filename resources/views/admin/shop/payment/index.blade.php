@extends("admin.main")

@section('title', 'Платежные системы интернет-магазина')

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
                    <a href="{{ route("shop.shop-payment-system.create", ['shop' => $shop->id]) }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 1%">#ID</th>
                                <th>Название</th>
                                <th class="controll-td"></th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($PaymentSystems as $PaymentSystem)

                                <tr>
                                    <td style="width: 1%">{{ $PaymentSystem->id }}</td>
                                    <td>{{ $PaymentSystem->name }}</td>
                                    <td class="td-actions">
                                        <a href="{{ route('shop.shop-payment-system.edit', ['shop' => $shop->id, 'shop_payment_system' => $PaymentSystem->id]) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                        <form action="{{ route('shop.shop-payment-system.destroy', ['shop' => $shop->id, 'shop_payment_system' => $PaymentSystem->id]) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete($(this).parents('form'))"class="td-list-delete-btn">
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