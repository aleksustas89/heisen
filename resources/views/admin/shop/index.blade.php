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
                        <a href="{{ route('shop.shop-item.create', ['shop' => $shop->id]) }}{{ ($parent > 0 ? '?parent_id=' . $parent : '') }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить товар</a>
                        <a href="{{ route('shop.shop-group.create', ['shop' => $shop->id]) }}{{ ($parent > 0 ? '?parent_id=' . $parent : '') }}" class="btn btn-primary"><i class="fas fa-plus icon-separator"></i>Добавить группу</a> 
                        <a href="{{ route('shop.shop-currency.index', ['shop' => $shop->id]) }}" class="btn btn-info"><i class="fas fa-dollar-sign icon-separator"></i>Валюты</a>
                        
                        <a href="{{ route('shop-order.index') }}" class="btn btn-danger"><i class="fas fa-shopping-cart icon-separator"></i>Заказы</a>
                        <a href="{{ route('shop.shop-quick-order.index', ['shop' => $shop->id]) }}" class="btn btn-burgundy"><i class="fas fa-shopping-cart icon-separator"></i>Быстрые заказы</a>

                        <button type="button" class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-tags icon-separator"></i>Цены<i class="mdi mdi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item" href="{{ route('shop.shop-discount.index', ['shop' => $shop->id]) }}">Скидки</a>
                            <a class="dropdown-item" href="{{ route('shop.shop-price.index', ['shop' => $shop->id]) }}">Изменение цен</a>
                        </div>

                        <button type="button" class="btn btn-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cogs icon-separator"></i>
                                Настройки
                                <i class="mdi mdi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item" href="{{ route('shop.edit', $shop->id) }}">Настройки магазина</a>
                            <a class="dropdown-item" href="{{ route('shop.shop-item-property.index', ['shop' => $shop->id]) }}">Свойства товаров</a>
                            <a class="dropdown-item" href="{{ route('shop.shop-item-list.index', ['shop' => $shop->id]) }}">Списки</a>
                            <a class="dropdown-item" href="{{ route('shop.shop-delivery.index', ['shop' => $shop->id]) }}">Доставка</a>
                            <a class="dropdown-item" href="{{ route('shop.shop-payment-system.index', ['shop' => $shop->id]) }}">Платежные системы</a>
                            <a class="dropdown-item" href="{{ route('cdek-sender.edit', 1) }}">Cdek отправитель</a>
                            <a class="dropdown-item" href="{{ route('boxberry-sender.edit', 1) }}">Boxberry отправитель</a>
                            <a class="dropdown-item" href="{{ route('shop.shop-filter.index', ['shop' => $shop->id]) }}">Статические фильтры</a>
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
                        <form class="admin-table">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 1%">
                                            <label>
                                                <input class="custom-control-input check-all" type="checkbox">
                                            </label>
                                        </th>
                                        <th style="width: 1%">#ID</th>
                                        <th style="width: 40px"  class="px-0 text-center"><i class="fa fa-bars" title="—"></i></th>
                                        <th>Название</th>
                                        <th  class="d-mob-none" width="140px">Цена</th>
        
                                        <th class="d-mob-none" width="40px"></th>
                                        <th class="d-mob-none" width="40px"></th>
                                        <th class="d-mob-none" width="60px"><i class="fas fa-sort-amount-down" title="—"></i></th>
                                        <th class="controll-td"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($shopGroups))
                                        @foreach ($shopGroups as $shopGroup)

                                            @php
                                                $isActive = $shopGroup->active == 1 ? false : true;
                                                $isHidden = $shopGroup->hidden == 1 ? true : false;
                                            @endphp

                                            <tr>
                                                <td>
                                                    <label>
                                                        <input name="shop_groups[{{ $shopGroup->id }}]" class="custom-control-input check-item" type="checkbox">
                                                    </label>
                                                </td>
                                                <td>
                                                    {{ $shopGroup->id }}
                                                </td>
                                                <td class="px-0 text-center"><i data-feather="folder"></i></td>
                                                <td>
                                                
                                                        <a href="?parent_id={{ $shopGroup->id }}">{{ $shopGroup->name }}</a>
                                                        
                                                        @if ($shopGroup->subgroups_count > 0)
                                                            <span class="badge-count btn-primary">{{ $shopGroup->subgroups_count }}</span>
                                                        @endif
                                                        @if ($shopGroup->subitems_count > 0)
                                                            <span class="badge-count btn-success">{{ $shopGroup->subitems_count }}</span>
                                                        @endif

                                                        <a href="{{ $shopGroup->url }}" target="_blank">
                                                            <i class="las la-external-link-alt"></i>
                                                        </a> 
                                                    
                                                </td>
                                                <td class="d-mob-none" width="100px">&nbsp;</td>
                                            
                                                <td class="d-mob-none">

                                                    <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="активен/не активен " onclick="toggle.init($(this))" onclick="toggle.init($(this))" @class([
                                                        'pointer',
                                                        'ico-inactive' => $isActive,
                                                    ]) id="toggle_ShopGroup_active_{{ $shopGroup->id }}">
                                            
                                                        <i class="lar la-lightbulb font-20"></i>
                                                    </span>
    
                                                </td>
                                                <td class="d-mob-none" width="40px">
    
                                                    <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="скрыт/показ." onclick="toggle.init($(this))" @class([
                                                        'pointer',
                                                        'ico-inactive' => $isHidden,
                                                    ]) id="toggle_ShopGroup_hidden_{{ $shopGroup->id }}">
                                            
                                                        <i class="lar la-lightbulb font-20"></i>
                                                    </span>
                                                </td>
                                                <td width="60px" class="td_editable d-mob-none"><span id="apply_check_shopGroup_sorting_{{ $shopGroup->id }}" class="editable">{{ $shopGroup->sorting }}</span></td>

                                                <td class="td-actions">
                                                    <a href="{{ route('shop.shop-group.edit', ['shop' => $shop->id, 'shop_group' => $shopGroup->id]) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                                    <a href="javascript:void(0)" class="mr-2 deleting" onclick="Operation.set('delete')"><i class="las la-trash-alt text-secondary font-16"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
            
                                    @foreach ($shopItems as $shopItem)

                                        @php
                                            $isActive = $shopItem->active == 1 ? false : true;
                                            $isHidden = $shopItem->hidden == 1 ? true : false;
                                            $linkWrong = strripos($shopItem->url, 'copy');
                                        @endphp

                                        <tr @class([
                                            'off' => $isActive,
                                            'list-group-item-danger' => $linkWrong ? true : false
                                        ])>
                                            <td>
                                                <label>
                                                    <input name="shop_items[{{ $shopItem->id }}]" class="custom-control-input check-item" type="checkbox">
                                                </label>
                                            </td>
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

                                                <div>
                                                    @include("admin.shop.shortcuts", ["shopItem" => $shopItem])
                                                </div>

                                                @if ($linkWrong)
                                                    <div class="font-13 fw-semibold" style="color: #8f2e34;">WRONG URL</div> 
                                                @endif
                                                
                                            </td>
                                            <td width="100px" class="td_editable d-mob-none">
                                                <div class="d-flex">

                                                    @php
                                                    $defaultModification = $shopItem->defaultModification();
                                                    $Object = $defaultModification ? $defaultModification : $shopItem;
                                                    $oldPrice = $Object->oldPrice();
                                                    $price = (int)$shopItem->price;
                                    
                                                    @endphp
                                                    <span 
                                                        data-value="{{ $price }}" 
                                                        id="apply_check_ShopItem_price_{{ $shopItem->id }}"
                                                        class="editable mx-1" id="item-price">{{ App\Services\Helpers\Str::price($price) }}
                                                    </span> 

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

                                                </div>
                                            </td>
                                            <td class="d-mob-none">

                                                
                                                <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="активен/не активен " onclick="toggle.init($(this))" onclick="toggle.init($(this))" @class([
                                                    'pointer',
                                                    'ico-inactive' => $isActive,
                                                ]) id="toggle_ShopItem_active_{{ $shopItem->id }}">
                                        
                                                    <i class="lar la-lightbulb font-20"></i>
                                                </span>

                                            </td>
                                            <td class="d-mob-none" width="40px">

                                                <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="скрыт/показ." onclick="toggle.init($(this))" @class([
                                                    'pointer',
                                                    'ico-inactive' => $isHidden,
                                                ]) id="toggle_ShopItem_hidden_{{ $shopItem->id }}">
                                        
                                                    <i class="lar la-lightbulb font-20"></i>
                                                </span>
                                            </td>
                                            <td class="d-mob-none" width="60px" class="td_editable"><span id="apply_check_shopItem_sorting_{{ $shopItem->id }}" class="editable">{{ $shopItem->sorting }}</span></td>

                                            <td class="td-actions-large">
                                                <a href="{{ route('shop.shop-item.edit', ['shop' => $shop->id, 'shop_item' => $shopItem->id]) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                                <a onclick="confirmCopy($(this)); return false;" href="{{ route('copyShopItem', $shopItem->id) }}" class="mr-2"><i class="las la-copy text-secondary font-16"></i></a>
                                                <a href="javascript:void(0)" class="mr-2 deleting" onclick="Operation.set('delete')"><i class="las la-trash-alt text-secondary font-16"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            <div class="card-footer text-start">
                                <div class="card-footer-inner v-hidden">
                                    <input type="hidden" name="operation" />
                                    <button type="submit" class="btn btn-sm btn-danger group-deleting" onclick="Operation.set('delete')">
                                        <i class="las la-trash-alt font-16"></i>
                                        Удалить
                                    </button>

                                    <button type="button" class="btn btn-sm btn-warning" onclick="Shortcut.addFromGroup()">
                                        <i class="las la-copy font-16"></i>
                                        Добавить в группы
                                    </button>
                                </div>
                            </div>
                        </form>

                        {{ $shopItems->appends(['parent_id' => $parent])->links() }}
                    </div>
            </div>
        </div>
    </div>

    
@endsection

@section("css")

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endsection

@section("js")

    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

    <script>
        BadgeClasses = [@foreach($BadgeClasses as $k => $BadgeClasse)'{{$BadgeClasse}}'@if($k < count($BadgeClasses)-1),@endif @endforeach];

        var routeSaveShortcutFromGroup = '{{ route("saveShortcutFromGroup") }}',
            routeGetShortcutGroup = '{{ route("getShortcutGroup") }}',
            routeAddShortcutFromGroup = '{{ route("addShortcutFromGroup") }}';

    </script>

    @php
        App\Services\Helpers\File::js('/assets/js/shortcut.js');
    @endphp

    <script>

        $("body").on("submit", "#addShopItemsToGroups", function() {

            $.ajax({
                url: routeSaveShortcutFromGroup,
                data: $("#addShopItemsToGroups").serialize(),
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function (data) {

                    if (data == true) {
                        document.location.reload();
                    } else {
                        $(".modal-body").prepend('<div class="alert alert-danger border-0" role="alert">' + data + '</div>');
                    }
                }
            });

            return false;
        });


    </script>

@endsection
