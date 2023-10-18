@extends("admin.main")

@section('title', 'Интернет-Магазин')

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
                    <a href="{{ route('modification.create') }}?shop_item_id={{ $oShopItem->id }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Создать модификации</a>
                </div>
   
                    <div class="card-body p-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 1%">#ID</th>
                                    <th style="width: 40px" class="px-0 text-center"><i class="fas fa-check" title="Модификация по умолчанию"></i></th>
                                    <th style="width: 40px" class="px-0 text-center"><i class="fa fa-bars" title="—"></i></th>
                                    <th>Название</th>
                                    <th width="100px">Цена</th>
                                    <th width="40px" class="text-center"><i class="fas fa-money-bill-alt"></i></th>
                                    <th width="200px">Свойства</th>
                                    <th width="40px"><i class="fa fa-lightbulb-o" title="Активность"></i></th>
                                    <th width="40px">
                                        <i class="las la-tags font-20"  title="Скидки"></i>
                                    </th>
                                    <th width="60px"><i class="fas fa-sort-amount-down" title="—"></i></th>
                                    <th class="td-actions"></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($shopItems as $shopItem)

                                    @php
                                        $isActive = $shopItem->active == 1 ? false : true;
                                    @endphp

                                    <tr @class(['off' => $isActive, "default" => $shopItem->default_modification == 1])>
                                        <td>{{ $shopItem->id }}</td>
                                        <td class="text-center">
                                            <div class="form-check">
                                                <input title="default" class="form-check-input" type="radio" name="default_modification" data-parent="{{ $shopItem->modification_id }}" value="{{ $shopItem->id }}" @if($shopItem->default_modification == 1) checked @endif >
                                            </div>
                                        </td>
                                        <td class="px-0 text-center td_image">

                                            @php

                                                $ShopModificationImage = $shopItem->ShopModificationImage;
                                                $ShopItemImage = !is_null($ShopModificationImage) ? $ShopModificationImage->ShopItemImage : null;

                                                $image_small = !is_null($ShopItemImage) ? $oShopItem->path() . $ShopItemImage->image_small : '';

                                            @endphp

                                       
                                            @if (!empty($image_small))
                                                <img src="{{ $image_small }}" alt="" height="40">
                                            @else
                                                <i class="la la-image " title=""></i> 
                                            @endif
                                        </td>
                                        <td class="td_editable">
                                            <span id="apply_check_shopItem_name_{{ $shopItem->id }}" class="editable product-name fw-semibold line-through-if-off">{{ $shopItem->name }}</span>
                                        </td>
                                        <td width="100px" class="td_editable"><span id="apply_check_shopItem_price_{{ $shopItem->id }}" class="editable">{{ $shopItem->price }}</span></td>
                                        <td class=" text-center">{{ $shopItem->shop_currency_id > 0 ? $shopItem->ShopCurrency->name : ''}}</td>
                                        <td>
                                            @foreach ($shopItem->PropertyValueInts as $propertyValueInt)
                                                @if ($propertyValueInt->ShopItemListItem)
                                                    <div>{{$propertyValueInt->ShopItemProperty->name}}: {{ $propertyValueInt->ShopItemListItem->value}}</div>
                                                @endif
                                                
                                            @endforeach
                                        </td>
                                        <td>
                                            <span onclick="toggle.init($(this))" @class([
                                                'pointer',
                                                'ico-inactive' => $isActive,
                                            ]) id="toggle_shopItem_active_{{ $shopItem->id }}">
                                    
                                                <i class="lar la-lightbulb font-20"></i>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route("shopItemDiscount.index") }}?shop_item_id={{ $shopItem->id }}">
                                                <i class="las la-tags font-20 palegreen"></i>
                                                @php
                                                    $count = $shopItem->ShopItemDiscount->count();
                                                @endphp
                                                @if ($count > 0)
                                                    <span class="badge-count position-absolute badge-count-abs btn-success">{{ $count }}</span>
                                                @endif
                                            </a>
                                        </td>
                                        <td width="60px" class="td_editable"><span id="apply_check_shopItem_sorting_{{ $shopItem->id }}" class="editable">{{ $shopItem->sorting }}</span></td>
                                        <td class="td-actions">

                                            <a href="{{ route('modification.edit', $shopItem->id) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                            <form action="{{ route('modification.destroy', $shopItem->id) }}" method="POST" style="display:inline-block;">
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

                        {{ $shopItems->appends(['shop_item_id' => $oShopItem->id])->links() }}

                    </div>
            </div>
        </div>
    </div>

    
@endsection

@section("js")

    <script>

        $("[name='default_modification']").change(function() {

            let value = $(this).val(),
                shop_item_id = $(this).attr("data-parent"),
                $this = $(this);   
                
                $this.parents("tr").addClass("default");
                $this.parents("tr").siblings("tr").removeClass("default");

                $.ajax({
                    url: "/admin/default_modification",
                    data: {"shop_item_id": shop_item_id, "modification_id" : value},
                    type: "GET",
                    dataType: "json"
                });
        });

    </script>

@endsection

@section("css")
    <style>
        tr.default{background: #5d8cfb36}
    </style>
@endsection