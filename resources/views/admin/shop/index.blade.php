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

                <div class="card-header button-items">
                    <a href="{{ route('shopItem.create') }}{{ ($parent > 0 ? '?parent_id=' . $parent : '') }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить товар</a>
                    <a href="{{ route('shopGroup.create') }} {{ ($parent > 0 ? '?parent_id=' . $parent : '') }}" class="btn btn-primary"><i class="fas fa-plus icon-separator"></i>Добавить группу</a> 
                    <a href="{{ route('shopCurrency.index') }}" class="btn btn-info"><i class="fas fa-dollar-sign icon-separator"></i>Валюты</a>
                    
                    <a href="{{ route('shopOrder.index') }}" class="btn btn-danger"><i class="fas fa-shopping-cart icon-separator"></i>Заказы</a>

                    <a href="{{ route('shopDiscount.index') }}" class="btn btn-warning"><i class="fas fa-tags icon-separator"></i>Скидки</a>

                    <button type="button" class="btn btn-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cogs icon-separator"></i>
                            Настройки
                            <i class="mdi mdi-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" style="">
                        <a class="dropdown-item" href="{{ route('shop.edit', 1) }}">Настройки магазина</a>
                        <a class="dropdown-item" href="{{ route('shopItemProperty.index') }}">Свойства товаров</a>
                        <a class="dropdown-item" href="{{ route('shopItemList.index') }}">Списки</a>
                        <a class="dropdown-item" href="{{ route('shopDelivery.index') }}">Доставка</a>
                    </div>

                </div>
   
                    <div class="card-body p-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 1%">#ID</th>
                                    <th style="width: 40px"  class="px-0 text-center"><i class="fa fa-bars" title="—"></i></th>
                                    <th>Название</th>
                                    <th width="100px">Цена</th>
                                    <th width="40px" class="text-center"><i class="fas fa-money-bill-alt"></i></th>
                                    <th width="40px"><i class="fa fa-lightbulb-o" title="Активность"></i></th>
                                    <th width="40px">
                                        <i class="las la-tags font-20"  title="Скидки"></i>
                                    </th>
                                    <th width="40px">
                                        <i class="lab la-buromobelexperte font-20" title="Модификации"></i>
                                    </th>
                                    <th width="60px"><i class="fas fa-sort-amount-down" title="—"></i></th>
                                    <th class="controll-td"></th>
                                </tr>
                            </thead>
                            <tbody>
        
                                @foreach ($shopGroups as $shopGroup)
                                    <tr>
                                        <td>
                                            {{ $shopGroup->id }}
                                        </td>
                                        <td class="px-0 text-center"><i data-feather="folder"></i></td>
                                        <td>
                                        
                                                <a href="?parent_id={{ $shopGroup->id }}">{{ $shopGroup->name }}</a>
                                                @php
                                                    $subCount = $shopGroup->getChildCount();
                                                @endphp
                                                @if ($subCount["groupsCount"] > 0)
                                                    <span class="badge-count btn-primary">{{ $subCount["groupsCount"] }}</span>
                                                @endif
                                                @if ($subCount["itemsCount"] > 0)
                                                    <span class="badge-count btn-success">{{ $subCount["itemsCount"] }}</span>
                                                @endif

                                                @php
                                                $url = '/' . $shop_path . '/' . $shopGroup->path();
                                                @endphp

                                                <a href="{{ $url }}" target="_blank">
                                                    <i class="las la-external-link-alt"></i>
                                                </a> 
                                            
                                        </td>
                                        <td width="100px">&nbsp;</td>
                                        <td width="40px">&nbsp;</td>
                                        <td width="40px">
                                            @if ($shopGroup->active == 1)
                                                <i class="fa fa-lightbulb-o" title="Активность"></i>
                                            @else
                                                <i class="fa fa-lightbulb-o fa-inactive" title="Активность"></i>
                                            @endif
                                        </td>
                                        <td width="40px">&nbsp;</td>
                                        <td width="40px">&nbsp;</td>
                                        <td width="60px" class="td_editable"><span id="apply_check_shopGroup_sorting_{{ $shopGroup->id }}" class="editable">{{ $shopGroup->sorting }}</span></td>

                                        <td class="td-actions">
                                            <a href="{{ route('shopGroup.edit', $shopGroup->id) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                            <form action="{{ route('shopGroup.destroy', $shopGroup->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete($(this).parents('form'))" class="td-list-delete-btn">
                                                    <i class="las la-trash-alt text-secondary font-16"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
        
                                @foreach ($shopItems as $shopItem)

                                    @php
                                        $isActive = $shopItem->active == 1 ? false : true;
                                    @endphp

                                    <tr @class([
                                        'off' => $isActive,
                                    ])>
                                        <td>
                                            {{ $shopItem->id }}
                                        </td>

                                        <td class="px-0 text-center td_image">
                                            @php
                                                $oShopItemImage = \App\Models\ShopItemImage::where("shop_item_id", "=", $shopItem->id)
                                                    ->orderBy('sorting', 'Asc')
                                                    ->orderBy('id', 'Asc')
                                                    ->first();
                                                $image_small= !is_null($oShopItemImage) ? $shopItem->path() . $oShopItemImage->image_small : '';
                                            @endphp

                                            @if (!empty($image_small))
                                                <img src="{{ $image_small }}" alt="" height="40">
                                            @else
                                                <i class="la la-image " title=""></i> 
                                            @endif
                                        </td>

                                        <td class="td_editable">
                                            
                                            <span id="apply_check_shopItem_name_{{ $shopItem->id }}" class="editable product-name fw-semibold line-through-if-off">{{ $shopItem->name }}</span>

                                            <a href="/{{ $shopItem->url() }}" target="_blank">
                                                <i class="las la-external-link-alt"></i>
                                            </a> 
                                            <br>

                                            <span class="text-muted font-13 fw-semibold">{{ $shopItem->marking }} </span> 
                                            
                                        </td>
                                        <td width="100px" class="td_editable"><span id="apply_check_shopItem_price_{{ $shopItem->id }}" class="editable">{{ $shopItem->price }}</span></td>
                                        <td width="40px" class="text-center">{{ $shopItem->shop_currency_id > 0 && $shopItem->ShopCurrency ? $shopItem->ShopCurrency->name : ''}}</td>
                                        <td>

                                            <span onclick="toggle.init($(this))" @class([
                                                'pointer',
                                                'ico-inactive' => $isActive,
                                            ]) id="toggle_shopItem_active_{{ $shopItem->id }}">
                                    
                                                <i class="lar la-lightbulb font-20"></i>
                                            </span>

                                        </td>
                                        <td width="40px">
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
                                        <td width="40px">
                                            <a href="{{route('modification.index')}}?shop_item_id={{ $shopItem->id }}">
                                                @php
                                                    $count = $shopItem->getModificationCount();
                                                @endphp
                                                
                                                <i class="lab la-buromobelexperte font-20"></i>
                                                @if($count > 0)
                                                    <span class="badge-count position-absolute badge-count-abs btn-danger">{{ $count }}</span>
                                                @endif
                                            </a>
                                        </td>
                                        <td width="60px" class="td_editable"><span id="apply_check_shopItem_sorting_{{ $shopItem->id }}" class="editable">{{ $shopItem->sorting }}</span></td>

                                        <td class="td-actions">
                                            <a href="{{ route('shopItem.edit', $shopItem->id) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                            <form action="{{ route('shopItem.destroy', $shopItem->id) }}" method="POST" style="display:inline-block;">
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

                        {{ $shopItems->appends(['parent_id' => $parent])->links() }}
                    </div>
            </div>
        </div>
    </div>

    
@endsection