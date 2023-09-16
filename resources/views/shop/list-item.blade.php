<div>
    <div class="uk-card tm-tovar" data-id="{{ $item->id }}">
        <!--<div class="uk-position-top-left uk-overlay uk-overlay-default uk-text-small">- 15%</div>-->
        <!--<div class="uk-position-top-right uk-position-xsmall uk-text-xsmall"><a href="" class="uk-icon uk-icon-button tm-icon" uk-icon="heart"></a></div>-->
        <div class="uk-card-media-top">
            @foreach ($item->getImages(false) as $image)
                <div data-src="{{ $image['image_small'] }}" uk-img="loading: eager" class="uk-height-medium uk-background-cover" alt=""></div>
            @endforeach  
        </div>
        <div class="uk-card-body uk-padding-remove-left uk-padding-remove-right">
            <h3 class="uk-card-title uk-margin-small-bottom">{{ $item->name }}</h3>
            <p class="uk-margin-remove-top tm-price">{{ number_format($item->price, 0, ',', ' ') }} {{ !is_null($item->ShopCurrency) ? $item->ShopCurrency->code : '' }}</p>
        </div>
        <a class="uk-position-cover" href="{{ $item->url() }}"></a>
    </div>
</div>