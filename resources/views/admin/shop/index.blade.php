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
                    <a href="{{ route('shopQuickOrder.index') }}" class="btn btn-burgundy"><i class="fas fa-shopping-cart icon-separator"></i>Быстрые заказы</a>


                    <button type="button" class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-tags icon-separator"></i>Цены<i class="mdi mdi-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" style="">
                        <a class="dropdown-item" href="{{ route('shopDiscount.index') }}">Скидки</a>
                        <a class="dropdown-item" href="{{ route('shop.shop-price.index', ['shop' => 1]) }}">Изменение цен</a>
                    </div>


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
                        <a class="dropdown-item" href="{{ route('cdekSender.edit', 1) }}">Cdek</a>
                    </div>

                    <div>
                        <form>
                            <div class="mb-1 mt-1 position-relative">
                                <input type="text" value="{{ $global_search }}" name="global_search" class="form-control" placeholder="Поиск" >
                                <input type="hidden" name="parent_id" value="{{ $parent }}" />
                                @if (!empty($global_search))<a class="clean-input" href="javascript:void(0)" onclick="$('[name=\'global_search\']').val(''); $(this).parents('form').submit()"><i class="las la-times-circle"></i></a>@endif
                            </div>
                        </form>
                    </div>
                </div>
   
                    <div class="card-body p-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 1%">#ID</th>
                                    <th style="width: 40px"  class="px-0 text-center"><i class="fa fa-bars" title="—"></i></th>
                                    <th>Название</th>
                                    <th  class="d-mob-none" width="200px">Цена</th>
    
                                    <th class="d-mob-none" width="40px"><i class="fa fa-lightbulb-o" title="Активность"></i></th>
                                    <th class="d-mob-none" width="40px">
                                        <i class="lab la-buromobelexperte font-20" title="Модификации"></i>
                                    </th>
                                    <th class="d-mob-none" width="60px"><i class="fas fa-sort-amount-down" title="—"></i></th>
                                    <th class="controll-td"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($shopGroups))
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
                                            <td class="d-mob-none" width="200px">&nbsp;</td>
                                        
                                            <td class="d-mob-none" width="40px">
                                                @if ($shopGroup->active == 1)
                                                    <i class="fa fa-lightbulb-o" title="Активность"></i>
                                                @else
                                                    <i class="fa fa-lightbulb-o fa-inactive" title="Активность"></i>
                                                @endif
                                            </td>
                                            <td class="d-mob-none" width="40px">&nbsp;</td>
                                            <td width="60px" class="td_editable d-mob-none"><span id="apply_check_shopGroup_sorting_{{ $shopGroup->id }}" class="editable">{{ $shopGroup->sorting }}</span></td>

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
                                @endif
        
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
                                                    ->where("image_small", "!=", "")
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

                                            <a href="{{ $shopItem->url }}" target="_blank">
                                                <i class="las la-external-link-alt"></i>
                                            </a> 
                                            <br>

                                            <span class="text-muted font-13 fw-semibold">{{ $shopItem->marking }} </span> 
                                            
                                        </td>
                                        <td width="200px" class="td_editable d-mob-none">
                                            <div class="d-flex">

                                                @if ($shopItem::$priceView == 0)

                                                    @php
                                                        $prices = App\Http\Controllers\ShopDiscountController::getModificationsPricesWithDiscounts($shopItem);
                                                    @endphp
                                
                                                    @if (count($prices) > 1)
                                                        {{ App\Services\Helpers\Str::price(min($prices)) }} - {{ App\Services\Helpers\Str::price(max($prices)) }}
                                                        <span class="item-old-price" id="item-old-price">
                                                            @if (!in_array($shopItem->price, $prices))
                                                                {{ App\Services\Helpers\Str::price($shopItem->price) }}
                                                            @endif
                                                        </span>
                                                    @else 
                                                        {{ App\Services\Helpers\Str::price($shopItem->price()) }} 
                                                        <span>{{ !empty($shopItem->oldPrice()) ? App\Services\Helpers\Str::price($shopItem->oldPrice()) : '' }}</span>
                                                    @endif
                                                @elseif($shopItem::$priceView == 1)
                                                    @php
                                                    $defaultModification = $shopItem->defaultModification();
                                                    $Object = $defaultModification ? $defaultModification : $shopItem;
                                                    $oldPrice = $Object->oldPrice();
                                    
                                                    @endphp
                                                    <span class=" mx-1" id="item-price">{{ App\Services\Helpers\Str::price($Object->price()) }}</span> 
                                                    @if ($oldPrice)<span class="item-old-price mx-1" id="item-old-price">{{ $oldPrice }}</span>@endif

                                                    <span class="mx-1">{{ $shopItem->shop_currency_id > 0 && $shopItem->ShopCurrency ? $shopItem->ShopCurrency->name : ''}}</span>
                                                    
                                                    @if ($oldPrice)
                                                        @php
                                                            $discounts = \App\Http\Controllers\ShopDiscountController::getDiscountsForItemAndModifications($Object);
                                                            $aDiscountTitles = [];
                                                            foreach ($discounts as $discount) {
                                                                $aDiscountTitles[] = $discount->name;
                                                            }
                                                        @endphp
                                                        <i class="las la-tags font-20 palegreen mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="{{ implode(',', $aDiscountTitles) }}"></i>
                                                    @endif

                                                @endif


                                            </div>
                                        </td>
                
                                        <td class="d-mob-none">

                                            <span onclick="toggle.init($(this))" @class([
                                                'pointer',
                                                'ico-inactive' => $isActive,
                                            ]) id="toggle_shopItem_active_{{ $shopItem->id }}">
                                    
                                                <i class="lar la-lightbulb font-20"></i>
                                            </span>

                                        </td>
                                        <td class="d-mob-none" width="40px">
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
                                        <td class="d-mob-none" width="60px" class="td_editable"><span id="apply_check_shopItem_sorting_{{ $shopItem->id }}" class="editable">{{ $shopItem->sorting }}</span></td>

                                        <td class="td-actions-large">
                                            <a href="{{ route('shopItem.edit', $shopItem->id) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                            <a onclick="confirmCopy($(this)); return false;" href="{{ route('copyShopItem', $shopItem->id) }}" class="mr-2"><i class="las la-copy text-secondary font-16"></i></a>
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

@section("css")
    <style>

    </style>
@endsection


