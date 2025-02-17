@extends('main')

@section('seo_title'){{ \App\Http\Controllers\SeoController::showItemTitle($shop, $item, $Modification) }}@endsection
@section('seo_description'){{ \App\Http\Controllers\SeoController::showItemDescription($shop, $item, $Modification) }}@endsection
@section('seo_keywords', $item->seo_keywords)

@section('canonical')

    @if (!is_null($defaultModification))
        <link rel="canonical" href="https://{{ request()->getHost() }}{{ $defaultModification->url }}" />
    @elseif ($item->canonical > 0 && !is_null($Canonical = \App\Models\ShopItem::find($item->canonical)))
        <link rel="canonical" href="https://{{ request()->getHost() }}{{ $Canonical->url }}" />
    @else
        <link rel="canonical" href="https://{{ request()->getHost() }}{{ $item->url }}" />
    @endif
@endsection

@section('robots')
    {{ \App\Http\Controllers\SeoController::robots(['follow', 'index']) }}
@endsection

@section('content')

    @php

    $client = Auth::guard('client')->user();
    $clientFavorites = !is_null($client) ? $client->getClientFavorites() : [];
    @endphp

	<div class="uk-section-xsmall uk-padding-remove-top" itemscope itemtype="http://schema.org/Product">
        <div uk-grid>
            @if (count($images) > 0)
                <div class="uk-width-1-2@m">
                    <div id="uk-slideshow-items" uk-slideshow="animation: push;ratio: 1:1; minHeight: 300;">
                        <div class="uk-position-relative uk-visible-toggle" tabindex="-1">
                            <ul class="uk-slideshow-items" uk-lightbox="animation: scale">
                                @foreach ($images as $k => $image)
                                    @if (isset($image['image_large']))
                                        <li id="uk-slide-{{$k}}">
                                            <a href="{{ $image['image_large'] }}">
                                                <img itemprop="image" alt="{{ $imageMask }}" title="{{ $imageMask }}" uk-img="loading: lazy" data-src="{{ $image['image_large'] }}" alt="" uk-cover>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
        
                            <a class="uk-position-center-left uk-position-small uk-hidden-hover" uk-slidenav-previous uk-slideshow-item="previous"></a>
                            <a class="uk-position-center-right uk-position-small uk-hidden-hover" uk-slidenav-next uk-slideshow-item="next"></a>
                        </div>
                        <div class="uk-margin" uk-slider>
                            <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1">
                                <ul class="uk-slider-items uk-child-width-1-2 uk-child-width-1-3@s uk-child-width-1-4@m uk-grid uk-grid-small">        
                   
                                    @php
                                        $k = 0;
                                    @endphp
                                    @foreach ($images as $image)
                                        @if (isset($image['image_large']))
                                            <li uk-slideshow-item="{{ $k }}">
                                                <a style="background-position: center;" uk-img="loading: lazy" data-src="{{ $image['image_large'] }}" uk-img=""></a>
                                            </li>
                                            @php
                                                $k++;
                                            @endphp
                                        @endif
                                    @endforeach
                                </ul>
                                <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
                                <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next"></a>
                            </div>
                            <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="uk-width-expand@m" id="item-information">

                @if (session('success'))
                    <div class="uk-alert-success" uk-alert>
                        <a href class="uk-alert-close" uk-close></a>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="uk-margin">
                    <a class="el-content uk-link-text" href="{{ $item->parentItemIfModification()->ShopGroup->url }}">{{ $item->parentItemIfModification()->ShopGroup->name }}</a>   
                </div>

                <h1 id="item-name" class="uk-h2 uk-margin-remove-vertical" itemprop="name">
                    @if ($Modification)
                        {{ $Modification->name }}
                    @else
                        {{ $item->name }}
                    @endif
                </h1>
                

                <div class="uk-h3 uk-margin uk-margin-top" itemprop="offers" itemscope itemtype="http://schema.org/Offer"> 

                    @if (isset($prices) && count($prices) > 1)
                        <span itemprop="price" id="item-price">
                            {{ App\Services\Helpers\Str::price(min($prices)) }} - {{ App\Services\Helpers\Str::price(max($prices)) }}
                        </span>
                        <span class="item-old-price" id="item-old-price">
                            @if (!in_array($item->price, $prices))
                                {{ App\Services\Helpers\Str::price($item->price) }}
                            @endif
                        </span>
                    @elseif($Modification)
                        <span itemprop="price" id="item-price">{{ App\Services\Helpers\Str::price($Modification->price()) }}</span> 
                        <span class="item-old-price" id="item-old-price">{{ App\Services\Helpers\Str::price($Modification->oldPrice()) }}</span>
                    @else 
                        <span itemprop="price" id="item-price">{{ App\Services\Helpers\Str::price($item->price()) }}</span> 
                        <span class="item-old-price" id="item-old-price">{{ App\Services\Helpers\Str::price($item->oldPrice()) }}</span>
                    @endif

                    @php
                        $oCurrency = $item->ShopCurrency;
                    @endphp

                    <span itemprop="priceCurrency">{{ !is_null($oCurrency) ? $oCurrency->code : '' }}</span>

                </div>
    
                <form id="add_to_cart">   
                    @csrf

                    <input type="hidden" name="shop_item_id" value="{{ $item->id }}" />

                    @php
                        $choose_properties_tooltip = [];
                    @endphp

                    @foreach ($aModProperties as $property)

                        @php
                            $choose_properties_tooltip[] = $property->name;
                            $defaultValue = '';
                        @endphp
                        
                        <label class="uk-form-label" data-property-id="{{ $property->id }}" data-name="{{ $property->name }}">{{ $property->name }}</label>
                        <div class="uk-margin-small">


                            @if ($property->destination == 1 && $property->type == 4 && !is_null($property->shopItemList))
                                <ul class="uk-grid uk-grid-xsmall tm-color-switcher" uk-grid="">

                                    @if (isset($aPropertyListItems[$property->id]))
                                        @foreach ($aPropertyListItems[$property->id] as $key => $Shop_Item_List_Item)
                                            
                                            @php
                                                if (isset($aDefaultValues) && in_array($Shop_Item_List_Item->id, $aDefaultValues)) {
                                                    $defaultValue = $Shop_Item_List_Item->id;
                                                }

                                                $aModification = \App\Models\ShopItem::where("modification_id", $item->id)
                                                                                ->join("property_value_ints", "property_value_ints.entity_id", "=", "shop_items.id")
                                                                                ->where("property_value_ints.property_id", $property->id)
                                                                                ->where("property_value_ints.value", $Shop_Item_List_Item->id)
                                                                                ->where("shop_items.deleted", 0)
                                                                                ->where("shop_items.active", 1)
                                                                                ->first();
                                            @endphp
                                        
                                            @if (!is_null($aModification))
                                                <li @class(["active" => isset($aDefaultValues) && in_array($Shop_Item_List_Item->id, $aDefaultValues)])><a @if(!is_null($aModification)) href="{{ $aModification->url }}" @endif onclick="Modification.choose($(this)); return false;" data-id="{{ $Shop_Item_List_Item->id }}" uk-tooltip="{{ $Shop_Item_List_Item->value }}" class="uk-border-circle" data-src="{{ $Shop_Item_List_Item->description }}" uk-img=""></a></li>
                                            @endif
                                            
                                        @endforeach
                                    @endif

                                </ul>

                                <input type="hidden" name="property_{{ $property->id }}" value="{{ $defaultValue }}" />

                            @elseif($property->destination == 0 && $property->type == 4 && !is_null($property->shopItemList) && isset($aPropertyListItems[$property->id]))
                                <ul class="uk-grid uk-grid-xsmall tm-other-switcher" uk-grid="">            
                                    @foreach ($aPropertyListItems[$property->id] as $key => $Shop_Item_List_Item)

                                        @php
                                            if (isset($aDefaultValues) && in_array($Shop_Item_List_Item->id, $aDefaultValues)) {
                                                $defaultValue = $Shop_Item_List_Item->id;
                                            }
                                        @endphp
                                        
                                        <li @class(["active" => isset($aDefaultValues) && in_array($Shop_Item_List_Item->id, $aDefaultValues)])><a onclick="Modification.choose($(this))" data-id="{{ $Shop_Item_List_Item->id }}" uk-tooltip="{{ $Shop_Item_List_Item->value }}">{{ $Shop_Item_List_Item->value }}</a></li>
                                    @endforeach
                                </ul>

                                <input type="hidden" name="property_{{ $property->id }}" value="{{ $defaultValue }}" />

                            @endif

                            

                        </div>

                    @endforeach

                </form>
                    
                <div class="uk-margin-medium" uk-margin>
                    <div uk-form-custom="target: true" class="uk-visible@s">
                        <input type="number" class="uk-input uk-form-width-xsmall" name="quantity" value="1" title="Qty" size="4" min="1" max="" step="1" placeholder="" inputmode="numeric" autocomplete="off">
                    </div>
                    <button type="button" id="cart_add" data-route="{{ route('cartAdd') }}" data-uk-tooltip="Выберите {{ implode('и', $choose_properties_tooltip) }}" uk-tooltip="Выберите {{ implode('и', $choose_properties_tooltip) }}" @if($Modification) onclick="Cart.add('{{ route('cartAdd') }}', {{ $Modification->id }}, $('[name=\'quantity\']').val())" @else disabled @endif  class="uk-button uk-buttom-small uk-button-primary buy-btn">КУПИТЬ <span uk-icon="icon: cart"></span></button>
                    <button uk-toggle="target: #quick-order" type="button" id="fast_order" data-uk-tooltip="Выберите {{ implode('и', $choose_properties_tooltip) }}" uk-tooltip="Выберите {{ implode('и', $choose_properties_tooltip) }}" @if(!$Modification) disabled @endif class="uk-button uk-buttom-small uk-button-primary buy-btn">КУПИТЬ В ОДИН КЛИК</button>
                    <div id="quick-order" uk-modal>
                        <div class="uk-modal-dialog uk-modal-body">
                            <h2 class="uk-modal-title">Быстрый заказ</h2>
                            
                            <form id="shop-quich-order" type="POST">
                                @csrf

                                <p>Заполните форму и наши менеджеры свяжутся с Вами для завершения заказа</p>

                                <div class="uk-margin">
                                    <label class="uk-form-label" for="form-stacked-text">Имя</label>
                                    <div class="uk-form-controls">
                                        <input required class="uk-input" name="name" type="text" placeholder="Ваше Имя">
                                    </div>
                                </div>

                                <div class="uk-margin">
                                    <label class="uk-form-label" for="form-stacked-text">Телефон</label>
                                    <div class="uk-form-controls">
                                        <input required class="uk-input" name="phone" type="text" placeholder="Телефон">
                                    </div>
                                </div>

                                <input type="hidden" name="shop_item_id" value="{{ $Modification->id ?? 0 }}" />
                                <p class="uk-text-right">
                                    <button class="uk-button uk-button-default uk-modal-close" type="button">Отменить</button>
                                    <button class="uk-button uk-button-primary" type="submit">Заказать</button>
                                </p>
                            </form>

                        </div>
                    </div>

                    @if (\App\Models\ClientFavorite::$Type == 0)
                        @php
                            $clientFavorites = !is_null($client) ? $client->getClientFavorites() : [];
                        @endphp
                        @if (is_null($client))
                            @include('shop.window-login')
                        @else
                            @php
                            $active = in_array($item->id, $clientFavorites) ? true : false;
                            @endphp
                            <a onclick="Favorite.add($(this), {{ $item->id }}, '{{ route('addFavorite') }}')" @class(["add-to-favorite-link", "uk-icon", "uk-icon-button", "tm-icon", "active" => $active]) uk-icon="heart"></a>
                        @endif
                    @elseif (\App\Models\ClientFavorite::$Type == 1)
                        @php
                        $clientFavorites = \App\Http\Controllers\Auth\ClientController::getCookieFavorites();
                        $active = in_array($item->id, $clientFavorites) ? true : false;
                        @endphp
                        <a onclick="Favorite.add($(this), {{ $item->id }}, '{{ route('addFavorite') }}')" @class(["add-to-favorite-link", "uk-icon", "uk-icon-button", "tm-icon", "active" => $active]) uk-icon="heart"></a>
                    @endif
        

                </div>


                <ul uk-accordion="collapsible: false" class="uk-list uk-list-divider">
                    @if (!empty($item->description))
                        <li>
                            <a class="uk-accordion-title">Описание</a>
                            <div class="uk-accordion-content" itemprop="description">
                                {!! $item->description !!}
                            </div>
                        </li>
                    @endif
                    
                    @php
                    $properties = $item->getProperties();
                    $Values = false;
                    foreach ($properties as $key => $property) {
                        if (count($property["property_values"]) > 0) {
                            $Values = true;
                        }
                    }

                    @endphp

                    @if (($properties && count($properties) > 0 && $Values) || count($Dimensions) > 0)
                        <li>
                            <a class="uk-accordion-title">ХАРАКТЕРИСТИКИ</a>
                            <div class="uk-accordion-content">
                        
                                    <ul class="uk-list">
                                        @if (count($Dimensions) > 0)
                                            @foreach ($Dimensions as $Dimension)
                                                <li>
                                                    <b>{{ $Dimension["name"] }}:</b>
                                                    {{ $Dimension["value"] }} {{ $Dimension["measure"] }}
                                                </li>
                                            @endforeach
                                        @endif
                                        @if ($properties && count($properties) > 0 && $Values)
                                            @foreach ($properties as $property)
                                                @if ($property["show_in_item"] == 1 && count($property["property_values"]) > 0)
                                                    <li>
                                                        <b>{{ $property["property_name"] }}:</b>
                                                        @foreach ($property["property_values"] as $value)
                                                            {{ $value }}
                                                        @endforeach
                                                    </li>
                                                @endif
                                            @endforeach
                                        @endif

                                    </ul>
                            </div>
                        </li>   
                    @endif 

                    <li>
                        <a class="uk-accordion-title">ПРЕИМУЩЕСТВА</a>
                        <div class="uk-accordion-content">
                            <ul>
                                <li><p>Кожаные изделия всегда выглядят достойно, солидно и приносят нотки благородности, что свидетельствует о наличии вкуса у владельцев.</p></li>
                                <li><p>Изделия из кожи очень практичны и долговечны в носке. Их тяжело порвать или посадить на них невыводимое пятно. Это практически невозможно. Но если возникает необходимость, то на такое изделие всегда можно поставить латку, которая не испортит, а только придаст неповторимости данному изделию.</p></li>
                                <li><p>За такими изделиями легко ухаживать.</p></li>
                                <li><p>Немного потертости на кожаном изделии придает ему нотку эпатажа и восторга в глазах любителей винтажного направления в мире моды.</p></li>
                                <li><p>Кожаные изделия отличаются своей дороговизной, но их способность сохранять первозданный вид долгое время, делает их значительно более экономным вариантом, по сравнению с изделиями из других материалов.</p></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a class="uk-accordion-title">ВИГОДЫ ПРИ РАБОТЕ С HEISEN</a>
                        <div class="uk-accordion-content">
                            <p>Мы разрабатываем собственные коллекции, опираясь на модные тенденции, и совершенствуем классические модели. Аксессуары от наших мастеров в процессе эксплуатации приобретают винтажный шарм, не теряя связь со временем.</p>
                            <p>Мы предлагаем аксессуары, в которых учтено всё, они очень долго служат хозяевам, сохраняют презентабельный вид, а в процессе носки приобретают неповторимую уникальность и изысканность. Такие вещи подчёркивают вкус владельца и помогают создать собственный стиль.</p>
                        </div>
                    </li>

                    <li>
                        <a class="uk-accordion-title">Отзывы <span class="uk-badge">{{ count($Comments) }}</span></a>
                        <div class="uk-accordion-content">
                            @if ($Comments && count($Comments) > 0)
                                @foreach ($Comments as $Comment)
                                    @include('comment.comment', [
                                        'Comment' => $Comment,
                                        'shopItem' => true,
                                    ])
                                @endforeach
                            @endif

                            <form action="{{ route('saveComment') }}" method="POST" enctype="multipart/form-data">

                                @csrf
            
                                <div class="uk-card uk-card-default uk-card-body uk-card-small uk-margin-large-top">
                                    <div class="uk-h2">Добавить отзыв к товару</div>
                                    <hr>
            
                                    <div class="uk-flex uk-flex-around grade-stars">
                                        <span class="uk-flex uk-flex-column uk-flex-middle grade-star cursor-pointer">
                                            <span class="uk-icon" uk-icon="icon: star; ratio: 3.5"></span>
                                            плохо
                                        </span>
                                        <span class="uk-flex uk-flex-column uk-flex-middle grade-star cursor-pointer">
                                            <span class="uk-icon" uk-icon="icon: star; ratio: 3.5"></span>
                                            так себе
                                        </span>
                                        <span class="uk-flex uk-flex-column uk-flex-middle grade-star cursor-pointer">
                                            <span class="uk-icon" uk-icon="icon: star; ratio: 3.5"></span>
                                            нормально
                                        </span>
                                        <span class="uk-flex uk-flex-column uk-flex-middle grade-star cursor-pointer">
                                            <span class="uk-icon" uk-icon="icon: star; ratio: 3.5"></span>
                                            хорошо
                                        </span>
                                        <span class="uk-flex uk-flex-column uk-flex-middle grade-star cursor-pointer">
                                            <span class="uk-icon" uk-icon="icon: star; ratio: 3.5"></span>
                                            отлично
                                        </span>
                                        <input type="hidden" name="grade" value="" />
                                    </div>
                                    
                            
                                    <div class="uk-margin">
                                        <label class="uk-form-label" for="form-stacked-text">Тема</label>
                                        <div class="uk-form-controls">
                                            <input value="" class="uk-input " name="subject" required="" id="form-stacked-text" type="text" placeholder="Тема">
                                        </div>
                                    </div>
                                                 
                                    <div class="uk-margin">
                                        <div class="uk-form-label">Комментарий</div>   
                                        <textarea required="" name="text" class="uk-textarea" rows="5" placeholder="Комментарий" aria-label="Textarea"></textarea>
                                    </div>
            
                                    <div class="uk-margin">
                                        <label class="uk-form-label" for="form-stacked-text">Имя, Фамилия</label>
                                        <div class="uk-form-controls">
                                            <input required="" value="{{ !is_null($client) ? implode(" ", [$client->name, $client->surname]) : '' }}" class="uk-input" name="author" id="form-stacked-text" type="text" placeholder="Имя, Фамилия">
                                        </div>
                                    </div>
                                    <div class="uk-margin">
                                        <label class="uk-form-label" for="form-stacked-text">Телефон</label>
                                        <div class="uk-form-controls">
                                            <input value="{{ $client->phone ?? '' }}" class="uk-input " name="phone" id="form-stacked-text" type="text" placeholder="Введите телефон...">
                                        </div>
                                    </div>
                                    <div class="uk-margin">
                                        <label class="uk-form-label" for="form-stacked-text">E-mail</label>
                                        <div class="uk-form-controls">
                                            <input value="{{ $client->email ?? '' }}" class="uk-input " name="email" id="form-stacked-text" type="text" placeholder="Введите e-mail...">
                                        </div>
                                    </div>
                                
                                    <input type="hidden" name="shop_item_id" value="{{ $item->id }}" />
                                    <div class="uk-text-center uk-margin">
                                        <button class="uk-button uk-button-primary uk-width-1-1">Оставить отзыв</button>
                                    </div>  
                        
                                </div>
            
                            </form>

                        </div>
                    </li>
                    <li>
                        <a class="uk-accordion-title">Бесплатная доставка от 5000 рублей!</a>
                        <div class="uk-accordion-content">
                            Мы производим бесплатную доставку товара при покупке от 5000 рублей по всей России на отделения почты (ПВЗ)."
                        </div>
                    </li>

                </ul>

                @if (!is_null($shopItemShortcuts) && count($shopItemShortcuts))
                    <hr />

                    <p><span uk-icon="icon: tag" class="uk-margin-small-right"></span>Теги:</p>
                    <div class="shortcuts">
                        @foreach ($shopItemShortcuts as $shopItemShortcut)
                            <a class="btn-shortcut" href="{{ $shopItemShortcut->ShopGroup->url }}">#{{ $shopItemShortcut->ShopGroup->name }}</a>
                        @endforeach
                    </div>

                @endif

                <hr />

            </div>
        </div>

        @if (isset($ShopItemAssociatedItems) && count($ShopItemAssociatedItems)>0)

            <h2 class="uk-h1 uk-margin-small uk-text-center uk-margin-xlarge uk-margin-bottom">Сопутствующие товары</h2>
            <div class="uk-child-width-1-3@s uk-child-width-1-5@m uk-child-width-1-2 uk-grid-small uk-grid" uk-grid="" itemscope itemtype="https://schema.org/OfferCatalog"> 
                @foreach ($ShopItemAssociatedItems as $ShopItemAssociatedItem)
                    @include('shop.list-item', [
                        'item' => $ShopItemAssociatedItem,
                        'client' => $client,
                        'clientFavorites' => $clientFavorites,
                    ])
                @endforeach   
            </div>
        @endif

    </div>

@endsection

@section("css")
    @php
        App\Services\Helpers\File::css('/css/colors.css');
    @endphp
    <style>
        .uk-slider-items a{
            height: 100px;
            width: 100%;
            display: inline-block;
            background-size: cover;
        }
        .grade-star.hover polygon, .grade-star.fill polygon {
            fill: #c39c5c;
            stroke: #cdb58d !important;
        }

        @media (max-width: 480px) {
            .grade-star {
                font-size: 10px;
            }
            .grade-star svg{ 
                width: 30px;
            }
        }
        .shortcuts {
            gap: 20px;
            display: flex;
            flex-wrap: wrap;
        }
        .btn-shortcut {
            font-size: 20px;
            border-radius: 0 !important;
            text-transform: uppercase;
        }
    </style>
@endsection

@section("js")
   
    @php
        App\Services\Helpers\File::js('/js/modification.js');   
    @endphp   
    <script>

        $(function() {

            $("#shop-quich-order").on("submit", function() {

                $.ajax({
                    url: "/shop-quich-order",
                    type: "POST",
                    data: $("#shop-quich-order").serialize(),
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $("#shop-quich-order").replaceWith(data);
                    },
                });

                return false;
            });

            $(".grade-star").mouseenter(function() {
                let index = $(this).index();
                $(".grade-stars").find(".grade-star").each(function() {
                    if ($(this).index() <= index) {
                        $(this).addClass("hover");
                    } else {
                        $(this).removeClass("hover");
                    }
                });
            });

            $(".grade-stars").mouseleave(function() {
                $(".grade-star").removeClass("hover");
            });

            $(".grade-star").click(function() {
                let index = $(this).index();
                $("[name='grade']").val(index + 1);
                $(".grade-stars").find(".grade-star").each(function() {
                    if ($(this).index() <= index) {
                        $(this).addClass("fill");
                    } else {
                        $(this).removeClass("fill");
                    }
                });
            });

        });
        
    </script>
@endsection