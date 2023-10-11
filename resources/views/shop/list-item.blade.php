@php
$discountPercent = 0;
$prices = App\Http\Controllers\ShopDiscountController::getModificationsPricesWithDiscounts($item);
$Discount = App\Http\Controllers\ShopDiscountController::getMaxDiscount($item);

@endphp


    <div class="uk-card tm-tovar" data-id="{{ $item->id }}">
       
        <div class="uk-position-top-right add-to-favorite uk-position-xsmall uk-text-xsmall">
            
            @if (!isset($client) || is_null($client))
                @include('shop.window-login')
            @else
                @php
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

            @foreach ($item->getImages(false) as $image)
                <div data-src="{{ $image['image_small'] }}" uk-img="loading: eager" class="uk-height-medium uk-background-cover" alt=""></div>
            @endforeach  
        </div>
        <div class="uk-card-body uk-padding-remove-left uk-padding-remove-right">
            <h3 class="uk-card-title uk-margin-small-bottom">{{ $item->name }}</h3>
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
        <a class="uk-position-cover" href="{{ $item->url() }}"></a>
    </div>
