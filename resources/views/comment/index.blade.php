@extends('main')

@section('seo_title', "Отзывы")
@section('seo_description', "Отзывы")
@section('seo_keywords', "")

@section('content')

    <div class="uk-section-small uk-padding-remove-bottom">
        <h1>Отзывы</h1>
        @foreach ($Comments as $Comment)
                    
            @include('comment.comment', [
                'Comment' => $Comment,
            ])
            
        @endforeach   

        <div class="pagination">
            {{ $Comments->links(('vendor.pagination.default')) }}
        </div>
    </div>

@endsection

@section("css")
    <link href="/assets/css/colors.css" rel="stylesheet" type="text/css">
@endsection