@extends('main')

@section('seo_title', 'Заказы клиента')
@section('seo_description', '')
@section('seo_keywords', '')

@section('robots')
    {{ \App\Http\Controllers\SeoController::robots(['nofollow', 'noindex']) }}
@endsection

@section('content')

<div class="uk-section-default uk-section uk-section-small">

    <div class="uk-child-width-1-1@s uk-child-width-1-2@m uk-grid" uk-grid="">
        
        @include('client.menu', ["page" => "order"])

        <div class="uk-width-expand@s">

            <h2>Ваши заказы</h2>

            @if (count($ShopOrders) > 0)
            <ul uk-accordion="" class="tm-order-history uk-text-small uk-accordion">
                @foreach ($ShopOrders as $ShopOrder)

                    @php

                        $orderImage = $ShopOrder->ShopOrderItems[0]->ShopItem->parentItemIfModification()->getImages(false);
                        //dd($ShopOrder->ShopOrderItems[0]->ShopItem->parentItemIfModification()->getImages(false));
                    @endphp

                    <li>
                        <div class="uk-accordion-title" href="#" id="uk-accordion-43-title-0" aria-controls="uk-accordion-43-content-0" aria-expanded="false" aria-disabled="false">
                            <ul class="uk-grid uk-grid-width-expand uk-text-small uk-margin-remove-left uk-child-width-1-5" uk-grid="">
                                <li class="uk-padding-remove-left uk-width-auto uk-first-column">
                                    <img class="uk-tovar-avatar" src="{{ $orderImage[array_key_first($orderImage)]["image_small"] }}" width="60" height="60" alt="">
                                </li>
                                <li>{{ $ShopOrder->id }}</li> 
                                <li>{{ date("d.m.Y H:i", strtotime($ShopOrder->created_at)) }}</li>  
                                <li>{{ \App\Services\Helpers\Str::price($ShopOrder->getSum()) }} {{ $ShopOrder->ShopCurrency->name }}</li>  
                            </ul>
                        </div>
                        <div class="uk-accordion-content" id="uk-accordion-43-content-0" role="region" aria-labelledby="uk-accordion-43-title-0" hidden="">
                            <div class="uk-child-width-1-2@s uk-child-width-1-3@m uk-grid uk-grid-stack" uk-grid="" uk-height-match="target: > div > .uk-card">
                                <div class="uk-first-column">
                                    <div class="uk-card uk-card-default uk-card-small uk-card-body">
                                        <div class="uk-h6">Контактные данные</div>
                                        <ul class="uk-list">
                                            <li>{{ implode(" ", [$ShopOrder->name, $ShopOrder->surname, $ShopOrder->patronymic]) }}</li>
                                            <li>{{ $ShopOrder->email }}</li>
                                            <li>{{ $ShopOrder->phone }}</li>
                                        </ul>
                                    </div>
                                </div>    
                
                                <div>
                                    <div class="uk-card uk-card-default uk-card-small uk-card-body" style="min-height: 152.266px;">
                                        <div class="uk-h6">Доставка</div>
                                        <ul class="uk-list">
                                            <li>{{ $ShopOrder->city }}</li>
                                            <li>Способ доставки: {{ $ShopOrder->ShopDelivery->name }}</li>
                                            
                                            @foreach ($ShopOrder->ShopDeliveryFieldValues as $ShopDeliveryFieldValue)
                                                @if (!in_array($ShopDeliveryFieldValue->shop_delivery_field_id, [14, 16, 17]))
                                                    <li>{{ $ShopDeliveryFieldValue->ShopDeliveryField->caption }}: {{ $ShopDeliveryFieldValue->value }}</li>
                                                @endif
                                                
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                
                                <div>
                                    <div class="uk-card uk-card-default uk-card-small uk-card-body" style="min-height: 152.266px;">
                                        <div class="uk-h6">Оплата</div>
                                        <ul class="uk-list">
                                            <li>{{ $ShopOrder->ShopPaymentSystem->name }}</li>
                                        </ul>
                                    </div>
                                </div>
                
                            </div>
                
                            <hr>
                        
                            <div class="uk-card uk-card-default uk-card-small uk-card-body">

                                @foreach ($ShopOrder->ShopOrderItems as $ShopOrderItem)

                                    @php
                                        $ShopItem = $ShopOrderItem->ShopItem->parentItemIfModification();
                                        $url = !is_null($ShopItem) ? $ShopItem->url : '';

                                        $images = $ShopItem->getImages(false);

                                    @endphp

                                    <div class="uk-margin">
                                        <div class="uk-grid-small uk-flex-middle uk-grid uk-grid-stack" uk-grid="">
                                            <div class="uk-width-auto uk-first-column">
                                                <img class="uk-tovar-avatar" src="{{ $images[array_key_first($images)]["image_small"] }}" width="120" height="120" alt="">
                                            </div>
                                            <div class="uk-width-expand"> 

                                                <div class="uk-margin-remove uk-h6"><a class="uk-link-reset" href="{{ $url }}">{{ $ShopOrderItem->name }}</a></div>
                                                <div class="uk-margin-remove-top tm-price">
                                                    {{ \App\Services\Helpers\Str::price($ShopOrderItem->price) }} х {{ round($ShopOrderItem->quantity) }} = {{ \App\Services\Helpers\Str::price($ShopOrderItem->price * $ShopOrderItem->quantity) }} {{ $ShopOrder->ShopCurrency->name }}
                                                    @if ($ShopOrderItem->old_price > 0)
                                                        <span>
                                                            {{ \App\Services\Helpers\Str::price($ShopOrderItem->old_price) }} {{ $ShopOrder->ShopCurrency->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>          
                                    </div>

                                @endforeach
                                            
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            {{ $ShopOrders->links(('vendor.pagination.default')) }}
            @else

                <p>У Вас еще нет заказов :(</p>

            @endif


        </div>
	</div>
</div>

@endsection

@section("css")

<style>
    .tm-order-history .uk-accordion-title{font-family: 'Open Sans'!important;font-size: 1.05rem;line-height: 1.4;}	
</style>

@endsection