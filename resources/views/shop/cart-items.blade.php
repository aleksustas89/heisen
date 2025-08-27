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

            <div class="cart-items">
                @foreach ($Cart["items"] as $CartItem)

                    @php
                        $ShopItem = $CartItem->ShopItem->parentItemIfModification();
                        $ecommerceData = $ShopItem->getEcommerceData();
                    @endphp

                    

                    <div class="uk-position-relative" >

                        <a data-ecommerce='@json($ecommerceData)' id="cart_item_{{ $CartItem->id }}" onclick="Cart.delete({{ $CartItem->id }})" class="uk-margin-small-right uk-icon uk-position-top-right cart-close" uk-icon="icon:close; ratio:0.7"></a>

                        <div class="uk-grid-small uk-flex-" uk-grid>
                            <div class="uk-width-auto">
                                @foreach ($ShopItem->getImages(false) as $image)
                                    @if (isset($image['image_small']))
                                        <a href="{{ $ShopItem->url }}">
                                            <img class="uk-tovar-avatar" data-src="{{ $image['image_small'] }}" uk-img="loading: lazy" width="80" height="80" alt="">
                                        </a>
                                    @endif
                                @endforeach 
                            </div>
                            <div class="uk-width-expand">
                                <h4 class="uk-margin-remove tm-name-card-small">
                                    <a class="uk-link-reset" href="{{ $ShopItem->url }}">{{ implode(", ", $CartItem->ShopItem->modificationName()) }}</a>
                                </h4>
                                
                                <ul class="uk-subnav uk-subnav-divider uk-margin-remove-top">
                                    <li>
                              
                                        <div class="uk-flex uk-flex-middle">
                                            <span>Кол-во:</span>
                                            <span uk-icon="chevron-left" @class(["pointer" => $CartItem->count > 1, "disabled-delete" => $CartItem->count == 1]) @if($CartItem->count > 1) onclick="Cart.minus('{{ route('updateItemInCart') }}', {{ $CartItem->id }})"  @endif></span>
                                            {{ $CartItem->count }}
                                            <span uk-icon="chevron-right" class="pointer" onclick="Cart.plus('{{ route('updateItemInCart') }}', {{ $CartItem->id }})"></span>
                                        </div>
                                    </li>
                                    <li>Цена: 
                                        {{$Currency->name}}
                                        {{ App\Services\Helpers\Str::price($CartItem->ShopItem->getPriceApplyCurrency($Currency)) }} 
                                        {{-- @if ($row->attributes["oldPrice"] && $row->attributes["oldPrice"] > $row->price)  
                                            <span class="cart-item-old-price item-old-price uk-margin-small-left uk-margin-small-right">{{ App\Services\Helpers\Str::price($row->attributes["oldPrice"]) }}</span>
                                        @endif --}}
                                        
                                        
                                    </li>
                                </ul>

                                @if ($CartItem->logo > 0 || !empty($CartItem->description))
                                    <div class="uk-alert-danger uk-alert" uk-alert="">
                                        @if ($CartItem->logo == 1)
                                            <div>Без логотипа</div>
                                        @endif
                                        @if ($CartItem->logo == 2)
                                            <div>С логотипом мастера</div>
                                        @endif
                                        @if (!empty($CartItem->description))
                                            <div>Персонализация: {{ $CartItem->description }}</div>
                                        @endif
                                    </div>
                                @endif
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

                <div class="uk-grid-small little_cart_summ" uk-grid style="flex:0; white-space: nowrap;">
                    <div class="uk-width-auto">Сумма заказа:</div><div class="uk-width-expand uk-text-right">{{$Currency->name}} {{ App\Services\Helpers\Str::price($totalPrice) }} </div>
                </div>

                @if ($totalDiscount > 0)
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-auto">Скидка:</div>
                        <div class="uk-width-expand uk-text-right">{{$Currency->name}} {{ App\Services\Helpers\Str::price($totalDiscount) }} </div>
                    </div>
                @endif

            </div>

            <hr />

            <div><b>Доставка бесплатна от {{$Currency->name}} {{ App\Services\Helpers\Str::price(env('FREE_DELIVERY_FROM')) }}</b></div>

            @php
                $percentage = round(($totalPrice / env('FREE_DELIVERY_FROM')) * 100, 100);
                $percentage = $percentage <= 100 ? $percentage : 100;
            @endphp

            <div class="delivery_line_wrapper">
                <div class="delivery_line_fill" style="width: {{ $percentage }}%;"></div>
                <div class="delivery_line_thumb" style="left: {{ $percentage }}%;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 490 490">
                        <path d="m490 268.656-87.933-99.281h-81.483v-39.274h-97.209c-18.689-39.949-59.262-67.695-106.199-67.695C52.567 62.406 0 114.973 0 179.583c0 44.856 25.342 83.9 62.456 103.586v92.515h44.57c1.194 28.824 25.007 51.909 54.119 51.909s52.925-23.085 54.119-51.909h121.927c1.194 28.824 25.008 51.909 54.12 51.909 29.111 0 52.925-23.085 54.119-51.909H490V268.656zM20.417 179.583c0-53.355 43.405-96.761 96.76-96.761 53.354 0 96.76 43.406 96.76 96.761 0 53.354-43.406 96.76-96.76 96.76-53.356 0-96.76-43.406-96.76-96.76zm140.728 227.594c-18.617 0-33.76-15.143-33.76-33.76 0-18.617 15.143-33.76 33.76-33.76 18.617 0 33.76 15.143 33.76 33.76 0 18.617-15.143 33.76-33.76 33.76zm139.023-51.909h-87.984c-7.48-20.972-27.531-36.027-51.039-36.027-23.508 0-43.559 15.056-51.039 36.027H82.872v-63.636a116.784 116.784 0 0 0 34.304 5.128c64.61 0 117.176-52.567 117.176-117.176a117.02 117.02 0 0 0-3.65-29.065h69.465v204.749zm91.143 51.909c-18.618 0-33.761-15.143-33.761-33.76 0-18.617 15.143-33.76 33.761-33.76 18.617 0 33.76 15.143 33.76 33.76 0 18.617-15.143 33.76-33.76 33.76zm78.272-51.909H442.35c-7.48-20.972-27.532-36.027-51.039-36.027-23.508 0-43.56 15.056-51.04 36.027h-19.686V189.792h72.291l76.707 86.611v78.865z"/><path d="m162.969 194.442-35.584-20.726v-68.163h-20.417v79.897l45.723 26.637z"/>
                    </svg>
                </div>
            </div>

            @if ($percentage == 100)
                <div style="color: #9d8661; text-align:right; margin-top:8px; font-size:12px;"><b>Бесплатная доставка!</b></div>
            @elseif ($percentage < 100)
                <div style="color: #9d8661; text-align:right; margin-top:8px; font-size:12px;">
                    <b>
                        <div class="uk-flex" style="justify-content: right; gap:5px">
                            <div>До бесплатной доставки</div> 
                            <div style="flex:0; white-space: nowrap;">
                                ₽ {{ App\Services\Helpers\Str::price(round(env('FREE_DELIVERY_FROM') - $totalPrice)) }}
                            </div>
                        </div>
                    </b>
                </div>
            @endif

            <hr />

            @if ($showLittleCart)
                <div class="uk-text-center">
                    <a href="{{ route("cartIndex") }}" class="uk-button uk-button-primary" onclick="ym({{ env('YM_ID') }}, 'reachGoal', 'GoToCart'); return true;">Оформить заказ</a>
                </div>
            @endif
            <!--Small card-->
        </div>  
    @endif

@else
    <a><span uk-icon="icon: bag"></span> (0)</a>
@endif