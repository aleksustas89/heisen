<div class="uk-inline">
    <a href="javascript:void(0)" class="uk-icon uk-icon-button uk-margin-small-right" uk-icon="heart"></a>
    <div class="uk-card uk-card-body uk-card-default uk-card-small uk-text-center uk-text-small add-favorite-window" uk-drop="mode: click">
        <div>Для добавления товара в избранное требуется авторизоваться.</div>
        <form method="post" action="{{ route('login') }}">
            @csrf
            <div class="uk-margin uk-margin-small">
                <div class="uk-position-relative">
                    <a class="uk-form-icon" href="#" uk-icon="icon: user"></a>
                    <input class="uk-input uk-width-1-1" type="text" name="email" placeholder="E-mail" aria-label="Clickable icon">
                </div>
            </div>
            <div class="uk-margin uk-margin-small">
                <div class="uk-position-relative">
                    <a class="uk-form-icon" href="#" uk-icon="icon: lock"></a>
                    <input class="uk-input uk-width-1-1" type="password" name="password" placeholder="Пароль" aria-label="Clickable icon">
                </div>
            </div>
            <div class="uk-margin uk-margin-small">
                <button class="uk-button uk-button-default uk-button-small">Войти</button>
            </div>
        </form>
        <div><a href="{{ route('registerForm') }}">Зарегистрироваться</a></div>
    </div>
</div>