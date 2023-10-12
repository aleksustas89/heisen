@php
$discountPercent = 0;
$prices = App\Http\Controllers\ShopDiscountController::getModificationsPricesWithDiscounts($item);
$Discount = App\Http\Controllers\ShopDiscountController::getMaxDiscount($item);
$url = $item->url();
@endphp


    <div class="uk-card tm-tovar">
       
        <div class="uk-position-top-right add-to-favorite uk-position-xsmall uk-text-xsmall">
            @if (\App\Models\ClientFavorite::$Type == 0)
                @if (!isset($client) || is_null($client))
                    @include('shop.window-login')
                @else
                    @php
                    $active = in_array($item->id, $clientFavorites) ? true : false;
                    @endphp
                    <a onclick="Favorite.add($(this), {{ $item->id }}, '{{ route('addFavorite') }}')" @class(["add-to-favorite-link", "uk-icon", "uk-icon-button", "tm-icon", "active" => $active]) uk-icon="heart"></a>
                @endif
            @elseif(\App\Models\ClientFavorite::$Type == 1)
            @php
                $clientFavorites = \App\Http\Controllers\Auth\ClientController::getCookieFavorites();
                $active = in_array($item->id, $clientFavorites) ? true : false;
                @endphp
                <a onclick="Favorite.add($(this), {{ $item->id }}, '{{ route('addFavorite') }}')" @class(["add-to-favorite-link", "uk-icon", "uk-icon-button", "tm-icon", "active" => $active]) uk-icon="heart"></a>
            @endif

        </div>
        <div class="uk-card-media-top uk-position-relative">

            @if ($Discount) 
                <div class="uk-position-top-left uk-overlay uk-overlay-default uk-text-small">
                    
                    @if ($Discount->type == 0)
                        до -{{ $Discount->value }}% 
                    @elseif($Discount->type == 1)
                        до -{{ App\Http\Controllers\ShopDiscountController::getDiscountPercent($item, $Discount->value) }}%
                    @endif
                    
                </div>
            @endif

            <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slideshow>
                <div class="uk-slider-container">
                    <a href="{{ $url }}">
                        <ul class="uk-slideshow-items uk-height-medium">
                            @foreach ($item->getImages() as $image)
                                
                                <li>
                                    <div data-src="{{ $image['image_small'] }}" uk-img="loading: eager" class="uk-height-1-1 uk-background-cover" alt=""></div>
                                </li>
                            
                            @endforeach 
                        </ul>
                    </a>
                    <a class="uk-position-center-left uk-position-small uk-hidden-hover" href uk-slidenav-previous uk-slideshow-item="previous"></a>
                    <a class="uk-position-center-right uk-position-small uk-hidden-hover" href uk-slidenav-next uk-slideshow-item="next"></a>
                </div>
            </div>


        </div>
        <div class="uk-card-body uk-padding-remove-left uk-padding-remove-right">
            <h3 class="uk-card-title uk-margin-small-bottom"><a href="{{ $url }}">{{ $item->name }}</a></h3>
            <p class="uk-margin-remove-top tm-price">
               
                @if (count($prices) > 1)
                    {{ App\Services\Helpers\Str::price(min($prices)) }} - {{ App\Services\Helpers\Str::price(max($prices)) }}
                    <span class="item-old-price" id="item-old-price">
                        @if (!in_array($item->price, $prices))
                            {{ App\Services\Helpers\Str::price($item->price) }}
                        @endif
                    </span>
                    {{ !is_null($item->ShopCurrency) ? $item->ShopCurrency->code : '' }}
                @else
                        {{ App\Services\Helpers\Str::price($item->price()) }} 
                        <span>{{ !empty($item->oldPrice()) ? App\Services\Helpers\Str::price($item->oldPrice()) : '' }}</span>
                        {{ !is_null($item->ShopCurrency) ? $item->ShopCurrency->code : '' }}
                @endif
                
                
            </p>
        </div>
    </div>
