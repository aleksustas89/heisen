@extends('skeleton')

@section('skeleton_content')

    <div uk-sticky="start: 200; animation: uk-animation-slide-top; sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky;">

        <nav class="uk-navbar-container tm-head-container uk-visible@m">
            <div class="tm-head-small">
                <div class="uk-container uk-container-xlarge">
                    <div uk-navbar>
                        <div class="uk-navbar-left">
                        
                            <div class="uk-navbar-item"><a class="uk-text-small" href="#"><span uk-icon="icon: phone"></span> +38(073) 004-72-95</a></div>

                            @if (isset($TopMenuStructures) && count($TopMenuStructures) > 0)
                                <ul class="uk-navbar-nav uk-visible@s">
                                    @foreach ($TopMenuStructures as $Structure) 
                                        <li><a href="/{{ $Structure->path() }}">{{ $Structure->name }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                            
                        </div>
                        <div class="uk-navbar-right">
                        
                            @php
                                $client = Auth::guard('client')->user();
                            @endphp

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
                                    <a href="{{ route('clientFavorites') }}"><span uk-icon="icon: heart"></span> (<span id="favorites-count">{{ count($client->getClientFavorites()) }}</span>)</a>
                                </div>

                            @endif

                            <div class="uk-navbar-item" id="cart">

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
                        
                        @if (isset($ShopGroups) && count($ShopGroups) > 0)
                            <ul class="uk-navbar-nav">
                                @foreach ($ShopGroups as $ShopGroup) 
                                    <li>

                                        @php
                                        $ChildCount = $ShopGroup->getChildCount();
                                        @endphp

                                        <a href="{{ $ShopGroup->url() }}">{{ $ShopGroup->name }}@if($ChildCount["groupsCount"] > 0)<span uk-navbar-parent-icon></span>@endif</a>

                                        @if ($ChildCount["groupsCount"] > 0)
                                            <div class="uk-navbar-dropdown">
                                                <ul class="uk-nav uk-navbar-dropdown-nav">
                                                    @foreach (\App\Models\ShopGroup::where("active", 1)->where("parent_id", $ShopGroup->id)->get() as $ShopGroupLevel2)
                                                        <li><a href="{{ $ShopGroupLevel2->url() }}">{{ $ShopGroupLevel2->name }}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </li>
                        @endif
                        
                    </div>
                    <div class="uk-navbar-right">
                        <div class="uk-margin-small-right uk-navbar-item uk-visible@s">
                            <form class="uk-search uk-search-default" action="{{ route("search") }}">
                                <button class="uk-search-icon-flip" uk-search-icon></button>
                                <input name="q" class="uk-search-input uk-border-rounded search-autocomplete" type="search" placeholder="Search" aria-label="Search">
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
                                <div uk-height-viewport="offset-top: true; offset-bottom: true">
                                    <button class="uk-offcanvas-close" type="button" uk-close></button>
                                    <ul class="uk-nav uk-nav-default">
                                        <li class="uk-nav-header">Каталог</li>
                                        <li class="uk-parent">
                                            <a href="#">Сумки</a>
                                            <ul class="uk-nav-sub">
                                                <li><a href="#">Сумки женские</a></li>
                                                <li><a href="#">Сумки мужские</a></li>
                                                <li><a href="#">Сумки на пояс</a></li>
                                                <li><a href="#">Дорожные сумки</a></li>
                                            </ul>
                                        </li>
                                        <li class="uk-parent">
                                            <a href="#">Портмоне и кошельки</a>
                                            <ul class="uk-nav-sub">
                                                <li><a href="#">Мужские кошельки</a></li>
                                                <li><a href="#">Женские кошельки</a></li>
                                                <li><a href="#">Картхолдеры, визитницы</a></li>
                                            </ul>
                                        </li>            
                                    </ul>
                                    <hr />
                                    <ul class="uk-nav uk-nav-default"> 
                                        <li><a href="#"><span class="uk-margin-small-right" uk-icon="icon: user"></span> Войти / Зарегистрироваться</a></li>
                                    </ul>
                                </div>  

                                <div class=" uk-overlay uk-overlay-default uk-text-center off-footer-tm">
                        
                                    <ul class="uk-nav uk-nav-default"> 
                                        <li>
                                            <a href="/ru/o-nas/pay/" title="Условия оплаты и доставки">Условия оплаты и доставки</a>
                                        </li>
                                        <li>
                                            <a href="/ru/o-nas/rules/" title="Условия возврата и обмена">Условия возврата и обмена</a>
                                        </li>
                                        <li>
                                            <a href="/ru/o-nas/garantii/" title="Гарантии">Гарантии</a>
                                        </li>
                                    
                                        <li>
                                            <a href="/ru/o-nas/contacts/" title="Контакты">Контакты</a>
                                        </li>
                                    
                                        <li>
                                            <a href="/ru/o-nas/politika/" title="Политика конфиденциальности">Политика конфиденциальности</a>
                                        </li>
                        
                                        <li class="uk-nav-divider"></li>     
                                        <li><a href="#"><span class="uk-margin-small-right" uk-icon="icon: phone"></span> +38(073) 004-72-95</a></li>
                                    
                                        <li class="uk-nav-divider"></li>
                                        <li><a href="#"><span class="uk-margin-small-right" uk-icon="icon: clock"></span> время работы <br /> пн-пт 9-20, сб 10-16<br /> вс- выходной</a></li>
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
                                            <input name="q" class="uk-search-input" type="search" placeholder="Search" aria-label="Search" autofocus>
                                        </form>
                                    </div>
                                    <div class="uk-width-auto">
                                        <a class="uk-drop-close" href="#" uk-close></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-navbar-item"><a href="#"><span uk-icon="icon: heart"></span> (2)</a></div>
                            
                        <div class="uk-navbar-item">
                            <a href="#"><span uk-icon="icon: bag"></span> (2)</a>
                            
                            <div class="uk-card uk-card-default uk-card-small uk-card-body" uk-drop="">
                                <!--small card-->
                                <div class="uk-text-center uk-margin-small-bottom uk-flex uk-flex-top uk-flex-center uk-flex-middle uk-position-relative">
                                    Корзина
                                    <span class="uk-margin-small-right uk-position-center-right">1 товар</span>
                                </div>
                                <div class="tm-tovar-card-small uk-inline">
                                    <a class="uk-margin-small-right uk-icon uk-position-top-right" uk-icon="icon:close; ratio:0.7"></a>
                                
                                    <div class="uk-grid-small uk-flex-middle" uk-grid>
                                        <div class="uk-width-auto">
                                            <img class="uk-tovar-avatar" src="/images/uig1.jpeg" width="80" height="80" alt="">
                                        </div>
                                        <div class="uk-width-expand">
                                            <h4 class="uk-margin-remove tm-name-card-small"><a class="uk-link-reset" href="#">Apple iPhone 11 Pro 256GB Space Gray</a></h4>
                                            <ul class="uk-subnav uk-subnav-divider uk-margin-remove-top">
                                                <li><a href="#">Size: 61"</a></li>
                                                <li><a href="#">Color: Red</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="uk-grid-small" uk-grid>
                                        <div class="uk-width-auto">Сумма заказа:</div>
                                        <div class="uk-width-expand uk-text-right">350,20€</div>
                                    </div>
                                </div>
                                <hr />
                                <div class="uk-text-center">
                                    <button class="uk-button uk-button-primary">Оформить заказ</button>
                                    <button class="uk-button uk-width-1-1 uk-button-link">Перейти в корзину</button>
                                </div>
                                <!--Small card-->
                            </div>        
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

        <div id="offcanvas-nav" uk-offcanvas="overlay: true">
            <div class="uk-offcanvas-bar">
                <button class="uk-offcanvas-close" type="button" uk-close></button>
                <ul class="uk-nav uk-nav-default">
                    <li class="uk-nav-header">Каталог</li>
                    <li class="uk-parent">
                        <a href="#">Сумки</a>
                        <ul class="uk-nav-sub">
                            <li><a href="#">Сумки женские</a></li>
                            <li><a href="#">Сумки мужские</a></li>
                            <li><a href="#">Сумки на пояс</a></li>
                            <li><a href="#">Дорожные сумки</a></li>
                        </ul>
                    </li>
                    <li class="uk-parent">
                        <a href="#">Портмоне и кошельки</a>
                        <ul class="uk-nav-sub">
                            <li><a href="#">Мужские кошельки</a></li>
                            <li><a href="#">Женские кошельки</a></li>
                            <li><a href="#">Картхолдеры, визитницы</a></li>
                        </ul>
                    </li>            
                    
                </ul>
                <hr />
                <ul class="uk-nav uk-nav-default"> 
                <li><a href="#"><span class="uk-margin-small-right" uk-icon="icon: user"></span> Войти / Зарегистрироваться</a></li>
                </ul>
                
                <div class="uk-position-bottom uk-overlay uk-overlay-default uk-text-center off-footer-tm">
                
                <ul class="uk-nav uk-nav-default">
                
                <li>
                <a href="/ru/o-nas/pay/" title="Условия оплаты и доставки">Условия оплаты и доставки</a>
                </li>
        
                <li>
                <a href="/ru/o-nas/rules/" title="Условия возврата и обмена">Условия возврата и обмена</a>
                </li>
                <li>
                <a href="/ru/o-nas/garantii/" title="Гарантии">Гарантии</a>
                </li>

                <li>
                <a href="/ru/o-nas/contacts/" title="Контакты">Контакты</a>
                </li>

                <li>
                <a href="/ru/o-nas/politika/" title="Политика конфиденциальности">Политика конфиденциальности</a>
                </li>

                <li class="uk-nav-divider"></li>     
                <li><a href="#"><span class="uk-margin-small-right" uk-icon="icon: phone"></span> +38(073) 004-72-95</a></li>
                    
                <li class="uk-nav-divider"></li>
                <li><a href="#"><span class="uk-margin-small-right" uk-icon="icon: clock"></span> время работы <br /> пн-пт 9-20, сб 10-16<br /> вс- выходной</a></li>
                </ul>
                
                </div>

            </div>
        </div>	
	
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
                            <div class="uk-panel uk-margin"><b>пн-пт:</b> 9-20,<br><b>сб:</b> 10-16,<br><b>вс:</b> выходной</div>
                  
                        </div>

                        <div class="uk-width-1-2@s uk-width-1-4@m">

                            @if (isset($ShopGroups) && count($ShopGroups) > 0)
                                <h3 class="uk-h4 uk-margin">Каталог</h3>
                                <ul class="uk-list">
                                    @foreach ($ShopGroups as $ShopGroup) 
                                        <li><a class="el-link uk-link-text uk-margin-remove-last-child" href="{{ $ShopGroup->url() }}">{{ $ShopGroup->name }}</a></li>
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
                                    <li><a href="/{{ $Structure->path() }}" class="uk-link-text">{{ $Structure->name }}</a></li>
                                @endif

                                @php
                                $Structure = \App\Models\Structure::find(29);
                                @endphp
                                @if (!is_null($Structure) && $Structure->active == 1)
                                    <li><a href="/{{ $Structure->path() }}" class="uk-link-text">{{ $Structure->name }}</a></li>
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



