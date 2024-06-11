@extends('main')

@section('seo_title', 'Товары с скидками')
@section('seo_description', )
@section('seo_keywords', )

@section('content')

    <div class="uk-section-xsmall tm-tovar">



        <div uk-grid="" class="uk-grid">
        
            <!--сам каталог-->
            <div class="uk-width-expand@m">
            
                <div class="uk-h3 uk-text-bold">Товары с скидками</div>
            
                <div class="uk-child-width-1-5@s uk-child-width-1-2 uk-grid-small uk-grid items" uk-grid="" itemscope itemtype="https://schema.org/OfferCatalog">

                    @php
                        $client = Auth::guard('client')->user();

                    @endphp
                    @foreach ($ShopItems as $ShopItem)
                        @include('shop.list-item', [
                            'item' => $ShopItem,
                            'client' => $client,
                            'clientFavorites' => !is_null($client) ? $client->getClientFavorites() : [],
                        ])
                    @endforeach   

                </div>
                
                <!--пагинация-->
                @if ($ShopItems->hasPages())
                    <div class="pagination-auto uk-hidden" data-url="{{ route('showItemWithDiscountsAjax') }}">
                        {{ $ShopItems->links(('vendor.pagination.default')) }}
                    </div>
                @endif
                <!--пагинация-->

            </div>
            <!--сам каталог-->
        
        </div>
    </div>

@endsection


@section("js")

<script>
    $(window).on("scroll", function(){
        if ($(".pagination-auto").length) {
            var pagination = $(".pagination-auto"),
                topPagination = pagination.offset().top,
                h = window.innerHeight
                    || document.documentElement.clientHeight
                    || document.body.clientHeight;

            if (window.pageYOffset > topPagination - 2*h && !pagination.hasClass("disabled")) {

                pagination.addClass("disabled");
                
                $.ajax({
                    url: pagination.data("url") + "?page=" + $(".js-pagination-more").data("n"),
                    method: 'GET',
                    dataType: "html",
                    success: function(data) {
                            
                        pagination.response = $("<div>" + data + "</div>");

                        if (pagination.response.find(".tm-tovar").length) {
                            $(".items").append(pagination.items = pagination.response.find(".tm-tovar"))
                        }

                        if (pagination.response.find("a.js-pagination-more").length) {
                            $(".pagination").replaceWith(pagination.response.find(".pagination"));
                            pagination.removeClass("disabled");
                        }
                        else $(".pagination").remove();
                    }
                });
            }
        }
    });
</script>

@endsection