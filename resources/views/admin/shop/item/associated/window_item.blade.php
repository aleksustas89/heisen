<tr>
    <td width="40px">
        @if ($shopItem->id != $aShopItem->id)
            <div class="form-switch form-switch-success"> 
                <input value="{{ $shopItem->id }}" 
                    @if (isset($ShopItemAssociatedItems) && in_array($shopItem->id, $ShopItemAssociatedItems)) checked @endif 
                    class="form-check-input" 
                    name="associated_items[]" 
                    type="checkbox" 
                    id="associated-item-{{ $shopItem->id }}">
            </div>
        @endif
    </td>
    <td class="d-flex align-items-center">
        
        @if ($images = $shopItem->getImages(false))
            
            @foreach ($shopItem->getImages(false) as $k => $image)
                @if (!empty($image['image_small']))
                    <img src="{{ $image['image_small'] }}" alt="" height="40">
                @endif
            @endforeach
        @else
            <i class="la la-image fs-1" title=""></i> 
        @endif


        <div class="d-inline-block align-middle mb-0 mx-3">
            {{ $shopItem->name }}
            <div>{{ $shopItem->marking }}</div>
        </div>
    </td>
</tr>