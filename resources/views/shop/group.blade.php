@extends('main')

@section('seo_title', !empty($group->seo_title) ? $group->seo_title : $group->name)
@section('seo_description', $group->seo_description)
@section('seo_keywords', $group->seo_keywords)

@section('content')

    <div class="uk-section-xsmall tm-tovar">
        <div uk-grid="" class="uk-grid">
        
            @php

                $links = [
                    'sorting' => $sorting
                ];

                $total = $items->total();
                $countProperties = count($filterProperties, COUNT_RECURSIVE) - count($filterProperties);

            @endphp
        
            <!--сам каталог-->
            <div class="uk-width-expand@m">
            
                <div class="uk-h3 uk-text-bold">
                    <div class="uk-inline-block">{{ $group->name }}</div>
                    @if (count($properties) > 0)
                        <button class="uk-button uk-button-default filter-btn" type="button" uk-toggle="target: #filter">
                            Фильтрация@if (isset($filterProperties) && $countProperties > 0):
                                @php
                                    $title = \App\Services\Helpers\Str::plural($countProperties, "Применен", "Применено", "Применено");
                                    $title2 = \App\Services\Helpers\Str::plural($countProperties, "Фильтр", "Фильтра", "Фильтров");
                                @endphp 
                                {{ $title }} {{ $countProperties }} {{ $title2 }}  
                            @endif
                        </button>
                    @endif

                    @if (count($SubGroups) > 0) 
                        <div>
                            @foreach ($SubGroups as $SubGroup)
                                <a class="uk-button uk-button-default filter-btn uk-margin-small-top uk-margin-small-bottom" href="{{ $SubGroup->url }}">{{ $SubGroup->name }}</a>
                            @endforeach
                        </div>
                    @endif

                </div>
            
                <!--фильтр-->
                @if (count($properties) > 0)
                    
                    <form action="{{ $path }}" id="filter" uk-offcanvas="flip: true; overlay: true" class="uk-offcanvas filter">
                        <div class="uk-offcanvas-bar tm-filtr-mob uk-dark" role="dialog" aria-modal="true">
                    
                            <div class="uk-card uk-card-default uk-position-relative uk-card-media-top uk-light">
                                Все фильтры <button class="uk-offcanvas-close" type="button" uk-close></button>
                            </div>
                            
                            <div class="uk-section uk-section-small">
                            
                                <ul uk-accordion="multiple: true" class="uk-accordion">

                                    @foreach ($properties as $property)

                                        @switch ($property->type)

                                            @case(4)

                                                <li class="uk-open">
                                                    <a class="uk-accordion-title" href="#" id="uk-accordion-50-title-0" role="button" aria-controls="uk-accordion-50-content-0" aria-expanded="true" aria-disabled="false">{{ $property->name }}</a>
                                                    <div class="uk-accordion-content" id="uk-accordion-50-content-0" role="region" aria-labelledby="uk-accordion-50-title-0">
                                                        <ul @class([
                                                            "uk-nav", "uk-nav-default", "tm-color" => $property->destination == 1
                                                        ]) data-id="{{ $property->id }}">   

                                                            @foreach ($property->shopItemList->listItems as $ListItem)
                                                                @php
                                                                    $checked = '';
                                                                    if (isset($filterProperties[$property->id]) && in_array($ListItem->id, $filterProperties[$property->id])) {
                                                                        $checked = ' checked="checked"';
                                                                    }
                                                                    $background = $property->destination == 1 && !empty($ListItem->color) ? ' background-color:'. $ListItem->color : '';
                                                                @endphp
                                                     
                                                                <li>
                                                                    <label class="uk-margin-small-bottom uk-display-block">
                                                                        <input data-id="{{ $ListItem->id }}" {{ $checked }} onclick="Filter.init($(this))" {{ $checked }} style="{{ $background }}" name="property_{{ $property->id }}_{{ $ListItem->id }}" value="1" class="uk-checkbox uk-margin-small-right" type="checkbox"><span>{{ $ListItem->value }}</span>
                                                                    </label>
                                                                </li>
                                                            @endforeach

                                                        </ul>
                                                    </div>
                                                </li>
                                        
                                            @break

                                        @endswitch

                                    @endforeach

                                </ul>
                            
                            </div>
                            
                            <div class="uk-grid uk-child-width-1-1 uk-grid-small uk-grid-stack" uk-grid="">
                                <div><button class="uk-button uk-button-primary uk-width-1-1" id="filter-items-found" type="submit">Показать {{ $items->total() }}</button></div>
                                <div><button onclick="Filter.clear()" class="uk-button uk-button-default uk-width-1-1" type="button">Очистить все</button></div>
                            </div>
                    
                        </div>

                        <input type="hidden" name="sorting" value="{{ $sorting }}" />

                    </form>

                @endif
                <!--фильтр-->
                
                <!--сортировка-->
                <div class="uk-child-width-1-2 uk-grid-small uk-grid" uk-grid="">
                    <div class="uk-text-small uk-first-column">
                        {{ $total }} {{ \App\Services\Helpers\Str::plural($total, "Товар", "Товара", "Товаров"); }}
                    </div>
                    <div class="uk-text-right">
                        <div class="uk-inline">
                            <button class="uk-button uk-button-link uk-text-small" type="button" aria-haspopup="true">Сортировать: <span uk-drop-parent-icon="" class="uk-icon uk-drop-parent-icon"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12"><polyline fill="none" stroke="#000" stroke-width="1.1" points="1 3.5 6 8.5 11 3.5"></polyline></svg></span></button>
                            <div class="uk-card uk-card-body uk-card-default uk-card-small uk-drop" uk-drop="mode: click">
                                <ul class="uk-nav uk-nav-default">
                                    <li><a onclick="$('[name=sorting]').val('new'); $('#filter').submit()">вначале новые</a></li>
                                    <li><a onclick="$('[name=sorting]').val('old'); $('#filter').submit()">вначале старые</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!--сортировка-->

                @if (isset($filterProperties) && $countProperties > 0 && $total == 0)
                    <h4 class="uk-text-center">Не найдены товары по заданным параметрам. Попробуйте изменить критерии поиска.</h4>
                @endif
                
                <div class="uk-child-width-1-3@s uk-child-width-1-5@m uk-child-width-1-2 uk-grid-small uk-grid items" uk-grid="">

 

                    @php
                        $client = Auth::guard('client')->user();

                    @endphp
                    @foreach ($items as $item)
                        @include('shop.list-item', [
                            'item' => $item,
                            'client' => $client,
                            'clientFavorites' => !is_null($client) ? $client->getClientFavorites() : [],
                        ])
                    @endforeach   

                </div>
                
                <!--пагинация-->
                @if ($items->hasPages())
                    <div class="pagination-auto uk-hidden" data-group="{{ $group->id }}">
                        {{ $items->appends($links)->links(('vendor.pagination.default')) }}
                    </div>
                @endif
                <!--пагинация-->

                <div>
                    {!! $group->text !!}
                </div>
            
            </div>
            <!--сам каталог-->
        
        </div>
    </div>

    

@endsection

@section("js")
    @php
        App\Services\Helpers\File::js('/js/shop-group.js');
    @endphp

    <script>
        var Filter = {

            form: $("#filter"),

            group: {{ $group->id }},

            clear: function() {
                this.form.find("input[type='checkbox']").each(function(){
                    if ($(this).prop("checked") == true) {
                        $(this).prop("checked", false);
                    }
                    if ($(this).is(':disabled')) {
                        $(this).removeAttr("disabled");
                    }
                });

                Filter.init();
            },

            init: function(item = false) {

                Spiner.show();

                $.ajax({
                    url: "/filter",
                    type: "POST",
                    data: this.form.serialize() + "&shop_group_id=" + this.group,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function (data) {

                    $("#filter-items-found").text(data.button);
                    
                    Spiner.hide();
                    },
                });

            }
        }
    </script>

@endsection

