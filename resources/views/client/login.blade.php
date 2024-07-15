@extends('main')

@section('seo_title', 'Вход в личный кабинет')
@section('seo_description', '')
@section('seo_keywords', '')

@section('robots')
    {{ \App\Http\Controllers\SeoController::robots(['follow', 'index']) }}
@endsection

@section('content')

<div class="uk-section-default uk-section uk-section-small">

    @if (session('error'))
        <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
            <a class="uk-alert-close" uk-close></a>
            {{ session('error') }}
        </div>
    @endif
    
    <div class="uk-child-width-1-1@s uk-child-width-1-2@m uk-grid" uk-grid="">
        <div class="uk-first-column">

            <h1 class="uk-h2">Вход в личный кабинет</h1>

            <p>Войдите и получите доступ к скидкам, личному кабинету с историей заказов и возможности сохранять товары в избранное</p>

            <form  class="uk-form-stacked" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="uk-margin">
                    <label class="uk-form-label">Email *</label>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                    <div class="uk-inline uk-width-1-1">
                        <span class="uk-form-icon uk-icon" uk-icon="icon: user"><svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.1" cx="9.9" cy="6.4" r="4.4"></circle><path fill="none" stroke="#000" stroke-width="1.1" d="M1.5,19 C2.3,14.5 5.8,11.2 10,11.2 C14.2,11.2 17.7,14.6 18.5,19.2"></path></svg></span>
                        <input class="uk-input" type="text" aria-label="Not clickable icon" class="uk-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ __('Email Address') }}" autofocus>
                    </div>
                </div>

                <div class="uk-margin">
                    <label class="uk-form-label">Пароль *</label>

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                    <div class="uk-inline uk-width-1-1">
                        <span class="uk-form-icon uk-form-icon-flip uk-icon" uk-icon="icon: lock"><svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect fill="none" stroke="#000" height="10" width="13" y="8.5" x="3.5"></rect><path fill="none" stroke="#000" d="M6.5,8 L6.5,4.88 C6.5,3.01 8.07,1.5 10,1.5 C11.93,1.5 13.5,3.01 13.5,4.88 L13.5,8"></path></svg></span>
                        <input class="uk-input @error('password') is-invalid @enderror" type="password" aria-label="Not clickable icon" name="password" placeholder="{{ __('Password') }}">
                    </div>
                </div> 

                <button class="uk-button uk-button-primary" type="submit">Войти</button>
                <a class="uk-margin-left" href="{{ route("restoreForm") }}">Восстановить пароль</a>
            </form>
        
        </div>
	</div>
</div>

@endsection


        
