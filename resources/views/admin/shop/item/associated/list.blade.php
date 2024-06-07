@if (isset($shopItem) && !is_null($shopItem))

    @if (!is_null($ShopItemAssociatedGroups = $shopItem->ShopItemAssociatedGroups) && count($ShopItemAssociatedGroups) > 0)


        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Сопутствующие группы</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            @foreach ($ShopItemAssociatedGroups as $ShopItemAssociatedGroup)

                                @if (!is_null($ShopGroup = $ShopItemAssociatedGroup->ShopGroup))

                                    @php
                                        $breadcrumbs = \App\Http\Controllers\ShopGroupController::breadcrumbs($ShopGroup, []);
                                        $route = route('deleteShopItemAssociatedGroup', ['shopItem' => $shopItem->id, 'shopItemAssociatedGroup' => $ShopItemAssociatedGroup->id]);
                                    @endphp
                                    <tr>
                                        <td width="300px">
                                            <p class="d-inline-block align-middle mb-0">
                                                <span class="d-inline-block align-middle mb-0 product-name fw-semibold">{{ $ShopItemAssociatedGroup->ShopGroup->name }}</span>
                                                <br>
                                                <span class="text-muted font-13 fw-semibold">кол-во товаров: {{ $ShopItemAssociatedGroup->ShopGroup->subitems_count }}</span>                                                  
                                            </p>
                                        </td>
                                        <td>
                                    
                                            <a target="_blank" href="{{ $ShopGroup->url }}">{{ $ShopGroup->url }}</a>
                                        </td>
                                        <td width="40px">                                                       
                                            <a href="javascript:void(0)" onclick="Associated.delete('{{ $route }}')"><i class="las la-trash-alt text-secondary font-16"></i></a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table> 
                </div>
            </div>
        </div>
    @endif

    @if (!is_null($ShopItemAssociatedItems = $shopItem->ShopItemAssociatedItems) && count($ShopItemAssociatedItems) > 0)
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Сопутствующие товары</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            @foreach ($ShopItemAssociatedItems as $ShopItemAssociatedItem)
                                @if (!is_null($aShopItem = $ShopItemAssociatedItem->ShopItem))

                                    @php
                                        $images = $aShopItem->getImages(false);
                                        $route = route('deleteShopItemAssociatedItem', ['shopItem' => $shopItem->id, 'shopItemAssociatedItem' => $ShopItemAssociatedItem->id]);
                                    @endphp

                                    <tr>
                                        <td width="300px">
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">
                                                    @if ($images)
                                                        
                                                        @foreach ($images as $k => $image)
                                                            @if (!empty($image['image_small']))
                                                                <img src="{{ $image['image_small'] }}" alt="" height="40">
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <i class="la la-image fs-1" title=""></i> 
                                                    @endif
                                                </span>

                                                <p class="d-inline-block align-middle mb-0">
                                                    <span class="d-inline-block align-middle mb-0 product-name fw-semibold lh-1">{{ $aShopItem->name }}</span>                                               
                                                </p>
                                            </div>
                                        </td>
                                        <td>
                                            <a target="_blank" href="{{ $aShopItem->url }}">{{ $aShopItem->url }}</a>
                                        </td>
                                        <td width="40px">                                                       
                                            <a href="javascript:void(0)" onclick="Associated.delete('{{ $route }}')"><i class="las la-trash-alt text-secondary font-16"></i></a>
                                        </td>
                                    </tr>
                                @endif 
                            @endforeach
                        </tbody>
                    </table> 
                </div>
            </div>
        </div>
    @endif

@endif