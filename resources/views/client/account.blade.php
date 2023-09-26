@extends('main')

@section('seo_title', 'Кабинет клиента')
@section('seo_description', '')
@section('seo_keywords', '')

@section('content')

<div class="uk-section-default uk-section uk-section-small">

    <div class="uk-child-width-1-1@s uk-child-width-1-2@m uk-grid" uk-grid="">
       
        @include('client.menu', ["page" => "account"])

        <div class="uk-width-expand@s">

            <h2>Добро пожаловать {{ $client->name }}!</h2>

            @if (session('success'))
                <div class="uk-alert-success uk-alert uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="uk-alert-danger uk-alert uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    {{ session('error') }}
                </div>
            @endif

            <div>

                <form method="POST" class="uk-form-stacked" action="{{ route('clientUpdate') }}">
                    @csrf

                    <div class="uk-margin">
                        <label for="name" class="uk-form-label">Имя</label>
                        <div class="uk-inline uk-width-1-1">
                            <input id="name" type="text" class="uk-input " name="name" value="{{ $client->name }}" required="" autocomplete="name" autofocus="">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label for="surname" class="uk-form-label">Фамилия</label>
                        <div class="uk-inline uk-width-1-1">
                            <input id="surname" type="text" class="uk-input " name="surname" value="{{ $client->surname }}" required="" autocomplete="surname">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label for="email" class="uk-form-label">E-mail</label>
                        <div class="uk-inline uk-width-1-1">
                            <input id="email" type="email" class="uk-input " name="email" value="{{ $client->email }}" required="" autocomplete="email">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label for="phone" class="uk-form-label">Телефон</label>
                        <div class="uk-inline uk-width-1-1">
                            <input id="phone" type="text" class="uk-input " name="phone" value="{{ $client->phone }}" required="" autocomplete="phone">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label for="password" class="uk-form-label">Пароль</label>
                        <div class="uk-inline uk-width-1-1">
                            <input id="password" type="password" class="uk-input " name="password" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label for="password-confirm" class="uk-form-label">Подтвердите пароль</label>
                        <div class="uk-inline uk-width-1-1">
                            <input id="password-confirm" type="password" class="uk-input" name="password_confirmation" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="uk-button uk-button-primary">
                                Изменить данные
                            </button>
                        </div>
                    </div>

                </form>    
            
            </div>
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

