@extends("admin.main")

@section('title', 'Доставки интернет-магазина')

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
                    <a href="{{ route("shop.shop-delivery-field.create", ['shop' => $shop->id]) }}?shop_delivery_id={{ $shop_delivery_id }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 1%">#ID</th>
                                <th>Название</th>
                                <th class="d-mob-none">Название в input</th>
                                <th style="width: 200px">Тип</th>
                                <th class="d-mob-none" style="width: 100px">Сортировка</th>
                                <th class="controll-td"></th>
                            </tr>
                        </thead>
                        <tbody>
                            

                            @foreach ($fields as $field)

                                <tr>
                                    <td style="widtd: 1%">{{ $field->id }}</td>
                                    <td><span id="apply_check_deliveryField_caption_{{ $field->id }}" class="editable">{{ $field->caption }}</span></td>
                                    <td class="d-mob-none"><span id="apply_check_deliveryField_field_{{ $field->id }}" class="editable">{{ $field->field }}</span></td>
                                    <td style="width: 200px">{{ $types[$field->type] ?? $field->type }}</td>
                                    <td class="d-mob-none" style="width: 100px"><span id="apply_check_deliveryField_sorting_{{ $field->id }}" class="editable">{{ $field->sorting }}</span></td>
                                    <td class="td-actions">
                                        <a href="{{ route('shop.shop-delivery-field.edit', ['shop' => $shop->id, 'shop_delivery_field' => $field->id]) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                        <form action="{{ route('shop.shop-delivery-field.destroy', ['shop' => $shop->id, 'shop_delivery_field' => $field->id]) }}" method="POST" class="d-inline">
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
