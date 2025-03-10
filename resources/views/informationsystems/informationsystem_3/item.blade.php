@extends('main')

@section('seo_title', $seo_title)
@section('seo_description', $seo_description)
@section('seo_keywords', $seo_keywords)

@section('content')

    {{-- @if ($images && count($images)) --}}
        
        <h1 class="uk-heading-small uk-margin">{{ $informationsystemItem->name }}</h1>

        {{-- <div class="uk-grid tm-grid-expand uk-child-width-1-1">

            <div class="uk-width-1-1@m">
                <div uk-slideshow="animation: push;ratio: auto; minHeight: 450;">

                    <div class="uk-position-relative uk-visible-toggle" tabindex="-1">

                        <ul class="uk-slideshow-items" uk-lightbox="animation: scale">

                            @foreach ($images as $image)
                                <li>
                                    <a href="{{ $image['image_large'] }}" data-caption="Caption 1">
                                        <div class="uk-height-large uk-background-contain" data-src="{{ $image['image_large'] }}" uk-img=""></div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
                        <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>
                    </div>

                    <div class="uk-margin" uk-slider>
                        <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1">
                            <ul class="uk-slider-items uk-child-width-1-2 uk-child-width-1-3@s uk-child-width-1-4@m uk-grid uk-grid-small">
    
                                @foreach ($images as $k => $image)

                                    <li uk-slideshow-item="{{ $k }}"><a href="#" data-src="{{ $image['image_small'] }}" uk-img=""></a></li>
                                @endforeach
                            </ul>

                            <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
                            <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next"></a>
                        </div>

                        <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
                    </div>
                </div>


            </div>
        </div> --}}
     
    {{-- @endif --}}

    <div class=" uk-margin static-page">
        <div class="uk-grid tm-grid-expand uk-child-width-1-1">
            <div class="uk-width-1-1@m">

                <div class="uk-panel uk-margin">

                    {!! $informationsystemItem->text !!}

                    @if (!is_null($informationsystemItem->informationsystemItemTags) && count($informationsystemItem->informationsystemItemTags) > 0)
                        <hr />

                        <p><span uk-icon="icon: tag" class="uk-margin-small-right"></span>Теги:</p>
                        <div class="shortcuts">
                            @foreach ($informationsystemItem->informationsystemItemTags as $informationsystemItemTag)
                                <a class="btn-shortcut" href="/{{ $informationsystemItem->Informationsystem->path }}/tags/{{ $informationsystemItemTag->Tag->path }}">#{{ $informationsystemItemTag->Tag->name }}</a>
                            @endforeach
                        </div>

                    @endif
                </div>

            </div>
        </div>
    </div>



@endsection

@section("css")

<style>
    .uk-slider-items a {
        height: 100px;
        width: 100%;
        display: inline-block;
        background-size: cover;
    }
</style>

@endsection
