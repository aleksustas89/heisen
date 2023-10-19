<!DOCTYPE html>
<html>
    <head>
        <title>@yield('seo_title')</title>
        <meta name="keywords" content="@yield('seo_keywords')">
        <meta name="description" content="@yield('seo_description')">

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		
		<!-- UIkit CSS -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.16.6/dist/css/uikit.min.css" />

		<!-- UIkit JS -->
		<script src="https://cdn.jsdelivr.net/npm/uikit@3.16.6/dist/js/uikit.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/uikit@3.16.6/dist/js/uikit-icons.min.js"></script>
		
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;1,300;1,400;1,500&display=swap" rel="stylesheet">
		
        @php
        App\Services\Helpers\File::css('/css/style.css');
        @endphp

        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
    
        <div class="tm-content" uk-height-viewport="offset-top: true;offset-bottom: true">
            @yield('skeleton_content')
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>       

        @php
            App\Services\Helpers\File::js('/js/jquery.autocomplete.min.js');
            App\Services\Helpers\File::js('/js/main.js');
        @endphp

        @yield('css')
        @yield('js')

        @php
            App\Services\Helpers\File::css('/css/media.css');
        @endphp
	</body>
</html>    


