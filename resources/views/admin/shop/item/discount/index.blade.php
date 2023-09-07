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
                <div class="card-header">
                    <a href="{{ route("shopItemDiscount.create") }}?shop_item_id={{ $shop_item_id }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 1%">№</th>
                                <th>Название</th>
                                <th style="width: 170px">Действует от</th>
                                <th style="width: 170px">Действует до</th>
                                <th style="width: 100px">Величина</th>
                                <th class="td-actions"></th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($itemDiscounts as $itemDiscount)

                                @php
                                    $discount = $itemDiscount->ShopDiscount;
                                    $isActive = $discount->active == 1 ? false : true;
                                @endphp

                                <tr @class([
                                    'off' => $isActive,
                                ])>
                                    <td>{{ $discount->id }}</td>
                                    <td>{{ $discount->name }}</td>
                                    <td>{{ \App\Services\Helpers\Str::datetime($discount->start_datetime) }}</td>
                                    <td>{{ \App\Services\Helpers\Str::datetime($discount->end_datetime) }}</td>
           
                                    <td class="td_editable">{{ $discount->value }} {{ isset($types[$discount->type]) ? $types[$discount->type] : '' }}</td>
                                    <td class="td-actions">
                                        <a href="{{ route('shopItemDiscount.edit', $itemDiscount->id) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                        <form action="{{ route('shopItemDiscount.destroy', $itemDiscount->id) }}" method="POST" class="d-inline">
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

                    {{ $itemDiscounts->links() }}
                </div>
            </div>
        </div>
    </div>

    
@endsection

