@extends('skeleton')

@section('skeleton_content')

    @php
    $client = Auth::guard('client')->user();
    $countFavorites = !is_null($client) ? count($client->getClientFavorites()) : 0;
    @endphp

    <div uk-sticky="start: 200; animation: uk-animation-slide-top; sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky;">

        <nav class="uk-navbar-container tm-head-container uk-visible@m">
            <div class="tm-head-small">
                <div class="uk-container uk-container-xlarge">
                    <div uk-navbar>
                        <div class="uk-navbar-left">
                        
                            <div class="uk-navbar-item"><a class="uk-text-small" href="#"><span uk-icon="icon: phone"></span> 8 996 784 05 75</a></div>

                            @if (isset($TopMenuStructures) && count($TopMenuStructures) > 0)
                                <ul class="uk-navbar-nav uk-visible@s">
                                    @foreach ($TopMenuStructures as $Structure) 
                                        <li class="uk-flex">
                       
                                            @php
                                                $Children = \App\Models\Structure::where("parent_id", $Structure->id)->get();
                                            @endphp

                                            @if (count($Children) > 0)
                                                <a href="{{ $Structure->url() }}" class="uk-button uk-button-link uk-padding-remove-left uk-padding-remove-right" type="button">
                                                    {{ $Structure->name }} 
                                                    <span uk-drop-parent-icon></span>
                                                </a>
                                                <div class="uk-card uk-card-body uk-card-default uk-card-small uk-width-small uk-drop" uk-drop="">
                                                    <div class="uk-flex uk-flex-center">
                                                        <ul class="uk-nav uk-dropdown-nav uk-text-small">
                                                            @foreach ($Children as $cStructure)
                                                                <li><a href="{{ $cStructure->url() }}">{{ $cStructure->name }}</a></li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>

                                            @else

                                                <a href="{{ $Structure->url() }}">{{ $Structure->name }}</a>

                                            @endif
                                        </li>
                                    @endforeach
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
                                <div class="uk-navbar-item">
                                    <a href="{{ route('clientFavorites') }}"><span uk-icon="icon: heart"></span> (<span class="favorites-count">{{ $countFavorites }}</span>)</a>
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
                        <a class="uk-navbar-item uk-logo" href="/" aria-label="Back to Home"><img data-src="/images/logo.png" uk-img="" /></a>
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
                                <input name="q" class="uk-search-input uk-border-rounded search-autocomplete uk-visible@l" type="search" placeholder="Search" aria-label="Search">
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
                        <a class="uk-navbar-item uk-logo" href="/" aria-label="Back to Home"><img data-src="/images/logo.png" style="height:50px;" uk-img="" /></a>
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
                                        @foreach ($TopMenuStructures as $Structure) 
                                            <li><a href="{{ $Structure->url() }}">{{ $Structure->name }}</a></li>
                                        @endforeach
                        
                                        <li class="uk-nav-divider"></li>     
                                        <li><a href="tel:89967840575"><span class="uk-margin-small-right" uk-icon="icon: phone"></span> 8 996 784 05 75</a></li>
                                    
                                        <li class="uk-nav-divider"></li>
                                        <li class="uk-flex uk-flex-middle">
                                            <div>
                                                <span class="uk-margin-small-right" uk-icon="icon: clock"></span>
                                            </div> 
                                            <div>
                                                <div>
                                                    <b>пн-пт:</b> 9-20,<br>
                                                </div>
                                                <div><b>сб:</b> 10-16,<br></div>
                                                <div><b>вс:</b> выходной</div>
                                            </div>
                                        </li>
                                    </ul>
                        
                                </div>
                            </div>
                        </div>

                        <div>
                            <a class="uk-navbar-toggle" href="#" uk-search-icon></a>
                            <div class="uk-navbar-dropdown" uk-drop="mode: click; cls-drop: uk-navbar-dropdown; boundary: !.uk-navbar; flip: false">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-expand">
                                        <form class="uk-search uk-search-navbar uk-width-1-1" action="{{ route("search") }}">
                                            <input name="q" class="uk-search-input search-autocomplete" type="search" placeholder="Search" aria-label="Search" autofocus>
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

            </div>            
        </div>

        <div>
            <!--Футер1-->
            <div class="uk-section-default uk-section uk-section-small">
                <div class="uk-container uk-container-xlarge">                
                    <hr />       
                    <div class="tm-grid-expand uk-grid-row-large uk-grid-margin-large uk-grid" uk-grid="">
                        <div class="uk-width-1-2@s uk-width-1-4@m uk-first-column">  
                            <h3 class="uk-h4 uk-margin">Время работы</h3>
                            <div class="uk-panel uk-margin">
                                <b>пн-пт:</b> 9-20,<br><b>сб:</b> 10-16,<br><b>вс:</b> выходной
                            </div>
                  
                        </div>

                        <div class="uk-width-1-2@s uk-width-1-4@m">

                            @if (isset($ShopGroups) && count($ShopGroups) > 0)
                                <h3 class="uk-h4 uk-margin">Каталог</h3>
                                <ul class="uk-list">
                                    @foreach ($ShopGroups as $ShopGroup) 
                                        <li><a class="el-link uk-link-text uk-margin-remove-last-child" href="{{ $ShopGroup["url"] }}">{{ $ShopGroup["name"] }}</a></li>
                                    @endforeach
                                </ul>
                            @endif

                        </div>

                        <div class="uk-width-1-2@s uk-width-1-4@m">
                            <h3 class="uk-h4 uk-margin">Документы</h3>
 
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
                        <div class="uk-text-small">© {{ date("Y") }} Tech Space. All rights reserved. Powered by <a target="_blank" href="https://www.astra-site.com/">AstraSite</a>.</div>
                        <div class="uk-flex uk-flex-right uk-width-expand@s">
                            <span class="footer-insta">
                                <a target="_blank" href="https://www.instagram.com/bartbag_kyiv/" uk-icon="icon: instagram">
                                    Follow us
                                </a>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--Футер-->
        </div>
    </div>
@endsection



