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
                    <a href="{{ route("shop.shop-delivery.create", ['shop' => $shop->id]) }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 1%">#ID</th>
                                <th>Название доставки</th>
                                <th class="d-mob-none">Описание</th>
                                <th class="d-mob-none" style="width: 40px">Цвет</th>
                                <th class="d-mob-none" style="width: 100px">Сортировка</th>
                                <th class="text-center" width="100">Поля</th>
                                <th class="controll-td"></th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($deliveries as $delivery)

                                <tr>
                                    <td style="width: 1%">{{ $delivery->id }}</td>
                                    <td><span id="apply_check_delivery_name_{{ $delivery->id }}" class="editable">{{ $delivery->name }}</span></td>
                                    <td class="d-mob-none"><span id="apply_check_delivery_description_{{ $delivery->id }}" class="editable">{{ $delivery->description }}</span></td>
                                    <td class="d-mob-none" style="width: 40px"><span id="apply_check_delivery_color_{{ $delivery->id }}" class="editable">{{ $delivery->color }}</span></td>
                                    <td class="d-mob-none" style="width: 100px"><span id="apply_check_delivery_sorting_{{ $delivery->id }}" class="editable">{{ $delivery->sorting }}</span></td>
                                    <td class="text-center">
                                        <a href="{{ route('shop.shop-delivery-field.index', ['shop' => $shop->id]) }}?shop_delivery_id={{ $delivery->id }}"><i data-feather="list"></i></a>
                                    </td>
                                    <td class="td-actions">
                                        <a href="{{ route('shop.shop-delivery.edit', ['shop' => $shop->id, 'shop_delivery' => $delivery->id]) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                        <form action="{{ route('shop.shop-delivery.destroy', ['shop' => $shop->id, 'shop_delivery' => $delivery->id]) }}" method="POST" style="display:inline-block;">
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
