@extends('skeleton')

@section('skeleton_content')

    @php
    $client = Auth::guard('client')->user();

    if (\App\Models\ClientFavorite::$Type == 0) {
        $countFavorites = !is_null($client) ? count($client->getClientFavorites()) : 0;
    } else if (\App\Models\ClientFavorite::$Type == 1) {
        $countFavorites = \App\Http\Controllers\Auth\ClientController::getCookieFavoritesCount();
    }

    @endphp

    <div uk-sticky="start: 200; animation: uk-animation-slide-top; sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky;">

        <nav class="uk-navbar-container tm-head-container uk-visible@m">
            <div class="tm-head-small">
                <div class="uk-container uk-container-xlarge">
                    <div uk-navbar>
                        <div class="uk-navbar-left">
                        
                            <div class="uk-navbar-item"><a class="uk-text-small" href="tel:{{ env('APP_PHONE') }}"><span uk-icon="icon: phone"></span> {{ env('APP_PHONE') }}</a></div>

                            @if (isset($TopMenuStructures) && count($TopMenuStructures) > 0)
                                <ul class="uk-navbar-nav uk-visible@s">
                                    @foreach ($TopMenuStructures as $aStructure) 
                                        <li class="uk-flex">

                                            @if (count($aStructure["sub"]) > 0)
                                                <a href="{{ !empty($aStructure["redirect"]) ? $aStructure["redirect"] : $aStructure["url"] }}" class="uk-button uk-button-link uk-padding-remove-left uk-padding-remove-right" type="button">
                                                    {{ $aStructure["name"] }} 
                                                    <span uk-drop-parent-icon></span>
                                                </a>
                                                <div class="uk-card uk-card-body uk-card-default uk-card-small uk-width-small uk-drop" uk-drop="">
                                                    <div class="uk-flex uk-flex-center">
                                                        <ul class="uk-nav uk-dropdown-nav uk-text-small">
                                                            @foreach ($aStructure["sub"] as $sStructure)
                                                                <li><a href="{{ !empty($sStructure["redirect"]) ? $sStructure["redirect"] : $sStructure["url"] }}">{{ $sStructure["name"] }}</a></li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>

                                            @else

                                                <a href="{{ !empty($aStructure["redirect"]) ? $aStructure["redirect"] : $aStructure["url"] }}">{{ $aStructure["name"] }}</a>

                                            @endif
                                        </li>
                                    @endforeach
                                    <li><a href="{{ route("comments") }}">Отзывы</a></li>

                                    @if (\App\Http\Controllers\ShopItemDiscountController::countItemsWithDiscounts() > 0)
                                        <li><a href="{{ route("showItemWithDiscounts") }}">Скидки</a></li>
                                    @endif

                                </ul>
                            @endif
                            
                        </div>
                        <div class="uk-navbar-right">

                            @if (is_null($client))

                                <div class="uk-text-small">
                                    <a href="{{ route("loginForm") }}">Кабинет</a> / <a href="{{ route("registerForm") }}">Регистрация</a>
                                </div>

                            @else
                                <div class=" uk-navbar-item">
                                    <div class="uk-inline tm-user">
                                        <button class="uk-button uk-button-link uk-padding-remove-left uk-padding-remove-right" type="button">Личный кабинет <span uk-drop-parent-icon></span></button>
                                        <div class="uk-card uk-card-body uk-card-default uk-card-small uk-width-small" uk-drop="">
                                            <div class="uk-flex uk-flex-center">
                                                <ul class="uk-nav uk-dropdown-nav uk-text-small">
                                                    <li><a href="{{ route('clientOrders') }}">История покупок</a></li>
                                                    <li><a href="{{ route('clientAccount') }}">Мои данные</a></li>
                                                    <li><a href="{{ route('clientFavorites') }}">Избранное</a></li>
                                                    <li><a href="javascript:void(0)" onclick="$('#logout-form').submit()">Выйти</a></li>
                                                </ul>
                                                <form id="logout-form" action="{{ route('clientLogout') }}" method="POST" class="d-none">
                                                    @csrf                      
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if (\App\Models\ClientFavorite::$Type == 0)
                                    <div class="uk-navbar-item">
                                        <a href="{{ route('clientFavorites') }}"><span uk-icon="icon: heart"></span> (<span class="favorites-count">{{ $countFavorites }}</span>)</a>
                                    </div>
                                @endif

                            @endif

                            @if (\App\Models\ClientFavorite::$Type == 1)
                                <div class="uk-navbar-item">
                                    <a href="{{ route('cookieFavorites') }}"><span uk-icon="icon: heart"></span> (<span class="favorites-count">{{ $countFavorites }}</span>)</a>
                                </div>
                            @endif

                            <div class="uk-navbar-item little-cart">

                                @include('shop.cart-items', ["littleCart" => 1])
        
                            </div>
                        </div>
                    </div>  
                </div>        
            </div>	
            <div class="uk-container uk-container-xlarge">
                <div uk-navbar>
                    <div class="uk-navbar-left">
                        <a class="uk-navbar-item uk-logo" href="/" aria-label="Back to Home">HEISEN</a>
                    </div>
                    <div class="uk-navbar-center">
                        <ul class="uk-navbar-nav">
                            @foreach ($ShopGroups as $ShopGroup) 
                                <li>
                                    <a href="{{ $ShopGroup["url"] }}">{{ $ShopGroup["name"] }}@if(count($ShopGroup["sub"]) > 0)<span uk-navbar-parent-icon></span>@endif</a>
                                    @if (count($ShopGroup["sub"]) > 0)
                                        <div class="uk-navbar-dropdown">
                                            <ul class="uk-nav uk-navbar-dropdown-nav">
                                                @foreach ($ShopGroup["sub"] as $sShopGroup)
                                                    <li><a href="{{ $sShopGroup['url'] }}">{{ $sShopGroup['name'] }}</a></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        
                    </div>
                    <div class="uk-navbar-right">
                        <div class="uk-navbar-item uk-visible@s">
                            <form class="uk-search uk-search-default" action="{{ route("search") }}">
                                <button class="uk-search-icon-flip" uk-search-icon></button>
                                <input name="q" class="uk-search-input uk-border-rounded search-autocomplete uk-visible@l" type="search" placeholder="Поиск" aria-label="Search">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        
        <nav class="uk-navbar-container tm-head-container-mob uk-hidden@m">
            <div class="uk-container uk-container-expand">
                <div uk-navbar>
                    <div class="uk-navbar-left"> 
                        <a class="uk-navbar-item uk-logo" href="/" aria-label="Back to Home">HEISEN</a>
                    </div>
                    <div class="uk-navbar-right">

                        <div id="offcanvas-nav" uk-offcanvas="overlay: true">
                            <div class="uk-offcanvas-bar uk-padding-remove-bottom"> 
                                <div uk-height-viewport="offset-top: true; offset-bottom: true" class="uk-padding-small uk-padding-remove-top  uk-padding-remove-left  uk-padding-remove-right">
                                    <button class="uk-offcanvas-close" type="button" uk-close></button>
                                    
                                    <ul class="uk-nav uk-nav-default">
                                        <li class="uk-nav-header">Каталог</li>

                                        @foreach ($ShopGroups as $ShopGroup) 
                                            <li class="uk-parent">
                                                <a href="{{ $ShopGroup["url"] }}">{{ $ShopGroup["name"] }}</a>
                                                @if (count($ShopGroup["sub"]) > 0)
                                                    <ul class="uk-nav-sub">
                                                        @foreach ($ShopGroup["sub"] as $sShopGroup)
                                                            <li><a href="{{ $sShopGroup['url'] }}">{{ $sShopGroup['name'] }}</a></li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach          
                                    </ul>
                                    <hr />
                                    <ul class="uk-nav uk-nav-default"> 
                                        <li><a class="uk-display-inline" href="{{ route("loginForm") }}"><span class="uk-margin-small-right" uk-icon="icon: user"></span>Кабинет</a> / <a class="uk-display-inline" href="{{ route("registerForm") }}">Регистрация</a></li>
                                    </ul>
                                </div>  

                                <div class=" uk-overlay uk-overlay-default uk-text-center off-footer-tm">
                        
                                    <ul class="uk-nav uk-nav-default"> 
                                        @foreach ($TopMenuStructures as $aStructure) 
                                            <li>
                                                <a href="{{ !empty($aStructure["redirect"]) ? $aStructure["redirect"] : $aStructure["url"] }}">{{ $aStructure["name"] }}</a>

                                                @if (count($aStructure["sub"]) > 0)
                                                    <ul class="uk-nav uk-dropdown-nav uk-text-small">
                                                        @foreach ($aStructure["sub"] as $sStructure)
                                                            <li><a href="{{ !empty($sStructure["redirect"]) ? $sStructure["redirect"] : $sStructure["url"] }}">{{ $sStructure["name"] }}</a></li>
                                                        @endforeach
                                                    </ul>
                                                @endif

                                            </li>
                                        @endforeach
                        
                                        <li class="uk-nav-divider"></li>     
                                        <li><a href="tel:{{ env('APP_PHONE') }}"><span class="uk-margin-small-right" uk-icon="icon: phone"></span> {{ env('APP_PHONE') }}</a></li>
                                    
                                        <li class="uk-nav-divider"></li>
                                        <li class="uk-flex uk-flex-middle">
                                            <div>
                                                <span class="uk-margin-small-right" uk-icon="icon: clock"></span>
                                            </div> 
                                            <div>
                                                <div><b>понедельник-пятница: </b>{{ env('SCHEDULE1') }}</div>
                                                <div><b>суббота: </b>{{ env('SCHEDULE2') }}</div>
                                                <div><b>воскресенье: </b>{{ env('SCHEDULE3') }}</div>
                                            </div>
                                        </li>
                                    </ul>
                        
                                </div>
                            </div>
                        </div>

                        <div>
                            <a class="uk-navbar-toggle" href="#" uk-search-icon></a>
                            <div class="uk-navbar-dropdown uk-width-1-1" uk-drop="mode: click; cls-drop: uk-navbar-dropdown; boundary: !.uk-navbar; flip: false">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-expand">
                                        <form class="uk-search uk-search-navbar uk-width-1-1" action="{{ route("search") }}">
                                            <input name="q" class="uk-search-input search-autocomplete" type="search" placeholder="Поиск" aria-label="Search" autofocus>
                                        </form>
                                        
                                    </div>
                                    <div class="uk-width-auto">
                                        <a class="uk-drop-close" href="#" uk-close></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (!is_null($client))
                            <div class="uk-navbar-item">
                                <a href="{{ route('clientFavorites') }}"><span uk-icon="icon: heart"></span> (<span class="favorites-count">{{ $countFavorites }}</span>)</a>
                            </div>
                        @endif

                        @if (\App\Models\ClientFavorite::$Type == 1)
                            <div class="uk-navbar-item">
                                <a href="{{ route('cookieFavorites') }}"><span uk-icon="icon: heart"></span> (<span class="favorites-count">{{ $countFavorites }}</span>)</a>
                            </div>
                        @endif
                            
                        <div class="uk-navbar-item little-cart">
                            @include('shop.cart-items', ["littleCart" => 1])    
                        </div>
                        <div class="uk-navbar-item">
                            <a class="uk-navbar-toggle" uk-navbar-toggle-icon href="#offcanvas-nav" uk-toggle></a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div class="uk-container uk-container-xlarge">

        <div class="tm-content" uk-height-viewport="offset-top: true;offset-bottom: true">
            <div class="uk-container uk-container-xlarge">
                
                @if (isset($breadcrumbs) && count($breadcrumbs) > 0)
                    <div class="uk-section-small uk-padding-remove-bottom">
                        <nav aria-label="Breadcrumb">
                            <ul class="uk-breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
                                @foreach ($breadcrumbs as $key => $breadcrumb)
                                    @if (!empty($breadcrumb["url"]))
                                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                            <a itemprop="item" href="{{ $breadcrumb["url"] }}">
                                                <span itemprop="name">{{ $breadcrumb["name"] }}</span>
                                            </a>
                                            <meta itemprop="position" content="{{ $key + 1 }}" />
                                        </li>
                                    @else 
                                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                            <span itemprop="item">
                                                <span itemprop="name">{{ $breadcrumb["name"] }}</span>
                                            </span>
                                            <meta itemprop="position" content="{{ $key + 1 }}" />
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </nav>
                    </div>
                @endif

                @yield('content')

            </div>            
        </div>

        <div>
            <!--Футер1-->
            <div class="uk-section-default uk-section uk-section-small">
                <div class="uk-container uk-container-xlarge">                
                    <hr />       
                    <div class="tm-grid-expand uk-grid-row-large uk-grid-margin-large uk-grid" uk-grid="">
                        <div class="uk-width-1-2@s uk-width-1-4@m uk-first-column">  
                            <div class="uk-h4 uk-margin">Время работы</div>
                            <div class="uk-panel uk-margin">
                                <div><b>понедельник-пятница: </b>{{ env('SCHEDULE1') }}</div>
                                <div><b>суббота: </b>{{ env('SCHEDULE2') }}</div>
                                <div><b>воскресенье: </b>{{ env('SCHEDULE3') }}</div>
                            </div>
                  
                        </div>

                        <div class="uk-width-1-2@s uk-width-1-4@m">

                            @if (isset($ShopGroups) && count($ShopGroups) > 0)
                                <div class="uk-h4 uk-margin">Каталог</div>
                                <ul class="uk-list">
                                    @foreach ($ShopGroups as $ShopGroup) 
                                        <li><a class="el-link uk-link-text uk-margin-remove-last-child" href="{{ $ShopGroup["url"] }}">{{ $ShopGroup["name"] }}</a></li>
                                    @endforeach
                                </ul>
                            @endif

                        </div>

                        <div class="uk-width-1-2@s uk-width-1-4@m">
                            <div class="uk-h4 uk-margin">Документы</div>
 
                            <ul class="uk-list">
                                @php
                                    $Structure = \App\Models\Structure::find(28);
                                @endphp
                                @if (!is_null($Structure) && $Structure->active == 1)
                                    <li><a href="{{ $Structure->url() }}" class="uk-link-text">{{ $Structure->name }}</a></li>
                                @endif

                                @php
                                $Structure = \App\Models\Structure::find(29);
                                @endphp
                                @if (!is_null($Structure) && $Structure->active == 1)
                                    <li><a href="{{ $Structure->url() }}" class="uk-link-text">{{ $Structure->name }}</a></li>
                                @endif
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
            <!--Футер1-->
        
            <!--Футер-->
            <div class="uk-section-default uk-section-xsmall uk-padding-remove-top">
                <div class="uk-container uk-container-xlarge">                
                    <hr />
                    <div uk-grid>
                        <div class="uk-text-small">© {{ date("Y") }} Tech Space. All rights reserved.</div>
                        
                    </div>
                </div>
            </div>
            <!--Футер-->
        </div>
    </div>

    <div style="position:fixed;bottom:20px;right:20px;z-index: 999;">
        <div class="uk-inline uk-flex uk-flex-center tm-dropmess">
            <button class="messbtn uk-flex uk-flex-center uk-flex-middle"> <span class="uk-icon iconmess" style="background-image: url(/images/message-solid.svg);"></span></button>
            
            <div uk-dropdown="pos: bottom-center;">
                <ul class="uk-nav uk-dropdown-nav tm-icon-nav">
                    <li><a target="_blank" href="https://t.me/heisen_spb"><span class="uk-icon uk-icon-image" style="background-image: url(/images/telegram-brands-solid.svg);"></span></a></li>
                    <li><a target="_blank" href="https://wa.me/+79111564465"><span class="uk-icon uk-icon-image" style="background-image: url(/images/square-whatsapp-brands-solid.svg);"></span></a></li>
                    <li><a target="_blank" href="https://vk.com/heisenru"><span class="uk-icon uk-icon-image" style="background-image: url(/images/vk-brands-solid.svg);"></span></a></li>
                    <li><a href="javascript:void(0)" uk-toggle="target: #request-call"><span class="uk-icon uk-icon-image" style="background-image: url(/images/message-regular.svg);"></span></a></li>
                </ul>
            </div>
        </div>
    </div>    


    <div id="request-call" uk-modal="" class="uk-modal" tabindex="-1">
        <div class="uk-modal-dialog uk-modal-body" role="dialog" aria-modal="true">
            <div class="uk-h2 uk-modal-title">Заявка на обратный звонок</div>
            
            <form id="request-call-form" type="POST">
                @csrf
                <p>Заполните форму и наши менеджеры свяжутся с Вами в ближайшее время</p>

                <div class="uk-margin">
                    <label class="uk-form-label" for="form-stacked-text">Ваше имя</label>
                    <div class="uk-form-controls">
                        <input required="" class="uk-input" name="name" type="text" placeholder="Ваше имя">
                    </div>
                </div>

                <div class="uk-margin">
                    <label class="uk-form-label" for="form-stacked-text">Ваш телефон</label>
                    <div class="uk-form-controls">
                        <input required="" class="uk-input" name="phone" type="text" placeholder="Ваш телефон">
                    </div>
                </div>

                <p class="uk-text-right">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">Отменить</button>
                    <button class="uk-button uk-button-primary" type="submit">Заказать</button>
                </p>
            </form>

        </div>
    </div>
@endsection



