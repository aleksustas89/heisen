<div class="uk-width-1-3@s uk-first-column">
    <ul class="uk-nav-default uk-nav-divider uk-nav" uk-nav="">
        <li @class(['uk-active' => $page == "order" ? true : false])><a href="{{ route('clientOrders') }}">История покупок</a></li>
        <li @class(['uk-active' => $page == "account" ? true : false])><a href="{{ route('clientAccount') }}">Мои данные</a></li>
        <li @class(['uk-active' => $page == "favorite" ? true : false])><a href="{{ route('clientFavorites') }}">Избранное</a></li>
        <li><a href="javascript:void(0)" onclick="$('#logout-form').submit()">Выйти</a></li>
    </ul>
</div>