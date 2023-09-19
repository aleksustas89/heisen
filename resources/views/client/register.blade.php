@extends('main')

@section('seo_title', 'Регистрация клиента')
@section('seo_description', '')
@section('seo_keywords', '')

@section('content')

<div class="uk-section-default uk-section uk-section-small">

    <div class="uk-child-width-1-1@s uk-child-width-1-2@m uk-grid" uk-grid="">
       
        <div>
        
            <h2>Регистрация</h2>

            <p>Зарегистрируйтесь и получайте доступ к скидкам, личному кабинету с историей заказов и возможности сохранять товары в избранное</p>

            @error('name')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Заполните поле Имя</p>
                </div>
            @enderror

            @error('surname')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Заполните поле Фамилия</p>
                </div>
            @enderror

            @error('email')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Заполните поле E-mail в формате heisen@gmail.com</p>
                </div>
            @enderror

            @error('phone')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Заполните поле Телефон</p>
                </div>
            @enderror

            @error('password')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Заполните поле Пароль</p>
                </div>
            @enderror

            @error('password_confirmation')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Заполните поле Подтверждение Пароля</p>
                </div>
            @enderror
            
            <form method="POST" action="{{ route('register') }}" class="uk-form-stacked" >
                @csrf

                <div class="uk-margin">
                    <label class="uk-form-label">Имя</label>

                    <div class="uk-inline uk-width-1-1">
                        <input id="name" type="text" class="uk-input" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="uk-margin">
                    <label class="uk-form-label">Фамилия</label>

                    <div class="uk-inline uk-width-1-1">
                        <input  type="text" class="uk-input" name="surname" value="{{ old('surname') }}" required autocomplete="surname">
                    </div>
                </div>

                <div class="uk-margin">
                    <label for="email" class="uk-form-label">E-mail</label>

                    <div class="uk-inline uk-width-1-1">
                        <input id="email" type="email" class="uk-input" name="email" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                </div>

                <div class="uk-margin">
                    <label for="email" class="uk-form-label">Телефон</label>

                    <div class="uk-inline uk-width-1-1">
                        <input type="text" class="uk-input" name="phone" value="{{ old('phone') }}" required autocomplete="phone">
                    </div>
                </div>

                <div class="uk-margin">
                    <label for="password" class="uk-form-label">Пароль</label>

                    <div class="uk-inline uk-width-1-1">
                        <input id="password" type="password" class="uk-input" name="password" required autocomplete="new-password">
                    </div>
                </div>

                <div class="uk-margin">
                    <label for="password-confirm" class="uk-form-label">Подтвердите пароль</label>

                    <div class="uk-inline uk-width-1-1">
                        <input id="password-confirm" type="password" class="uk-input" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>

                <div class="uk-margin">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="uk-button uk-button-primary">
                            Зарегистрироваться
                        </button>
                    </div>
                </div>

            </form>    
        
        </div>
	</div>
</div>

@endsection

@section("js")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>

    <script>
        $('[name="phone"]').mask("+7 (999) 999-9999", {autoclear: false});
    </script>

@endsection

        
