@extends('main')

@section('seo_title'){{ \App\Http\Controllers\SeoController::showItemTitle($shop, $item, $Modification) }}@endsection
@section('seo_description'){{ \App\Http\Controllers\SeoController::showItemDescription($shop, $item, $Modification) }}@endsection
@section('seo_keywords', $item->seo_keywords)

@section('canonical')

    @if (!is_null($defaultModification))
        <link rel="canonical" href="https://{{ request()->getHost() }}{{ $defaultModification->url }}" />
    @elseif ($item->canonical > 0 && !is_null($Canonical = \App\Models\ShopItem::find($item->canonical)))
        <link rel="canonical" href="https://{{ request()->getHost() }}{{ $Canonical->url }}" />
    @else
        <link rel="canonical" href="https://{{ request()->getHost() }}{{ $item->url }}" />
    @endif
@endsection

@section('robots')
    {{ \App\Http\Controllers\SeoController::robots(['follow', 'index']) }}
@endsection

@section('content')

    @include("shop/item-content")

@endsection

@section("css")
    @php
        App\Services\Helpers\File::css('/css/colors.css');
    @endphp
    <style>
        .uk-slider-items a{
            height: 100px;
            width: 100%;
            display: inline-block;
            background-size: cover;
        }
        .grade-star.hover polygon, .grade-star.fill polygon {
            fill: #c39c5c;
            stroke: #cdb58d !important;
        }

        @media (max-width: 480px) {
            .grade-star {
                font-size: 10px;
            }
            .grade-star svg{ 
                width: 30px;
            }
        }
        .shortcuts {
            gap: 20px;
            display: flex;
            flex-wrap: wrap;
        }
        .btn-shortcut {
            font-size: 20px;
            border-radius: 0 !important;
            text-transform: uppercase;
        }

        .uk-slider-items li {
            height: 100px; 
            overflow: hidden;
        }

        .position-relative {
            position: relative;
        }

        .play {
            width: 40px;
            height: 40px;
            position: absolute;
            left: calc(50% - 20px);
            top: calc(50% - 20px);
            cursor: pointer;
            display: block;
        }
    </style>
@endsection

@section("js")
   
    @php
        App\Services\Helpers\File::js('/js/modification.js');   
    @endphp   
    <script>

        $(function() {

            $("#shop-quich-order").on("submit", function() {

                $.ajax({
                    url: "/shop-quich-order",
                    type: "POST",
                    data: $("#shop-quich-order").serialize(),
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $("#shop-quich-order").replaceWith(data);
                    },
                });

                return false;
            });

            $(".grade-star").mouseenter(function() {
                let index = $(this).index();
                $(".grade-stars").find(".grade-star").each(function() {
                    if ($(this).index() <= index) {
                        $(this).addClass("hover");
                    } else {
                        $(this).removeClass("hover");
                    }
                });
            });

            $(".grade-stars").mouseleave(function() {
                $(".grade-star").removeClass("hover");
            });

            $(".grade-star").click(function() {
                let index = $(this).index();
                $("[name='grade']").val(index + 1);
                $(".grade-stars").find(".grade-star").each(function() {
                    if ($(this).index() <= index) {
                        $(this).addClass("fill");
                    } else {
                        $(this).removeClass("fill");
                    }
                });
            });

        });
        
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.addEventListener('shown', (e) => {
                if (e.target && e.target.classList.contains('uk-lightbox')) {
                setTimeout(() => {
                    const video = e.target.querySelector('video');
                    if (video) {
                    video.setAttribute('autoplay', '');
                    video.setAttribute('muted', '');
                    video.setAttribute('playsinline', '');
                    video.play().catch(() => {});
                    }
                }, 100);
                }
            });
        });
    </script>
@endsection