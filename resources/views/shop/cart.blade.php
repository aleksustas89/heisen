@extends('main')

@section('seo_title', "Корзина")
@section('seo_description', "Корзина")

@section('content')

    @php
        App\Services\Helpers\File::js('/js/cart.js');
    @endphp

    <div class="uk-container uk-container-xlarge">
        <div class="uk-section-small uk-padding-remove-bottom">
            <nav aria-label="Breadcrumb">
                <ul class="uk-breadcrumb">
                    <li><a href="{{ route('home') }}">Главная</a></li>                                                                         
                    <li><span>Корзина</span></li>
                </ul>
            </nav>
        </div>

        @if (isset($success))

            <div class="uk-flex uk-flex uk-align-center uk-flex-center uk-text-center uk-flex-middle uk-flex-column empty-cart">
                <div>
                    <a class="uk-navbar-item uk-logo" href="/" aria-label="Back to Home" tabindex="0" role="menuitem">HEISEN</a>
                </div>
                <h1>Ваш заказ оформлен</h1>

                @if (isset($paymentUrl))
                    <h3>Сейчас Вы будете перенаправлены на платежную систему</h3>

                    <script>
                        Ecommerce.purchase(@json($ecommerceData));
                        setTimeout(function() {
                            window.location.href = "{{ $paymentUrl }}";
                        }, 3000);
                    </script>

                @else
                    <script>
                        Ecommerce.purchase(@json($ecommerceData));
                    </script>
                    <h3>Наши менеджеры скоро свяжутся с Вами!</h3>

                @endif

            </div>

        @elseif (isset($Cart["countItems"]) && $Cart["countItems"] > 0)

            <h1 id="item-name" class="uk-h2 uk-margin-remove-vertical uk-section-small uk-padding-remove-top">Оформление заказа</h1>

            @error('email')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Введите E-mail в формате heisen@gmail.com</p>
                </div>
            @enderror
            @error('name')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>{{ $message }}</p>
                </div>
            @enderror
            @error('surname')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>{{ $message }}</p>
                </div>
            @enderror
            

            @error('phone')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Заполните поле Телефон</p>
                </div>
            @enderror
            @error('city')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Заполните поле Город</p>
                </div>
            @enderror
            @error('delivery_7_city_id')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Заполните поле Город</p>
                </div>
            @enderror
            @error('delivery_7_office_id')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Заполните поле Отделение Cdek</p>
                </div>
            @enderror
            @error('delivery_1_address')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Заполните поле Отделение Почты России</p>
                </div>
            @enderror

            @error('delivery_8_city')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Заполните поле Отделение Boxberry</p>
                </div>
            @enderror

            <div class="uk-grid" uk-grid class="cart-block">
                <div class="uk-width-2-3@s cart-block-user-data">
                    <form method="POST" id="cart-order" onsubmit="ym({{ env('YM_ID') }}, 'reachGoal', 'Order'); return true;">
                        @csrf
                        
                        <div class="uk-card uk-card-default uk-card-body uk-card-small uk-form-stacked">
                            <div class="uk-h4">Доставка</div>
                            <hr />   	
                
                            @if (count($shopDeliveries))
                                <div class="uk-form-label">Выберете способ доставки</div>

                                <ul class="uk-subnav uk-subnav-pill" uk-switcher>

                                    <li @if(old('shop_delivery_id') == 7) class="uk-active" @endif>
                                        <a data-hidden="shop_delivery_id" data-id="7" href="#cdek-window" uk-toggle=""
                                            onclick="Cart.chooseDelivery($(this));" aria-selected="true" role="tab" 
                                            id="uk-switcher-2-tab-0" aria-controls="uk-switcher-2-tabpanel-0">
                                            CDEK
                                        </a>
                                    </li>

                                    <li @if(old('shop_delivery_id') == 8) class="uk-active" @endif>
                                        <a data-hidden="shop_delivery_id" data-id="8" href="javascript::void(0)" 
                                            onclick="Cart.chooseDelivery($(this)); Cart.chooseBoxberry()" aria-selected="true" role="tab" 
                                            id="uk-switcher-2-tab-2" aria-controls="uk-switcher-2-tabpanel-2">
                                            Boxberry
                                        </a>
                                    </li>

                                    <li @if(old('shop_delivery_id') == 1) class="uk-active" @endif>
                                        <a data-hidden="shop_delivery_id" data-id="1"
                                            onclick="Cart.chooseDelivery($(this));" href="#pochta-rf-window" uk-toggle="" aria-selected="true" role="tab" 
                                            id="uk-switcher-2-tab-1" aria-controls="uk-switcher-2-tabpanel-1">
                                            Почта России
                                        </a>
                                    </li>
                                </ul>

                                <input type="hidden" name="shop_delivery_id" value="7" /> 
    
                                <ul class="uk-switcher uk-margin">

                                    <li role="tabpanel">

                                        <button type="button" class="uk-button" href="#cdek-window" uk-toggle="" role="button">Выбрать адрес доставки</button>

                                        <input type="hidden" name="delivery_7_delivery_type" value="{{ old('delivery_7_delivery_type') ?? 11 }}">
                                        
                                        <input type="hidden" name="delivery_7_city" value="{{ old('delivery_7_city') }}">
                                        <input type="hidden" name="delivery_7_office" value="{{ old('delivery_7_office') }}">
                                        <input type="hidden" name="delivery_7_office_id" value="{{ old('delivery_7_office_id') }}">

                                        <div id="cdekResult" class="uk-margin-top">
                                            @if (!empty(old('delivery_7_city')))
                                                <p>Город: {{ old('delivery_7_city') }}</p>
                                            @endif

                                            @if (!empty(old('delivery_7_office')))
                                                <p>Отделение: {{ old('delivery_7_office') }}</p>
                                            @endif

                                        </div>
                                    </li>

                                    <li>

                                        <button type="button" class="uk-button" onclick="Cart.chooseBoxberry()">Выбрать адрес доставки</button>

                                        <input type="hidden" name="delivery_8_id" value="{{ old('delivery_8_id') }}"/>
                                        <input type="hidden" name="delivery_8_city" value="{{ old('delivery_8_city') }}"/>
                                        <input type="hidden" name="delivery_8_address" value="{{ old('delivery_8_address') }}" />

                                        <input type="hidden" name="delivery_8_price" value="{{ old('delivery_8_price') }}"> 
                                        
                                        
                                        <div id="boxberry_result" class="uk-margin-top">
                                            @if (!empty(old('delivery_8_city')))
                                                <p>Город: {{ old('delivery_8_city') }}</p>
                                            @endif

                                            @if (!empty(old('delivery_8_address')))
                                                <p>Адрес: {{ old('delivery_8_address') }}</p>
                                            @endif

                                            @if (!empty(old('delivery_8_price')))
                                                <p>Ориентировочная цена: {{ old('delivery_8_price') }} ₽</p>
                                            @endif

                                        </div>

                                        <div id="boxberry-modal" style="" uk-modal>
                                            <div class="uk-modal-dialog uk-width-auto uk-modal-body"  id="boxberry_map">
                                                <button class="uk-modal-close-outside" type="button" uk-close></button>
                                            </div>
                                        </div>

                                    </li>
                                 
                                    <li>     
                                        
                                        <button type="button" class="uk-button" href="#pochta-rf-window" uk-toggle="" role="button">Выбрать адрес доставки</button>
                                        
                                        <input type="hidden" name="delivery_1_address" value="{{ old('delivery_1_address') }}" />
                                        <input type="hidden" name="delivery_1_index" value="{{ old('delivery_1_index') }}" />
                                        <input type="hidden" name="delivery_1_region" value="{{ old('delivery_1_region') }}" />
                                        <input type="hidden" name="delivery_1_area" value="{{ old('delivery_1_area') }}" />
                                        <input type="hidden" name="delivery_1_city" value="{{ old('delivery_1_city') }}" />

                                        <div id="prResult" class="uk-margin-top">

                                            @if (!empty(old('delivery_1_address')))
                                                <p>Адрес: {{ old('delivery_1_address') }}
                                                    @if (!empty(old('delivery_1_index'))), {{old('delivery_1_index')}}@endif
                                                    @if (!empty(old('delivery_1_region'))), {{old('delivery_1_region')}}@endif
                                                    @if (!empty(old('delivery_1_area'))), {{old('delivery_1_area')}}@endif
                                                    @if (!empty(old('delivery_1_city'))), {{old('delivery_1_city')}}@endif
                                                </p>
                                            @endif
                                        </div>                                          
                                    </li>
                                </ul>
                            @endif

                        </div>
                        @if (count($Payments) > 0)
                            <div class="uk-card uk-card-default uk-card-body uk-margin uk-card-small uk-form-stacked">
                                <div class="uk-h4">Оплата</div>
                                <hr />
                                <div class="uk-form-label">Выберите способ оплаты</div>
                                <div class="uk-form-controls">
                                    <ul class="uk-list">
                                        @foreach ($Payments as $k => $Payment)
                                            @php
                                                $is_personalization = $personalization && $Payment->description =='hand' ? true : false;
                                            @endphp
                                            <li>
                                                <label>
                                                    <input {{ $k == 0 || old('shop_payment_system_id') == $Payment->id ? 'checked' : '' }} 
                                                        @if ($is_personalization) disabled="disabled" @endif
                                                        id="shop_payment_system_{{ $Payment->id }}"
                                                        class="uk-radio" 
                                                        type="radio" 
                                                        value="{{ $Payment->id }}" 
                                                        name="shop_payment_system_id"> {{ $Payment->name }}

                                                        @if ($is_personalization) (Этод метод оплаты недоступен при выборе персонализации) @endif


                                                    @if ($Payment->id == 7)
                                                        <div id="shop_payment_system_{{ $Payment->id }}_desc" class="toggle-block">
                                                            
                                                            <div class="cont">

                                                                <div style="margin-bottom:2px; font-size:12px;"><b>Оплата наличными или картой банка при доставке заказа</b></div>
                                                                
                                                                <div style="margin-bottom:5px; color:#9d8661"><b>Обращаем ваше внимание!</b></div>
                                                                <div>При данном способе оплаты взимается комиссия - 5%</div>
                                                            </div>
                                                        </div>
                                                    @endif


                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                

                            </div>
                        @endif
                        <div class="uk-card uk-card-default uk-card-body uk-card-small uk-margin">
                            <div class="uk-h4">Контактные данные</div>
                            <hr />
                            <div class="uk-margin">
                                <label class="uk-form-label" for="form-stacked-text">Фамилия</label>
                                <div class="uk-form-controls">
                                    <input value="{{ old('surname') ?? $client->surname ?? '' }}" class="uk-input" name="surname" id="form-stacked-text" type="text" placeholder="Введите фамилию...">
                                </div>
                            </div>
                            <div class="uk-margin">
                                <label class="uk-form-label" for="form-stacked-text">Имя</label>
                                <div class="uk-form-controls">
                                    <input value="{{ old('name') ?? $client->name ?? '' }}" class="uk-input @error('name') is-invalid @enderror" name="name" required="" id="form-stacked-text" type="text" placeholder="Введите имя...">
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="form-stacked-text">Телефон</label>
                                <div class="uk-form-controls">
                                    <input value="{{ old('phone') ?? $client->phone ?? '' }}" class="uk-input @error('phone') is-invalid @enderror" name="phone" id="form-stacked-text" type="tel" placeholder="Введите телефон...">
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="form-stacked-text">E-mail</label>
                                <div class="uk-form-controls">
                                    <input value="{{ old('email') ?? $client->email ?? '' }}" class="uk-input" name="email" id="form-stacked-text" type="text" placeholder="Введите e-mail...">
                                </div>
                            </div>
                            <div class="uk-form-label">Комментарий к заказу (не обязательно)</div>    	    	
                            <div class="uk-margin">
                                <textarea name="description" class="uk-textarea" rows="5" placeholder="Комментарий к заказу (не обязательно)" aria-label="Textarea">{{ old('description') }}</textarea>
                            </div>

                            <label class="uk-form-label"><input value="{{ old('not_call') ?? 1 }}" @if (old('not_call') == 1) checked @endif name="not_call" class="uk-checkbox" type="checkbox"> Не звоните мне для подтверждения заказа</label>
                            <div class="uk-text-center uk-margin">
                                <button class="uk-button uk-button-primary uk-width-1-1">Оформить и оплатить заказ</button>
                            </div> 

                        </div>
                    </form>
                </div>

                <div class="uk-width-expand@s cart-block-items" id="cart">
                    @include('shop.cart-items')
                </div>
            </div>
        @else
            <div class="uk-flex uk-flex uk-align-center uk-flex-center uk-text-center uk-flex-middle  uk-flex-column empty-cart">
                <div>
                    <a class="uk-navbar-item uk-logo" href="/" aria-label="Back to Home" tabindex="0" role="menuitem">HEISEN</a>
                </div>
                <h1>В корзине нет <br>ни одного товара :(</h1>
                <p>А на <a href="/">главной</a> странице есть много интересного:)</p>
            </div>
        @endif
    </div>


    <div class="uk-modal-full " id="cdek-window" uk-modal>
        <div class="uk-modal-dialog" style="display: flex; height: 100%;">
            <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
            <div class="cdek-map" id="map" style=""></div>
        </div>
    </div>

    <div class="uk-modal-full " id="pochta-rf-window" uk-modal>
        <div class="uk-modal-dialog" style="display: flex; height: 100%;">
            <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
            <div class="pochta-rf-map" id="pochta-rf-map" style="">
                <script src="https://widget.pochta.ru/map/widget/widget.js"></script>
                <script>
                    ecomStartWidget({
                        id: 52409,
                        containerId: 'pochta-rf-map',                                
                        callbackFunction: pochtaRfCallback                                                                    
                    });
                </script>
            </div>
        </div>
    </div>

@endsection

@section("js")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
    <script src="/js/jquery.autocomplete.min.js"></script>

    <script type="text/javascript" src="//points.boxberry.ru/js/boxberry.js"> </script>
    <script>

        $(function() {

            $('[name="phone"]').mask("+7 (999) 999-9999", {autoclear: false});
        });

        if ($("#cart-order").length) {
            
            var BoxberryToken = '{{ isset($Boxberry) ? $Boxberry->token : '' }}';
            boxberry.openOnPage('boxberry_map');
            boxberry.open(boxberry_callback, BoxberryToken,'','', 1000, 500, 0, 20, 20, 20);
    
            function boxberry_callback(result) {

                let boxResult = '';

                boxResult += '<p>Адрес доставки: ' + result.address + '</p>';
                //boxResult += '<p>Ориентировочная цена: ' + result.price + ' ₽</p>';

                $("[name='delivery_8_id']").val(result.id);
                $("[name='delivery_8_city']").val(result.name);
                $("[name='delivery_8_address']").val(result.address);
                $("[name='delivery_8_price']").val(result.price);

                $("#boxberry_result").html(boxResult);

                UIkit.modal("#boxberry-modal").hide();
            }

        }

    </script>

    @if (isset($CdekOffices))
        <script src="https://api-maps.yandex.ru/2.1/?load=package.standard,package.geoObjects&lang=ru-RU&amp;apikey=616a9f13-3554-476b-98c2-1bda1c2eddf4" type="text/javascript"></script>
        <script>

            var routeChooseOffice = '{{ route("chooseOffice") }}';

            var aPoints = [
                @foreach ($CdekOffices as $CdekOffice)
                    [{{ $CdekOffice->latitude }}, {{ $CdekOffice->longitude }}],
                @endforeach
            ];
        
            var aPointsData = [
                @foreach ($CdekOffices as $CdekOffice)
                    ['{{ $CdekOffice->code }}', "{{ $CdekOffice->name }}", "{{ \App\Services\Helpers\Str::clean($CdekOffice->address_comment) }}", "{{ $CdekOffice->work_time }}"],
                @endforeach
            ];
        
            var Cdek = {
        
                window: function() {
        
                    UIkit.modal("#cdek-window").show();
                },
        
                chooseOffice: function(code) {
        
                    $.ajax({
                        url: routeChooseOffice,
                        type: "GET",
                        data: {"code": code},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function (data) {

                            let html = '';

                            if (data.city.length) {
                                html += '<p>Город: '+ data.city +'</p>'; 
                                $("[name='delivery_7_city']").val(data.city);
                            }

                            $("[name='delivery_7_office_id']").val(data.code);
                            
                            html += '<p>Отделение: '+ data.name +'</p>';

                            $("[name='delivery_7_office']").val(data.name);

                            $("#cdekResult").html(html);

                            UIkit.modal("#cdek-window").hide();      
                        },
                    });
        
                }
            }
        
            function init () {
                myMap = new ymaps.Map('map', {
                    center: [55.751574, 37.573856],
                    zoom: 9,
                    controls: ['zoomControl'],
                }, {
                    yandexMapDisablePoiInteractivity: true
                }),
        
                    clusterer = new ymaps.Clusterer({

                    clusterIconColor: "#1ab248",
        
                    clusterIconPieChartRadius: 15,
        
                    clusterIconPieChartCoreRadius: 10,

                    clusterIconPieChartStrokeWidth: 1,

                    groupByCoordinates: false,
                }),

                getPointData = function (index) {
                    return {
                        balloonContentHeader: '<b>' + aPointsData[index][1] + '</b>',
                        balloonContentBody: '<p>' + aPointsData[index][2] + '</p><p>Время работы: ' + aPointsData[index][3] + '</p><p><a href="javascript:void(0)" onclick="Cdek.chooseOffice(\''+ aPointsData[index][0] +'\')">Выбрать отделение</a></p>',
                    };
                },

                getPointOptions = function () {
                    return {
                        preset: 'islands#icon',
                        iconColor: '#1ab248',
                        zIndex: 10000
                    };
                },
                points = aPoints,
                geoObjects = [];

                mySearchControl = new ymaps.control.SearchControl({
                    options: {
                        noPlacemark: true
                    }
                }),
                // Результаты поиска будем помещать в коллекцию.
                mySearchResults = new ymaps.GeoObjectCollection(null, {
                    hintContentLayout: ymaps.templateLayoutFactory.createClass('$[properties.name]')
                });
                myMap.controls.add(mySearchControl);
                myMap.geoObjects.add(mySearchResults);
                // При клике по найденному объекту метка становится красной.
                mySearchResults.events.add('click', function (e) {
                    e.get('target').options.set('preset', 'islands#redIcon');
                });
                // Выбранный результат помещаем в коллекцию.
                mySearchControl.events.add('resultselect', function (e) {
                    var index = e.get('index');
                    mySearchControl.getResult(index).then(function (res) {
                    mySearchResults.add(res);
                    });
                }).add('submit', function () {
                        mySearchResults.removeAll();
                })
        
                for(var i = 0, len = points.length; i < len; i++) {
                    geoObjects[i] = new ymaps.Placemark(points[i], getPointData(i), getPointOptions());
                }
        
                clusterer.options.set({
                    gridSize: 60,
                    //clusterDisableClickZoom: true
                });
        
                clusterer.add(geoObjects);
                myMap.geoObjects.add(clusterer);

                var myGeoObjects = myMap.geoObjects;
        
            }
        
            ymaps.ready(init);
        
            var myMap;
        
        </script>
    @endif

@endsection

@section("css")

    <style>
        .empty-cart {
            height: 300px;
        }
        .cancel-chosen-city {margin: 0 5px; font-weight: bold;border-bottom: 1px dashed;}
        .input-spiner{font-size: 10px; position: absolute; display: none;}
        .input-spiner span{width: 10px; height: 10px;}
        .uk-modal-full.uk-open{display: flex!important}
        #boxberry_result {font-weight: 500}
        #boxberry_result a {border-bottom: 1px dashed;}
        .cart-close {top: -14px !important; right: -20px !important;}
        .pointer {cursor: pointer;}

        @media (max-width: 640px) {
            .cart-block-user-data {
                order: 1;
            }
            .cart-block-items {
                order: 0;
            }

            #item-name {
                text-align: center;
            }
        }

        .cdek-map, .pochta-rf-map {
            height:100%; 
            width: 100%;
            padding: 0; 
            margin: 0;
        }
    </style>

@endsection