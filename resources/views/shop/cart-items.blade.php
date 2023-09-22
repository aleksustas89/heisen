@php
    $cartCollection = \App\Http\Controllers\CartController::getCart();
    $totalDiscount = \App\Http\Controllers\CartController::getTotalDiscount();
    $count = $cartCollection ? $cartCollection->count() : 0;
    $CurrentCurrency = \App\Http\Controllers\ShopController::CurrentCurrency();
    $currency_code =  $CurrentCurrency ? $CurrentCurrency->code : '';
    $showLittleCart = isset($littleCart) && $littleCart == 1 ? true : false;
@endphp

@if ($showLittleCart)
    <a><span uk-icon="icon: bag"></span> ({{ $count }})</a>
@endif
@if ($count > 0)
    <div class="uk-card uk-card-default uk-card-small uk-card-body" @if ($showLittleCart) uk-drop="" @endif>
        <!--small card-->

        @foreach ($cartCollection as $row)

        <div class="uk-position-relative">

                <a onclick="Cart.delete({{ $row->id }}{{ $showLittleCart ? ', 1' : '' }})" class="uk-margin-small-right uk-icon uk-position-top-right" uk-icon="icon:close; ratio:0.7"></a>
                <div class="uk-grid-small uk-flex-middle" uk-grid>
                    <div class="uk-width-auto">
                        <img class="uk-tovar-avatar" src="{{ $row->attributes["img"] }}" width="80" height="80" alt="">
                    </div>
                    <div class="uk-width-expand">
                        <h4 class="uk-margin-remove tm-name-card-small"><a class="uk-link-reset" href="{{ $row->attributes["url"] }}">{{ $row->name }}</a></h4>
                        
                        <ul class="uk-subnav uk-subnav-divider uk-margin-remove-top">
                            <li>Кол-во: {{ $row->quantity }}</li>
                            <li>Цена: 
                                {{ App\Services\Helpers\Str::price($row->price) }} 
                                @if ($row->attributes["oldPrice"] && $row->attributes["oldPrice"] > $row->price)  
                                    <span class="cart-item-old-price item-old-price uk-margin-small-left uk-margin-small-right">{{ App\Services\Helpers\Str::price($row->attributes["oldPrice"]) }}</span>
                                @endif
                                
                                {{$currency_code}}
                            </li>
                        </ul>
                        @if (isset($row->attributes["priceChanged"]))
                            <div class="uk-alert-danger uk-alert" uk-alert="">
                                <p>С момента добавления в корзину, цена или скидка были изменены!</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
            <hr />

        @endforeach

        <div class="uk-grid-small little_cart_summ" uk-grid>
            @php
            $total = App\Services\Helpers\Str::price(\App\Http\Controllers\CartController::getTotal());
            @endphp
            <div class="uk-width-auto">Сумма заказа:</div><div class="uk-width-expand uk-text-right">{{ $total }} {{$currency_code}}</div>
        </div>

        @if ($totalDiscount > 0)
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-auto">Скидка:</div>
                <div class="uk-width-expand uk-text-right">-{{ App\Services\Helpers\Str::price($totalDiscount) }} {{$currency_code}}</div>
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