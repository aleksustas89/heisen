@extends('main')

@section('seo_title', $group->seo_title)
@section('seo_description', $group->seo_description)
@section('seo_keywords', $group->seo_keywords)

@section('content')

    <div class="uk-section-xsmall tm-tovar">
        <div uk-grid="" class="uk-grid">
        
            @if (isset($menuGroups) && count($menuGroups) > 0)
                <!--рубрики-->
                <div class="uk-width-1-4@m uk-first-column">
                
                    <ul class="uk-nav-default uk-nav-divider" uk-nav>
                        @php
                            function buildMenu($menuGroups, $group)
                            {

                                $result = '';

                                foreach ($menuGroups as $menuGroup) {

                                    $class = [];


                                    if ($menuGroup["id"] == $group->id) {
                                        $class[] = 'uk-active';
                                    }

                                    if ($menuGroup["sub"] && count($menuGroup["sub"]) > 0) {
                                        $class[] =  'uk-parent';
                                    }

                                    $arrov = $menuGroup["sub"] && count($menuGroup["sub"]) > 0 ? '<span uk-nav-parent-icon></span>' : '';

                                    $result .= '<li class="'. implode(' ', $class) .'"><a href="'. $menuGroup["path"] .'">'. $menuGroup["name"] . $arrov .'</a>';

                                    if ($menuGroup["sub"] && count($menuGroup["sub"]) > 0) {

                                        $result .= '<ul class="uk-nav-sub">';
                                        
                                        $result .= buildMenu($menuGroup["sub"], $group);
                                
                                        $result .= '</ul>';
                                    
                                    }
                                          
                                    $result .= '</li>';
                                }

                                return $result;
                            }

                            echo buildMenu($menuGroups, $group);
                        @endphp
                    </ul>
                
                </div>
                <!--рубрики-->
            @endif

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
                
                <div class="uk-child-width-1-4@s uk-child-width-1-2 uk-grid-small uk-grid" uk-grid="">

                    @foreach ($items as $item)
                        @include('shop.list-item', ['item' => $item])
                    @endforeach   

                </div>
                
                <!--пагенация-->
                {{ $items->appends($links)->links(('vendor.pagination.default')) }}
                <!--пагенация-->

                <div>
                    {!! $group->text !!}
                </div>
            
            </div>
            <!--сам каталог-->
        
        </div>
    </div>

    

@endsection

@section("js")
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="/js/shop-group.js"></script>
@endsection