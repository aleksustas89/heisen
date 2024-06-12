@extends('main')

@section('seo_title', 'Избранные товары клиента')
@section('seo_description', '')
@section('seo_keywords', '')

@section('robots')
    {{ \App\Http\Controllers\SeoController::robots(['nofollow', 'noindex']) }}
@endsection

@section('content')

<div class="uk-section-default uk-section uk-section-small">

    <div class="uk-child-width-1-1@s uk-child-width-1-1@m uk-grid" uk-grid="">
       
        @include('client.menu', ["page" => "favorite"])

        <div class="uk-width-expand@s">
        
            <h2>Ваши избранные товары</h2>

            @if ($shopItems)

                <div class="uk-child-width-1-3@s uk-child-width-1-5@m uk-child-width-1-2 uk-grid-small uk-grid" uk-grid="">
                    @foreach ($shopItems as $item)
                        @include('shop.list-item', [
                            'item' => $item,
                        ])
                    @endforeach  
                </div>

                {{ $shopItems->links(('vendor.pagination.default')) }}

            @else
                <p>У вас нет товаров, добавленных в избранное.</p>
            @endif
        
        </div>
	</div>
</div>

@endsection