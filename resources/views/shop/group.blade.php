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

            @endphp
        
            <!--сам каталог-->
            <div class="uk-width-expand@m">
            
                <div class="uk-h3 uk-text-bold">{{ $group->name }}</div>
            
                <!--фильтр-->
                @if (count($properties) > 0)
                    <form action="{{ $path }}" class="uk-child-width-auto uk-grid-small uk-grid" uk-grid="" id="filter">
          
                        @foreach ($properties as $property)

                            @switch ($property->type)

                                @case(4)

                                    <div class="uk-inline">
                                        <button class="uk-button uk-button-default" type="button">{{ $property->name }} <span uk-drop-parent-icon></span></button>
                                            <div class="uk-card uk-card-body uk-card-default uk-card-small uk-margin-remove-top" uk-drop="mode: click">
                                            <ul class="uk-nav uk-nav-default">
                                                @foreach ($property->shopItemList->listItems as $ListItem)
                                                    @php
                                                        $checked = '';
                                                        if (isset($filterProperties[$property->id]) && in_array($ListItem->id, $filterProperties[$property->id])) {
                                                            $checked = ' checked="checked"';
                                                            $links['property_' . $property->id . '_' . $ListItem->id]  = 'on';
                                                        }
                                                    @endphp
                                                    <li><a><label><input {{ $checked }} class="uk-checkbox" name="property_{{ $property->id }}_{{ $ListItem->id }}" type="checkbox"> {{ $ListItem->value }}</label></a></li>
                                                @endforeach
                                                
                                            </ul>
                                            <button class="uk-button uk-button-primary uk-margin uk-width-1-1" type="submit">Применить</button>
                                        </div>
                                    </div>
                            
                                @break

                            @endswitch

                        @endforeach

                        <input type="hidden" name="sorting" value="{{ $sorting }}" />
                    </form>
                @endif
                <!--фильтр-->
                
                <!--сортировка-->
                <div class="uk-child-width-1-2 uk-grid-small uk-grid" uk-grid="">
                    <div class="uk-text-small uk-first-column">
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
@endsection