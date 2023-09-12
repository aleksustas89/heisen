@extends('main')

@section('seo_title', $structure->seo_title ?? $structure->name)
@section('seo_description', $structure->seo_description)
@section('seo_keywords', $structure->seo_keywords)

@section('content')

    <!--слайдер-->
    <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slideshow="animation: push;ratio: 7:3">

        <ul class="uk-slideshow-items">
            <li>
                <div class="uk-position-cover" uk-slideshow-parallax="scale: 1.2,1.2,1">
                    <img src="/images/slider/1.jpg" alt="" uk-cover>
                </div>
                <div class="uk-position-cover" uk-slideshow-parallax="opacity: 0,0,0.2; backgroundColor: #000,#000"></div>
                
                <div class="uk-position-center-right">
                    <div class="uk-padding-large uk-width-xlarge uk-margin-remove-first-child">
                        <h2 class="uk-h1 uk-margin-top uk-margin-remove-bottom">Собственная мастерская</h2>
                        <div class="el-content uk-panel uk-margin-top">
                            Это возможность предлагать не только продукцию нашего дизайна, но и отшивать модели по эскизам клиента, и вносить коррективы по желанию заказчика в имеющуюся модель.
                        </div>
                    </div>
                </div>
                
            </li>
            <li>
                <div class="uk-position-cover" uk-slideshow-parallax="scale: 1.2,1.2,1">
                    <img src="/images/slider/2.jpg" alt="" uk-cover>
                </div>
                <div class="uk-position-cover" uk-slideshow-parallax="opacity: 0,0,0.2; backgroundColor: #000,#000"></div>
                <div class="uk-position-center-right">
                    <div class="uk-padding-large uk-width-xlarge uk-margin-remove-first-child">

                        <h2 class="uk-h1 uk-margin-top uk-margin-remove-bottom">Лаконичный дизайн и высокое качество</h2>
                        <div class="el-content uk-panel uk-margin-top">Для создания каждой вещи мы отбираем лучшие материалы: только добротную кожу и фурнитуру.</div>
                    </div>
                </div>
            </li>
            <li>
                <div class="uk-position-cover" uk-slideshow-parallax="scale: 1.2,1.2,1">
                    <img src="/images/slider/3.jpg" alt="" uk-cover>
                </div>
                <div class="uk-position-cover" uk-slideshow-parallax="opacity: 0,0,0.2; backgroundColor: #000,#000"></div>
                <div class="uk-position-center-right">
                    <div class="uk-padding-large uk-width-xlarge uk-margin-remove-first-child">
                        <h2 class="uk-h1 uk-margin-top uk-margin-remove-bottom">Неповторимая уникальность и изысканность</h2>
                        <div class="el-content uk-panel uk-margin-top">
                            Мы предлагаем аксессуары, в которых учтено всё, они очень долго служат хозяевам, сохраняют презентабельный вид, 
                            а в процессе носки приобретают неповторимую уникальность и изысканность. 
                        </div>
                    </div>
                </div>
            </li>
        </ul>

        <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
        <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>

    </div>	
    <!--слайдер-->



    <h2 class="uk-h1 uk-margin-xlarge uk-margin-remove-bottom uk-text-center">Насладись Перфекцианизмом Наших Изделий</h2>
	<div class="uk-margin uk-width-xlarge uk-margin-auto uk-text-center">
        Наши менеджеры всегда на связи и с радостью проконсультируют Вас по всем вопросам
    </div>

    @php

    $first = isset($groups[0]) ? $groups[0] : false; 
    $second = isset($groups[1]) ? $groups[1] : false; 
    $third = isset($groups[2]) ? $groups[2] : false; 
    $fourth = isset($groups[3]) ? $groups[3] : false; 
    $fifth = isset($groups[4]) ? $groups[4] : false; 
    $sixth = isset($groups[5]) ? $groups[5] : false; 

    @endphp

	<!--блок с популярными товарами-->
	<div class="uk-child-width-1-2@s uk-text-center uk-grid-small home-groups" uk-grid>
        <div>
            <div class="uk-child-width-1-2@s uk-text-center uk-grid-small home-group-small" uk-grid>
                @if ($first)
                    <div class="home-group-item">
                        <a href="/{{ $first->getFullPath() }}" class="uk-inline-block">
                            <img src="{{ $first->dir() }}{{ $first->image_large }}" />
                            <div class="uk-position-bottom-center uk-padding">
                                <h3 class="uk-h5 uk-margin-top uk-margin-remove-bottom">{{ $first->name }}</h3>
                            </div>
                        </a>
                    </div>
                @endif
                @if ($second)
                    <div class="home-group-item">
                        <a href="/{{ $second->getFullPath() }}" class="uk-inline-block">
                            <img src="{{ $second->dir() }}{{ $second->image_large }}">
                            <div class="uk-position-bottom-center uk-padding">
                                <h3 class="uk-h5 uk-margin-top uk-margin-remove-bottom">{{ $second->name }}</h3>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
            
            @if ($third)
                <div class="uk-child-width-1-1 uk-text-center uk-grid-small home-group-large home-group-item" uk-grid>
                    <a href="/{{ $third->getFullPath() }}" class="uk-inline-block">
                        <img src="{{ $third->dir() }}{{ $third->image_large }}">
                        <div class="uk-position-bottom-center uk-padding">
                            <h3 class="uk-h5 uk-margin-top uk-margin-remove-bottom">{{ $third->name }}</h3>
                        </div>
                    </a>
                </div>
            @endif
        </div>
        <div>
            @if ($fourth)
                <div class="uk-child-width-1-1 uk-text-center uk-grid-small home-group-large home-group-item" uk-grid>
                    <a href="/{{ $fourth->getFullPath() }}" class="uk-inline-block">
                        <img src="{{ $fourth->dir() }}{{ $fourth->image_large }}">
                        <div class="uk-position-bottom-center uk-padding">
                            <h3 class="uk-h5 uk-margin-top uk-margin-remove-bottom">{{ $fourth->name }}</h3>
                        </div>
                    </a>
                </div> 
            @endif    
            <div class="uk-child-width-1-2@s uk-text-center uk-grid-small" uk-grid>
                @if ($fifth)
                    <div class="home-group-small home-group-item">
                        <a href="/{{ $fifth->getFullPath() }}" class="uk-inline-block">
                            <img src="{{ $fifth->dir() }}{{ $fifth->image_large }}">
                            <div class="uk-position-bottom-center uk-padding">
                                <h3 class="uk-h5 uk-margin-top uk-margin-remove-bottom">{{ $fifth->name }}</h3>
                            </div>
                        </a>
                    </div>
                @endif   
                @if ($sixth) 
                    <div class="home-group-small home-group-item">
                        <a href="/{{ $sixth->getFullPath() }}" class="uk-inline-block">
                            <img src="{{ $sixth->dir() }}{{ $sixth->image_large }}">
                            <div class="uk-position-bottom-center uk-padding">
                                <h3 class="uk-h5 uk-margin-top uk-margin-remove-bottom">{{ $sixth->name }}</h3>
                            </div>
                        </a>
                    </div>
                @endif 
            </div>
        </div>
	</div>
	<!--блок с популярными товарами-->

    @if (count($newItems) > 0)

        <h2 class="uk-h1 uk-margin-small uk-text-center uk-margin-xlarge uk-margin-bottom">Новинки</h2>

        <div class="uk-child-width-1-4@s uk-child-width-1-2 uk-grid-small uk-grid" uk-grid="">

            @foreach ($newItems as $item)
                @include('shop.list-item', ['item' => $item])
            @endforeach   

        </div>
    @endif

@endsection


@section("css")

<link href="/css/pages/home.css" rel="stylesheet">

@endsection