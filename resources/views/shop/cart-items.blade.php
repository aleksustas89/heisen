@php
$Cart =  App\Http\Controllers\CartController::getCart();

@endphp

@if ($Cart)
    @php
        $totalDiscount = $Cart["totalDiscount"];
        $totalPrice = $Cart["totalPrice"];
        $countItems = $Cart["countItems"];
        $showLittleCart = isset($littleCart) && $littleCart == 1 ? true : false;
    @endphp

    @if ($showLittleCart)
        <a><span uk-icon="icon: bag"></span> ({{ $countItems }})</a>
    @endif
    @if ($countItems > 0)
        <div class="uk-card uk-card-default uk-card-small uk-card-body" @if ($showLittleCart) uk-drop="" @endif>
            <!--small card-->

            @foreach ($Cart["items"] as $CartItem)

            @php
                $ShopItem = $CartItem->ShopItem->parentItemIfModification();
            @endphp

            <div class="uk-position-relative">

                    <a onclick="Cart.delete({{ $CartItem->id }}{{ $showLittleCart ? ', 1' : '' }})" class="uk-margin-small-right uk-icon uk-position-top-right" uk-icon="icon:close; ratio:0.7"></a>
                    <div class="uk-grid-small uk-flex-middle" uk-grid>
                        <div class="uk-width-auto">
                            @foreach ($ShopItem->getImages(false) as $image)
                                <a href="{{ $ShopItem->url }}">
                                    <img class="uk-tovar-avatar" data-src="{{ $image['image_small'] }}" uk-img="loading: lazy" width="80" height="80" alt="">
                                </a>
                            @endforeach 
                        </div>
                        <div class="uk-width-expand">
                            <h4 class="uk-margin-remove tm-name-card-small">
                                <a class="uk-link-reset" href="{{ $ShopItem->url }}">{{ implode(", ", $CartItem->ShopItem->modificationName()) }}</a>
                            </h4>
                            
                            <ul class="uk-subnav uk-subnav-divider uk-margin-remove-top">
                                <li>
                                    Кол-во: {{ $CartItem->count }}
                                    <div class="uk-flex uk-flex-column uk-margin-small-left">
                                        <a href="javascript:void(0)" onclick="Cart.plus('{{ route('updateItemInCart') }}', {{ $CartItem->id }}, {{ $showLittleCart }})">
                                            <span uk-icon="chevron-up"></span>
                                        </a>
                                        <a href="javascript:void(0)" @class(["disabled-delete" => $CartItem->count == 1]) @if($CartItem->count > 1) onclick="Cart.minus('{{ route('updateItemInCart') }}', {{ $CartItem->id }}, {{ $showLittleCart }})"  @endif>
                                            <span uk-icon="chevron-down"></span>
                                        </a>
                                    </div>
                                </li>
                                <li>Цена: 
                                    {{ App\Services\Helpers\Str::price($CartItem->ShopItem->getPriceApplyCurrency($Currency)) }} 
                                    {{-- @if ($row->attributes["oldPrice"] && $row->attributes["oldPrice"] > $row->price)  
                                        <span class="cart-item-old-price item-old-price uk-margin-small-left uk-margin-small-right">{{ App\Services\Helpers\Str::price($row->attributes["oldPrice"]) }}</span>
                                    @endif --}}
                                    
                                    {{$Currency->name}}
                                </li>
                            </ul>
                            {{-- @if (isset($row->attributes["priceChanged"]))
                                <div class="uk-alert-danger uk-alert" uk-alert="">
                                    <p>С момента добавления в корзину, цена или скидка были изменены!</p>
                                </div>
                            @endif --}}

                        </div>
                    </div>
                </div>
                <hr />

            @endforeach

            <div class="uk-grid-small little_cart_summ" uk-grid>
                <div class="uk-width-auto">Сумма заказа:</div><div class="uk-width-expand uk-text-right">{{ App\Services\Helpers\Str::price($totalPrice) }} {{$Currency->name}}</div>
            </div>

            @if ($totalDiscount > 0)
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-auto">Скидка:</div>
                    <div class="uk-width-expand uk-text-right">-{{ App\Services\Helpers\Str::price($totalDiscount) }} {{$Currency->name}}</div>
                </div>
            @endif

            <hr />
            @if ($showLittleCart)
                <div class="uk-text-center">
                    <a href="{{ route("cartIndex") }}" class="uk-button uk-button-primary">Оформить заказ</a>
                </div>
            @endif
            <!--Small card-->
        </div>  
    @endif

@else
    <a><span uk-icon="icon: bag"></span> (0)</a>
@endif