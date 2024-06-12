@extends('main')

@section('seo_title', 'Восстановить')
@section('seo_description', '')
@section('seo_keywords', '')

@section('robots')
    {{ \App\Http\Controllers\SeoController::robots(['follow', 'index']) }}
@endsection

@section('content')

<div class="uk-section-default uk-section uk-section-small">

    <div class="uk-child-width-1-1@s uk-child-width-1-2@m uk-grid" uk-grid="">
       
        <div>
        
            <h2>Восстановить пароль</h2>

            @if (session('error'))
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @if (session('success'))
                <div class="uk-alert-success uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            
            <form method="POST" action="{{ route("restore") }}" class="uk-form-stacked" >
                @csrf

                <div class="uk-margin">
                    <label for="email" class="uk-form-label">E-mail</label>

                    <div class="uk-inline uk-width-1-1">
                        <input id="email" type="email" class="uk-input" name="email" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                </div>

                <div class="uk-margin">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="uk-button uk-button-primary">
                            Восстановить
                        </button>
                    </div>
                </div>

            </form>    
        
        </div>
	</div>
</div>

@endsection