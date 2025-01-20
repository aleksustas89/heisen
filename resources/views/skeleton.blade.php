<!DOCTYPE html>
<html prefix="og: https://ogp.me/ns/article#">
    <head>
        <title>@yield('seo_title')</title>
        <meta name="keywords" content="@yield('seo_keywords')">
        <meta name="description" content="@yield('seo_description')">

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

        <meta property="og:title" content="@yield('seo_title')" />
        <meta property="og:description" content="@yield('seo_description')" />
        <meta property="og:image" content="{{ request()->getScheme() }}://{{ request()->getHttpHost() }}/images/heisen.jpg" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <link rel="shortcut icon" href="{{ request()->getScheme() }}://{{ request()->getHttpHost() }}/images/favicon.ico">
		
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

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-1R59VLXCQ5"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-1R59VLXCQ5');
        </script>


        <!-- Yandex.Metrika counter -->
        <script type="text/javascript" >
            (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
            (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
        
            ym(99567323, "init", {
                clickmap:true,
                trackLinks:true,
                accurateTrackBounce:true,
                webvisor:true
            });
        </script>
        <noscript><div><img src="https://mc.yandex.ru/watch/99567323" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
        <!-- /Yandex.Metrika counter -->

        @yield('canonical')

        @yield('robots')

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
            App\Services\Helpers\File::js('/js/cart.js');
        @endphp

        @yield('css')
        @yield('js')

        @php
            App\Services\Helpers\File::css('/css/media.css');
        @endphp

        <!-- Top.Mail.Ru counter -->
        <script type="text/javascript">
            var _tmr = window._tmr || (window._tmr = []);
            _tmr.push({id: "3578536", type: "pageView", start: (new Date()).getTime()});
            (function (d, w, id) {
            if (d.getElementById(id)) return;
            var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
            ts.src = "https://top-fwz1.mail.ru/js/code.js";
            var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
            if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
            })(document, window, "tmr-code");
        </script>
        <noscript><div><img src="https://top-fwz1.mail.ru/counter?id=3578536;js=na" style="position:absolute;left:-9999px;" alt="Top.Mail.Ru" /></div></noscript>
        <!-- /Top.Mail.Ru counter -->
	</body>
</html>    


