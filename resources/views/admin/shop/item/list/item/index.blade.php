@extends("admin.main")

@section('title', 'Элементы списка')

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
                    <a href="{{ route('shop.shop-item-list-item.create', ['shop' => $shop->id]) }}?list_id={{ $list_id }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить</a>
                </div>

                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 1%">#ID</th>
                                <th>Значение</th>
                                <th width="40px"></th>
                                <th class="d-mob-none" width="60px"><i class="fas fa-sort-amount-down" title="—"></i></th>
                                <th class="controll-td"></th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($items as $item)

                                @php
                                    $isActive = $item->active == 1 ? false : true;
                                @endphp

                                <tr>
                                    <td>
                                        {{ $item->id }}
                                    </td>

                                    <td>
                                        {{ $item->value }}
                                    </td>

                                    <td width="40px">

                                        <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="активен/не активен" onclick="toggle.init($(this))" onclick="toggle.init($(this))" @class([
                                            'pointer',
                                            'ico-inactive' => $isActive,
                                        ]) id="toggle_ShopItemListItem_active_{{ $item->id }}">
                                
                                            <i class="lar la-lightbulb font-20"></i>
                                        </span>
                                    </td>

                                    <td class="d-mob-none" width="60px" class="td_editable"><span id="apply_check_ShopItemListItem_sorting_{{ $item->id }}" class="editable">{{ $item->sorting }}</span></td>

                                    <td class="td-actions">
                                        <a href="{{ route('shop.shop-item-list-item.edit', ['shop' => $shop->id, 'shop_item_list_item' => $item->id]) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                        <form action="{{ route('shop.shop-item-list-item.destroy', ['shop' => $shop->id, 'shop_item_list_item' => $item->id]) }}" method="POST" style="display:inline-block;">
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