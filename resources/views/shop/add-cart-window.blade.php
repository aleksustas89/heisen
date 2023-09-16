
<div id="modal-full" class="uk-modal-full" uk-modal>
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
        <div class="uk-grid-collapse uk-child-width-1-2@s uk-flex-middle" uk-grid>
            @php
            $ShopItemImage = $ShopItem->getShopItemImage();
            @endphp
            @if ($ShopItemImage)
                <div class="uk-background-cover" style="background-image: url('{{ $ShopItemImage->ShopItem->path() . $ShopItemImage->image_large }}');" uk-height-viewport></div>
            @endif
            <div class="uk-padding-large">
                <h1>Поздравляем!</h1>
                <p>{{ $ShopItem->name }} почти у Вас!</p>
                <p>
                    <a href="{{ route('cartIndex') }}" class="uk-button uk-buttom-small uk-button-primary active">Оформить заказ</a>
                    <a class="uk-button uk-buttom-small uk-button-primary uk-modal-close">Продолжить покупки</a>
                </p>
            </div>
        </div>
    </div>
</div>