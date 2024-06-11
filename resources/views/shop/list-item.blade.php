@php
$discountPercent = 0;
$Discount = App\Http\Controllers\ShopDiscountController::getMaxDiscount($item);
$url = $item->url;
@endphp


<div class="uk-card tm-tovar" itemprop="itemListElement" itemscope itemtype="https://schema.org/Offer">
    
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
            <div class="uk-position-top-left uk-overlay uk-overlay-default uk-text-small uk-position-z-index">
                
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
                    <ul class="uk-slideshow-items list-item-image">
                        @foreach ($item->getImages() as $image)
                            
                            <li>
                                <div data-src="{{ $image['image_small'] }}" uk-img="loading: lazy" class="uk-height-1-1 uk-background-cover" alt=""></div>
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
        <div class="uk-h3 uk-card-title uk-margin-small-bottom">
            <a href="{{ $url }}" itemprop="name">{{ $item->name }}</a>
            <link itemprop="url" href="{{ $url }}">
        </div>
        <p class="uk-margin-remove-top tm-price">
            @php
                $defaultModification = $item->defaultModification();
                $Object   = $defaultModification ? $defaultModification : $item;
                $price    = $Object->getPriceApplyCurrency($Currency);
                $oldPrice = $Object->getOldPriceApplyCurrency($Currency);
            @endphp

            <span id="item-price" class="item-price">
                {{ $Currency->name ?? '' }}
                {{ App\Services\Helpers\Str::price($price) }}
                <meta itemprop="price" content="{{ $price }}">
                <meta itemprop="priceCurrency" content="RUB">
                <link itemprop="availability" href="http://schema.org/InStock">
            </span> 

            @if ($oldPrice > 0)
                <span class="item-old-price" id="item-old-price">
                    <span>{{ $Currency->name ?? '' }}</span>
                    {{ App\Services\Helpers\Str::price($oldPrice) }}
                </span>
            @endif
        </p>
    </div>
</div>
