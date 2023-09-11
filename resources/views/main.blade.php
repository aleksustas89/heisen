<!DOCTYPE html>
<html>
    <head>
        <title>@yield('seo_title')</title>
        <meta name="keywords" content="@yield('seo_keywords')">
        <meta name="description" content="@yield('seo_description')">

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/uikit@3.16.6/dist/css/uikit.min.css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
		<script src="//cdn.jsdelivr.net/npm/uikit@3.16.6/dist/js/uikit.min.js"></script>
		<script src="//cdn.jsdelivr.net/npm/uikit@3.16.6/dist/js/uikit-icons.min.js"></script>
		<link rel="preconnect" href="//fonts.googleapis.com">
		<link rel="preconnect" href="//fonts.gstatic.com" crossorigin>
		<link href="//fonts.googleapis.com/css2?family=Heebo:wght@400;700&family=Nunito+Sans&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
        <link href="/app/css/style.css" rel="stylesheet">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @livewireStyles

    </head>
    <body>
        <div uk-sticky="start: 200; animation: uk-animation-slide-top; sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky;">
            <nav class="uk-navbar-container">
                <div class="uk-container">
                    <div uk-navbar>
                        <div class="uk-navbar-left">
                            <a class="uk-navbar-toggle uk-hidden@m" uk-toggle="" href="#offcanvas-nav">
                                <span uk-navbar-toggle-icon></span>
                            </a>
                            <a class="uk-navbar-item uk-logo" href="/" aria-label="Back to Home"><img src="/img/logo.png"/></a>
                        </div>
                        <div class="uk-navbar-right">
                        
                            <!--
                            <div class="uk-navbar-item uk-visible@s">
                                <form class="uk-search uk-search-default">
                                    <a href="" class="uk-search-icon-flip" uk-search-icon></a>
                                    <input class="uk-search-input" type="search" placeholder="Search" aria-label="Search">
                                </form>
                            </div>-->

                            <div class="uk-inline tm-user">
                            @php
                                $user = Auth::user();
                            @endphp

                            @if ($user)
                                <button class="uk-button uk-button-link uk-visible@s" type="button">Личный кабинет <span uk-drop-parent-icon></span></button>
                                <div class="uk-card uk-card-body uk-card-default uk-card-small uk-width-expand" uk-drop="">
                                    <div class="uk-flex uk-flex-center">
                                        <ul class="uk-nav uk-dropdown-nav uk-text-small">
                                            <li><a href="/user/add-adv">Подать обьявление</a></li>
                                            <li><a href="/user/my-adv">Мои обьявления</a></li>
                                            <li><a href="/user/account/">Мои данные</a></li>
                                            <li>
                                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    Выход
                                                </a>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                    @csrf
                                                </form>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                            @else 
                                <a class="uk-button uk-button-link uk-visible@s" href="/user/login">Вход / Регистрация</a>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        
        <div id="offcanvas-nav" uk-offcanvas="overlay: true">
            <div class="uk-offcanvas-bar">
                <ul class="uk-nav uk-nav-default">
                    @if (isset($shop_groups) && count($shop_groups) > 0)
                        <li class="uk-parent">
                            <a>Каталог</a>
                            <ul class="uk-nav-sub">
                                @foreach ($shop_groups as $shop_group) 
                                    <li><a href="{{ $shop_group->getFullPath() }}">{{ $shop_group->name }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        
        <div class="tm-content">
        
            <div class="uk-container">
            
                @if (isset($breadcrumbs) && count($breadcrumbs) > 0)
                    <div class="uk-section-small uk-padding-remove-bottom">
                        <nav aria-label="Breadcrumb">
                            <ul class="uk-breadcrumb">
                                @foreach ($breadcrumbs as $breadcrumb)
                                    @if (!empty($breadcrumb["url"]))
                                        <li><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                                    @else 
                                        <li><span>{{ $breadcrumb["name"] }}</span></li>
                                    @endif
                                @endforeach
                            </ul>
                        </nav>
                    </div>
                @endif

                @yield('content')

                <div class="uk-section-default uk-section uk-section-small">
                    <div class="uk-container">                
                        <hr />
                        <div class="tm-grid-expand uk-grid-row-large uk-grid-margin-large uk-grid" uk-grid="">
                            <div class="uk-width-1-2@s uk-width-1-4@m uk-first-column">         
                                <h3 class="uk-h4 uk-margin">Адрес</h3>
                                <div class="uk-panel uk-margin">
                                    Украина, Одесса<br>
                                    Дерибасовская 21, офис 23<br> 
                                </div>
                            </div>

                            @if (isset($shop_groups) && count($shop_groups) > 0)
                                <div class="uk-width-1-2@s uk-width-1-4@m">
                                    <h3 class="uk-h4 uk-margin">Категории</h3>
                                    <ul class="uk-list">
                                        @foreach ($shop_groups as $shop_group) 
                                            <li><a href="{{ $shop_group->getFullPath() }}" class="el-link uk-link-text uk-margin-remove-last-child">{{ $shop_group->name }}</a></li>
                                        @endforeach
                                    </ul>  
                                </div>
                            @endif

                            @if (isset($bottom_menu_structures) && count($bottom_menu_structures) > 0)
                                <div class="uk-width-1-2@s uk-width-1-4@m">
                                    <h3 class="uk-h4 uk-margin">О компании</h3>
                                    <ul class="uk-list">
                                        @foreach ($bottom_menu_structures as $structure) 
                                            <li><a href="{{ $structure->path() }}" class="uk-link-text">{{ $structure->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="uk-section-default uk-section uk-section-small uk-padding-remove-top">
                    <div class="uk-container">                
                        <hr />
                        <div uk-grid>
                            <div class="uk-text-small">
                                © @php echo date("Y") @endphp
                                Dog Portal. 
                                All rights reserved. Powered by <a target="_blank" href="https://www.astra-site.com/">AstraSite</a>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @livewireScripts
	    
	</body>
</html>   