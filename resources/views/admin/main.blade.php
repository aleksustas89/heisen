<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Админ-панель - @yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <link rel="shortcut icon" href="/assets/images/favicon.ico">
        <link href="{{ asset('/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('/assets/plugins/animate/animate.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('/assets/css/admin.css') }}" rel="stylesheet" type="text/css" />
        <script src="{{ asset('/assets/plugins/jquery/jquery.min.js') }}"></script>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="nofollow, noindex" />
    </head>

    <body id="body" class="dark-sidebar">
        <!-- leftbar-menu -->
        <div class="left-sidebar">
            <!-- LOGO -->
            <div class="brand">
                <a href="{{ route("homeAdmin") }}" class="logo">
                    <span>
                        <img src="/assets/images/logo-sm.png" alt="logo-small" class="logo-sm">
                    </span>
                    <span class="developer-text logo-text d-xxl-inline d-lg-none">
                        Powerful
                    </span>
                </a>
            </div>
            <div class="sidebar-user-pro media border-end">                    
                <div class="position-relative mx-auto">
                    @php
                    $ico = '';
                    $eName = explode(" ", Auth::user()->name);
                    if (count($eName) > 1) {
                        $ico = mb_substr($eName[0], 0, 1) . mb_substr($eName[1], 0, 1);
                    } else {
                        $ico = mb_substr(Auth::user()->name, 0, 2);
                    }
                    @endphp
                    <span class="thumb-sm justify-content-center d-flex align-items-center bg-soft-warning rounded me-2">{{ $ico }}</span>
                </div>
                <div class="media-body ms-2 user-detail align-self-center">
                    <h5 class="font-14 m-0 fw-bold">{{ Auth::user()->name }} </h5>  
                    <p class="opacity-50 mb-0">{{ Auth::user()->email }}</p>  
                    <div>
                        <a href="javascript:void(0)" class="user-detail-logout" onclick="confirmDelete($('#logout-form'), 'Выполнить выход из админ-панели?')">
                            <i class="ti ti-power menu-icon"></i>
                            <span>Выход</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>        
                </div>                    
            </div>
    
            <!--end logo-->
            <div class="menu-content h-100" data-simplebar>
                <div class="menu-body navbar-vertical">
                    <div class="collapse navbar-collapse tab-content" id="sidebarCollapse">
    
                        <ul class="navbar-nav tab-pane active" id="Main" role="tabpanel">
                                
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('homeAdmin') }}">
                                    <i class="ti ti-stack menu-icon"></i>
                                    <span>Главная</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('structure.index') }}" class="nav-link">
                                    <i class="ti ti-notes menu-icon"></i>
                                    <span>Структура</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('user.index') }}" class="nav-link">
                                    <i class="ti ti-user menu-icon"></i>
                                    <span>Сотрудники</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('client.index') }}" class="nav-link">
                                    <i class="ti ti-users menu-icon"></i>
                                    <span>Клиенты</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('shop.index') }}" class="nav-link">
                                    <i class="ti ti-shopping-cart menu-icon"></i>
                                    <span>Интернет-магазин</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('adminSearch') }}" class="nav-link">
                                    <i class="ti ti-search menu-icon"></i>
                                    <span>Поиск</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('comment.index') }}" class="nav-link">
                                    <i class="ti ti-messages menu-icon"></i>
                                    <span>Комментарии</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('adminSitemap') }}" class="nav-link">
                                    <i class="ti ti-messages menu-icon"></i>
                                    <span>Sitemap</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('trash.index') }}" class="nav-link">
                                    <i class="ti ti-trash menu-icon"></i>
                                    <span>Корзина</span>
                                </a>
                            </li>
                            
                        </ul>
                    </div>
                </div>
            </div>    
        </div>
        <!-- end left-sidenav-->
        <!-- end leftbar-menu-->

        <!-- Top Bar Start -->
        <div class="topbar">            
            <!-- Navbar -->
            <nav class="navbar-custom" id="navbar-custom">    
 

                <ul class="list-unstyled topbar-nav mb-0">                        
                    <li>
                        <button class="nav-link button-menu-mobile nav-icon" id="togglemenu">
                            <i class="ti ti-menu-2"></i>
                        </button>
                    </li>                     
                </ul>
            </nav>
            <!-- end navbar-->
        </div>
        <!-- Top Bar End -->

        <div class="page-wrapper">

            <!-- Page Content-->
            <div class="page-content-tab">

                <div class="container-fluid">

                    @yield('breadcrumbs')

                    @yield('content')

                </div><!-- container -->

                <!-- Footer Start -->
                <footer class="footer text-center text-sm-start">
                    &copy; <script>
                        
                    </script> Powerful Admin-Panel, developed on Laravel <span class="text-muted d-none d-sm-inline-block float-end">Designed with <i class="mdi mdi-heart text-danger"></i> by Alex Potuzhniy</span>
                </footer>
                <!-- end Footer -->                
                <!--end footer-->
            </div>
            <!-- end page content -->
        </div>
        <!-- end page-wrapper -->

        <!-- Javascript  -->   
        <!-- App js -->
        <script src="{{ asset('/assets/js/app.js') }}"></script>
        <script src="{{ asset('/assets/plugins/wysiwyg/jquery.tinymce.min.js') }}"></script>

        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

        @php
            App\Services\Helpers\File::js('/assets/js/main.js');
            App\Services\Helpers\File::js('/assets/js/toggle.js');
            App\Services\Helpers\File::js('/assets/js/editable.js');
        @endphp


        <script src="/assets/plugins/sweet-alert2/sweetalert2.min.js"></script>

        <script>
            var toggle_route = '{{ route("adminToggle") }}',
                editable_route = '{{ route("adminEditable") }}';
            
        </script>

        @yield('css')
        @yield('js')
        
    </body>
    <!--end body-->
</html>