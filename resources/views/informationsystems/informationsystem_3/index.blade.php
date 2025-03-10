@extends('main')

@section('seo_title', $seo_title)
@section('seo_description', $seo_description)
@section('seo_keywords', $seo_keywords)

@section('content')

    <article class="uk-container uk-container-xlarge uk-margin">
        <div class="uk-grid-margin">
            <div class="uk-grid tm-grid-expand uk-child-width-1-1">
                <div class="uk-width-1-1@m">
                    <h1 class="uk-margin-medium">{{ $informationsystem->name }}</h1>
                    <div class="uk-margin uk-margin-remove-bottom">
                        <div class="uk-grid uk-child-width-1-1 uk-child-width-1-2@s uk-child-width-1-3@l uk-grid-medium uk-grid-match uk-grid-stack" uk-grid="">

                            @foreach ($informationsystemItems as $informationsystemItem)

                                <div>
                                    <div class="el-item uk-card uk-card-default uk-flex uk-flex-column">
                                        <div class="uk-card-media-top uk-position-relative">

                                            @foreach ($informationsystemItem->getImages(false) as $image)
                                                <div uk-img="" data-src="{{ $image['image_small'] }}" alt="" loading="lazy" class="uk-height-medium uk-background-norepeat uk-background-center-center"></div>
                                            @endforeach
                                            
                                            <a href="{{ $informationsystemItem->url }}" class="uk-position-cover"></a>
                                        </div>
                                        <div class="uk-flex-1 uk-flex uk-flex-column uk-card-body uk-margin-remove-first-child">

                                            <div class="el-meta uk-text-meta uk-margin-top">
                                                <time>
                                                    {{ date("d m Y", strtotime($informationsystemItem->created_at)) }}
                                                </time>
                                            </div>
                                            <h3 class="el-title uk-h3 uk-margin-small-top uk-margin-remove-bottom uk-flex-1">
                                                <a href="{{ $informationsystemItem->url }}" class="uk-link-reset">{{ $informationsystemItem->name }}</a>
                                            </h3>
                                            <div class="uk-margin-medium-top">
                                                <a href="{{ $informationsystemItem->url }}" class="el-link uk-link-muted">Подробнее</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @endforeach
                        </div>
                    </div>

                    @if ($informationsystemItems->hasPages())
                        <div class="pagination-auto uk-hidden">
                            {{ $informationsystemItems->links(('vendor.pagination.default')) }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </article>
@endsection