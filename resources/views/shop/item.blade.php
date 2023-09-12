@extends('main')

@section('seo_title', !empty($item->seo_title) ? $item->seo_title : $item->name)
@section('seo_description', $item->seo_description)
@section('seo_keywords', $item->seo_keywords)

@section('content')

    @php
        $images = $item->getImages();
    @endphp

	<div class="uk-section-xsmall uk-padding-remove-top">
        <div uk-grid>
            @if (count($images) > 0)
                <div class="uk-width-1-2@m">
                    <div uk-slideshow="animation: push;ratio: 1:1; minHeight: 300;">
                        <div class="uk-position-relative uk-visible-toggle" tabindex="-1">
                            <ul class="uk-slideshow-items" uk-lightbox="animation: scale">
                                @foreach ($images as $k => $image)
                                    <li><a href="{{ $image['image_large'] }}"><img src="{{ $image['image_large'] }}" alt="" uk-cover></a></li>
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
                    <a class="el-content uk-link-text" href="/{{ $item->ShopGroup->getFullPath() }}">{{ $item->ShopGroup->name }}</a>   
                </div>
                <h1 class="uk-h2 uk-margin-remove-vertical uk-width-xlarge">{{ $item->name }}</h1>
                <div class="uk-h3 uk-margin uk-margin-top"> 
                    {{ number_format($item->price, 0, ',', ' ') }} {{ !is_null($item->ShopCurrency) ? $item->ShopCurrency->code : '' }}
                </div>
    
                <form>    
                    <label class="uk-form-label">Цвет: коньячный</label>
                    <div class="uk-margin-small">
                        <ul class="uk-grid uk-grid-xsmall tm-color-switcher" uk-grid="">
                            <li><a href="/" uk-tooltip="коньячный" class="uk-border-circle tm-active" data-src="images/djeyn/dj-10.jpg" uk-img=""></a></li>
                            <li><a href="/" uk-tooltip="серый" class="uk-border-circle" data-src="images/djeyn/dj-11.jpg" uk-img=""></a></li>
                            <li><a href="/" uk-tooltip="розовый" class="uk-border-circle" data-src="images/djeyn/dj-12.jpg" uk-img=""></a></li>
                            <li><a href="/" uk-tooltip="кофейный" class="uk-border-circle" data-src="images/djeyn/dj-13.jpg" uk-img=""></a></li>
                        </ul>
                    </div>
                    <label class="uk-form-label">Цвет фурнитуры: антик</label>
                    <div class="uk-margin-small">
                        <ul class="uk-grid uk-grid-xsmall tm-other-switcher" uk-grid="">
                            <li><a href="/" uk-tooltip="антик" class="tm-active">Антик</a></li>
                            <li><a href="/" uk-tooltip="антик" class="">Серебро</a></li>
                        </ul>
                    </div>
                    <!--
                    <div class="uk-margin-medium" uk-margin>
                        <div uk-form-custom="target: true" class="uk-visible@s">
                            <input type="number" id="" class="uk-input uk-form-width-xsmall" name="quantity" value="1" title="Qty" size="4" min="1" max="" step="1" placeholder="" inputmode="numeric" autocomplete="off">
                        </div>
                        <button class="uk-button uk-buttom-small uk-button-primary">КУПИТЬ <span uk-icon="icon: cart"></span></button>
                        <button class="uk-button uk-buttom-small uk-button-primary">КУПИТЬ В ОДИН КЛИК</button>
                        <a href="" class="uk-icon-button uk-margin-small-right" uk-icon="heart"></a>
                    </div>
                -->
                </form>
        
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
    .uk-slider-items a{height: 100px;
        width: 100%;
        display: inline-block;
        background-size: cover;
    }
</style>
@endsection