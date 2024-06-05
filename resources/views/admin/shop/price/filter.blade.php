
@if (count($shopItems) > 0)
    <table class="table table-bordered admin-table">
        <thead>
            <tr>
                <th style="width: 1%">
                    <label>
                        <input class="custom-control-input check-all" type="checkbox">
                    </label>
                </th>
                <th style="width: 1%">#ID</th>
                <th style="width: 40px"  class="px-0 text-center"><i class="fa fa-bars" title="—"></i></th>
                <th>Название</th>
                <th  class="d-mob-none" width="100px">Цена</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($shopItems as $shopItem)



                <tr>
                    <td>
                        <label>
                            <input name="shop_items[{{ $shopItem->id }}]" class="custom-control-input check-item" type="checkbox">
                        </label>
                    </td>
                    <td>
                        {{ $shopItem->id }}
                    </td>

                    <td class="px-0 text-center td_image">
                        @php
                            $oShopItemImage = \App\Models\ShopItemImage::where("shop_item_id", "=", $shopItem->id)
                                ->where("image_small", "!=", "")
                                ->orderBy('sorting', 'Asc')
                                ->orderBy('id', 'Asc')
                                ->first();
                            $image_small= !is_null($oShopItemImage) ? $shopItem->path() . $oShopItemImage->image_small : '';
                        @endphp

                        @if (!empty($image_small))
                            <img src="{{ $image_small }}" alt="" height="40">
                        @else
                            <i class="la la-image " title=""></i> 
                        @endif
                    </td>

                    <td class="td_editable">
                        
                        <span id="apply_check_shopItem_name_{{ $shopItem->id }}" class="editable product-name fw-semibold line-through-if-off">{{ $shopItem->name }}</span>

                        <a href="{{ $shopItem->url }}" target="_blank">
                            <i class="las la-external-link-alt"></i>
                        </a> 
                        <br>

                        <span class="text-muted font-13 fw-semibold">{{ $shopItem->marking }} </span>                         
                    </td>
                    <td width="100px" class="td_editable d-mob-none">
                        <div class="d-flex">

                            @php
                            $price = (int)$shopItem->price;
                            @endphp
                            <span 
                                data-value="{{ $price }}" 
                                id="apply_check_ShopItem_price_{{ $shopItem->id }}"
                                class="editable mx-1" id="item-price">{{ App\Services\Helpers\Str::price($price) }}
                            </span> 

                            <span class="mx-1">{{ $shopItem->shop_currency_id > 0 && $shopItem->ShopCurrency ? $shopItem->ShopCurrency->name : ''}}</span>
                            
                        </div>
                    </td>
                </tr>

            @endforeach
        </tbody>
    </table>

    <input type="type" name="new_price" class="form-control" placeholder="Новая цена">

@endif