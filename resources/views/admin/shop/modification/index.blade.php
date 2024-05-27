

<div class="card-body p-0">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th style="width: 1%">#ID</th>
                <th style="width: 40px" class="px-0 text-center"><i class="fas fa-check" title="Модификация по умолчанию"></i></th>
                <th style="width: 40px" class="px-0 text-center"><i class="fa fa-bars" title="—"></i></th>
                <th>Свойства</th>
                <th width="200px">Цена</th>
                <th width="40px"><i class="fa fa-lightbulb-o" title="Активность"></i></th>
                <th width="60px"><i class="fas fa-sort-amount-down" title="—"></i></th>
                <th class="td-actions-large"></th>
            </tr>
        </thead>
        <tbody>

            @foreach ($shopItems as $shopItem)

                @php
                    $isActive = $shopItem->active == 1 ? false : true;
                @endphp

                <tr @class(['off' => $isActive, "default" => $shopItem->default_modification == 1])>
                    <td>{{ $shopItem->id }}</td>
                    <td class="text-center">
                        <div class="form-check">
                            <input data-route="{{ route('defaultModification', $shopItem->id) }}" title="default" class="form-check-input" type="radio" name="default_modification" value="{{ $shopItem->id }}" @if($shopItem->default_modification == 1) checked @endif >
                        </div>
                    </td>
                    <td class="px-0 text-center td_image">

                        @php

                            $ShopModificationImage = $shopItem->ShopModificationImage;
                            $ShopItemImage = !is_null($ShopModificationImage) ? $ShopModificationImage->ShopItemImage : null;

                            $image_small = !is_null($ShopItemImage) ? $oShopItem->path() . $ShopItemImage->image_small : '';

                        @endphp

                    
                        @if (!empty($image_small))
                            <img src="{{ $image_small }}" alt="" height="40">
                        @else
                            <i class="la la-image " title=""></i> 
                        @endif
                    </td>
                    <td>

                        @foreach ($shopItem->PropertyValueInts as $propertyValueInt)
                            @if ($propertyValueInt->ShopItemListItem)
                                <div>{{ $propertyValueInt->ShopItemProperty->name }}: {{ $propertyValueInt->ShopItemListItem->value }}</div>
                            @endif
                            
                        @endforeach 
                    </td>
                    <td width="200px" class="td_editable">
                        

                        @if ($shopItem::$priceView == 0)

                        <span id="apply_check_shopItem_price_{{ $shopItem->id }}" class="editable">{{ $shopItem->price }}</span>
                            
                        @elseif($shopItem::$priceView == 1)
                            @php
                    
                            $oldPrice = $shopItem->oldPrice();
            
                            @endphp
                            <span class=" mx-1" id="item-price">{{ App\Services\Helpers\Str::price($shopItem->price()) }}</span> 
                            @if ($oldPrice)<span class="item-old-price mx-1" id="item-old-price">{{ $oldPrice }}</span>@endif

                            <span class="mx-1">{{ $shopItem->shop_currency_id > 0 && $shopItem->ShopCurrency ? $shopItem->ShopCurrency->name : ''}}</span>
                            
                            @if ($oldPrice)
                                @php
                                    $discounts = \App\Http\Controllers\ShopDiscountController::getDiscountsForItemAndModifications($shopItem);
                                    $aDiscountTitles = [];
                                    foreach ($discounts as $discount) {
                                        $aDiscountTitles[] = $discount->name;
                                    }
                                @endphp
                                <i class="las la-tags font-20 palegreen mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="{{ implode(',', $aDiscountTitles) }}"></i>
                            @endif

                        @endif


                    </td>
    

                    <td>
                        <span onclick="toggle.init($(this))" @class([
                            'pointer',
                            'ico-inactive' => $isActive,
                        ]) id="toggle_shopItem_active_{{ $shopItem->id }}">
                
                            <i class="lar la-lightbulb font-20"></i>
                        </span>
                    </td>
                    <td width="60px" class="td_editable"><span id="apply_check_shopItem_sorting_{{ $shopItem->id }}" class="editable">{{ $shopItem->sorting }}</span></td>
                    <td class="td-actions-large">

                        <a href="{{ route('modification.edit', $shopItem->id) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                        {{-- <a onclick="confirmCopy($(this)); return false;" href="{{ route('copyShopItem', $shopItem->id) }}" class="mr-2"><i class="las la-copy text-secondary font-16"></i></a>
                            --}}
                        <button type="button" onclick="adminModification.deleteModification('{{ route('modification.destroy', $shopItem->id) }}', $(this))" class="td-list-delete-btn">
                            <i class="las la-trash-alt text-secondary font-16"></i>
                        </button>

                    </td>
                </tr>

            @endforeach

        </tbody>
    </table>
</div>


<style>
    tr.default{background: #5d8cfb36}
</style>
