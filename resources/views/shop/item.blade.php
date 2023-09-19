@extends('main')

@section('seo_title', !empty($item->seo_title) ? $item->seo_title : $item->name)
@section('seo_description', $item->seo_description)
@section('seo_keywords', $item->seo_keywords)

@section('content')

    @php
        $images = $item->getImages();
        $client = Auth::guard('client')->user();
        $clientFavorites = !is_null($client) ? $client->getClientFavorites() : [];
    @endphp



	<div class="uk-section-xsmall uk-padding-remove-top">
        <div uk-grid>
            @if (count($images) > 0)
                <div class="uk-width-1-2@m">
                    <div id="uk-slideshow-items" uk-slideshow="animation: push;ratio: 1:1; minHeight: 300;">
                        <div class="uk-position-relative uk-visible-toggle" tabindex="-1">
                            <ul class="uk-slideshow-items" uk-lightbox="animation: scale">
                                @foreach ($images as $k => $image)
                                    <li id="uk-slide-{{$k}}"><a href="{{ $image['image_large'] }}"><img src="{{ $image['image_large'] }}" alt="" uk-cover></a></li>
                                @endforeach
                            </ul>
        
                            <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
                            <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>
                        </div>
                        <div class="uk-margin" uk-slider>
                            <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1">
                                <ul class="uk-slider-items uk-child-width-1-2 uk-child-width-1-3@s uk-child-width-1-4@m uk-grid uk-grid-small">        
                   
                                    @php
                                        $k = 0;
                                    @endphp
                                    @foreach ($images as $image)
                  
                                        <li uk-slideshow-item="{{ $k }}">
                                            <a style="background-position: center;" data-src="{{ $image['image_large'] }}" uk-img=""></a>
                                        </li>
                                        @php
                                            $k++;
                                        @endphp
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

            <div class="uk-width-expand@m">
                <div class="uk-margin">
                    <a class="el-content uk-link-text" href="{{ $item->ShopGroup->url() }}">{{ $item->ShopGroup->name }}</a>   
                </div>
                <h1 id="item-name" class="uk-h2 uk-margin-remove-vertical">{{ $item->name }}</h1>
                <div class="uk-h3 uk-margin uk-margin-top"> 
                    <span id="item-price">{{ App\Services\Helpers\Str::price($item->price) }}</span> {{ !is_null($item->ShopCurrency) ? $item->ShopCurrency->code : '' }}
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
                        @endphp
                        
                        <label class="uk-form-label" data-property-id="{{ $property->id }}" data-name="{{ $property->name }}">{{ $property->name }}</label>
                        <div class="uk-margin-small">


                            @php
                                $Shop_Item_List_Items = $property->shopItemList->listItems->whereIn("id", $aModValues);
                            @endphp

                            @if ($property->destination == 1 && $property->type == 4 && !is_null($property->shopItemList))
                                <ul class="uk-grid uk-grid-xsmall tm-color-switcher" uk-grid="">


                                    @foreach ($Shop_Item_List_Items as $Shop_Item_List_Item)
                                        <li><a onclick="Modification.choose($(this))" data-id="{{ $Shop_Item_List_Item->id }}" uk-tooltip="{{ $Shop_Item_List_Item->value }}" class="uk-border-circle" data-src="{{ $Shop_Item_List_Item->description }}" uk-img=""></a></li>
                                    @endforeach

                                </ul>
                            @elseif($property->destination == 0 && $property->type == 4 && !is_null($property->shopItemList))
                                <ul class="uk-grid uk-grid-xsmall tm-other-switcher" uk-grid="">
                                    @foreach ($Shop_Item_List_Items as $Shop_Item_List_Item)
                                        <li><a onclick="Modification.choose($(this))" data-id="{{ $Shop_Item_List_Item->id }}" uk-tooltip="{{ $Shop_Item_List_Item->value }}">{{ $Shop_Item_List_Item->value }}</a></li>
                                    @endforeach
                                </ul>
                            @endif

                            <input type="hidden" name="property_{{ $property->id }}" />

                        </div>

                    @endforeach

                </form>
                    
                <div class="uk-margin-medium" uk-margin>
                    <div uk-form-custom="target: true" class="uk-visible@s">
                        <input type="number" class="uk-input uk-form-width-xsmall" name="quantity" value="1" title="Qty" size="4" min="1" max="" step="1" placeholder="" inputmode="numeric" autocomplete="off">
                    </div>
                    <button type="button" id="cart_add" data-route="{{ route('cartAdd') }}" data-uk-tooltip="Выберите {{ implode('и', $choose_properties_tooltip) }}" uk-tooltip="Выберите {{ implode('и', $choose_properties_tooltip) }}" disabled class="uk-button uk-buttom-small uk-button-primary buy-btn">КУПИТЬ <span uk-icon="icon: cart"></span></button>
                    <button type="button" id="fast_order" data-uk-tooltip="Выберите {{ implode('и', $choose_properties_tooltip) }}" uk-tooltip="Выберите {{ implode('и', $choose_properties_tooltip) }}" disabled class="uk-button uk-buttom-small uk-button-primary buy-btn">КУПИТЬ В ОДИН КЛИК</button>
        
                    @if (is_null($client))
                        @include('shop.window-login')
                    @else
                        @php
                        $active = in_array($item->id, $clientFavorites) ? true : false;
                        @endphp
                        <a onclick="Favorite.add($(this), {{ $item->id }}, '{{ route('addFavorite') }}')" @class(["add-to-favorite-link", "uk-icon", "uk-icon-button", "tm-icon", "active" => $active]) uk-icon="heart"></a>
                    @endif
                </div>

                <hr />
                <ul uk-accordion="collapsible: false" class="uk-list uk-list-divider">
                    <li>
                        <a class="uk-accordion-title">Описание</a>
                        <div class="uk-accordion-content">
                            {!! $item->description !!}
                        </div>
                    </li>
                    <li>
                        <a class="uk-accordion-title">ХАРАКТЕРИСТИКИ</a>
                        <div class="uk-accordion-content">
                       
                                <ul class="uk-list">
                                    @foreach ($item->getProperties() as $property)
                                        @if ($property["show_in_item"] == 1 && count($property["property_values"]) > 0)
                                            <li>
                                                <b>{{ $property["property_name"] }}:</b>
                                                @foreach ($property["property_values"] as $value)
                                                    {{ $value }}
                                                @endforeach
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                        </div>
                    </li>    
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
                        <a class="uk-accordion-title">ВИГОДЫ ПРИ РАБОТЕ С BARTBAG</a>
                        <div class="uk-accordion-content">
                            <p>Мы разрабатываем собственные коллекции, опираясь на модные тенденции, и совершенствуем классические модели. Аксессуары от наших мастеров в процессе эксплуатации приобретают винтажный шарм, не теряя связь со временем.</p>
                            <p>Мы предлагаем аксессуары, в которых учтено всё, они очень долго служат хозяевам, сохраняют презентабельный вид, а в процессе носки приобретают неповторимую уникальность и изысканность. Такие вещи подчёркивают вкус владельца и помогают создать собственный стиль.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

@endsection

@section("css")
    <style>
        .uk-slider-items a{
            height: 100px;
            width: 100%;
            display: inline-block;
            background-size: cover;
        }
    </style>
@endsection

@section("js")
    <script src="/js/modification.js"></script>
    <script src="/js/cart.js"></script>
@endsection