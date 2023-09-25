@extends('main')

@section('seo_title', "Поиск по сайту")

@section('content')

    <div class="uk-section-xsmall tm-tovar">
        <div uk-grid="" class="uk-grid">
        
     
            <div class="uk-width-expand@m">
            
                <div class="uk-h3 uk-text-bold">Поиск</div>
            
                <div class="uk-child-width-1-4@s uk-child-width-1-2 uk-grid-small uk-grid" uk-grid="">

                    <form class="uk-search uk-search-default search-form">
                        <a href="" class="uk-search-icon-flip uk-icon uk-search-icon" uk-search-icon=""><svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.1" cx="9" cy="9" r="7"></circle><path fill="none" stroke="#000" stroke-width="1.1" d="M14,14 L18,18 L14,14 Z"></path></svg></a>
                        <input name="q" class="uk-search-input uk-border-rounded" value="{{ $q }}" type="search" placeholder="Search" aria-label="Search">
                    </form>

                    @php
                        $client = Auth::guard('client')->user();

                    @endphp

                    @foreach ($SearchWords as $SearchWord)

                        @include('shop.list-item', [
                            'item' => $SearchWord->SearchPage->ShopItem,
                            'client' => $client,
                            'clientFavorites' => !is_null($client) ? $client->getClientFavorites() : [],
                        ])

                    @endforeach   

           
                </div>
            
                <!--пагенация-->
                @if (count($SearchWords) > 0)

                @php

                    $links = [
                        'q' => $q
                    ];

                @endphp

                {{ $SearchWords->appends($links)->links(('vendor.pagination.default')) }}
                @endif
                <!--пагенация-->
            </div>
            <!--сам каталог-->
        </div>
    </div>

@endsection

@section("css")
<style>
.search-form {width: 100%!important;}
.search-results {margin: 20px;}
</style>
@endsection
