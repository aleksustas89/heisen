@extends('skeleton')

@section('seo_title', "Корзина")
@section('seo_description', "Корзина")

@section('skeleton_content')

    <div class="uk-container uk-container-xlarge">
        <div class="uk-section-small uk-padding-remove-bottom">
            <nav aria-label="Breadcrumb">
                <ul class="uk-breadcrumb">
                    <li><a href="{{ route('home') }}">Главная</a></li>                                                                         
                    <li><span>Корзина</span></li>
                </ul>
            </nav>
        </div>

        @if ($cartCount > 0)

            <h1 id="item-name" class="uk-h2 uk-margin-remove-vertical uk-section-small uk-padding-remove-top">Оформление заказа</h1>

            @error('email')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Введите E-mail в формате borys-jonson@gmail.com</p>
                </div>
            @enderror
            @error('name')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Заполните поле Имя</p>
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

            <div class="uk-grid" uk-grid class="cart-block">
                <div class="uk-width-2-3@s cart-block-user-data">
                    <form method="POST">
                        @csrf

                        <div class="uk-card uk-card-default uk-card-body uk-card-small uk-form-stacked">
                            <div class="uk-h4">Доставка</div>
                            <hr />   	
                
                            <!--
                            <div class="uk-form-label">Выберете город доставки</div>
                            <nav class="uk-navbar-container" uk-navbar>
                                <div class="uk-inline uk-width-1-1 uk-margin">
                                    <button class="uk-button uk-button-default tm-bitton-fotm-list uk-width-1-1" type="button">
                                        <span id="chosenCity" data-chosen="Выбран город" data-default="Выберете город">
                                            
                                            @if(null !== old("city_autocomplete"))
                                                Выбран город: {{ old("city_autocomplete") }} <a onclick="Cart.cancelChosenCity()" class="cancel-chosen-city">Отменить</a>
                                            @elseif (null !== old("city_custom"))
                                                Выбран город: {{ old("city_custom") }} <a onclick="Cart.cancelChosenCity()" class="cancel-chosen-city">Отменить</a>
                                            @else
                                                Выберете город
                                            @endif
                                        </span> 
                                        <span uk-drop-parent-icon></span>
                                    </button>
                                    <div class="uk-card uk-card-body uk-card-default uk-card-small" uk-drop="mode: click;boundary: !.uk-navbar; stretch: x; flip: false">
                                        <input value="{{ old("city_autocomplete") }}" name="city_autocomplete" class="uk-input" id="city-autocomplete" type="text" placeholder="Начните печатать название города" id="autocomplete" />
                                        <input type="hidden" id="city_id" name="city_id"  value="{{ old("city_id") }}" />
                                        
                                        <hr>
                                        <div><b>Либо впишите название города:</b></div>
                                        <br>
                                        <input class="uk-input" id="city_custom" value="{{ old("city_custom") }}" name="city_custom" type="text" placeholder="Название города" />
                                        <input type="hidden" id="city" name="city" value="{{ old("city") }}" />
                                    </div>
                                </div>
                            </nav>-->
                
                            @if (count($shopDeliveries))
                                <div class="uk-form-label">Выберете способ доставки</div>

                                <ul class="uk-subnav uk-subnav-pill" uk-switcher>
                                    @foreach ($shopDeliveries as $k => $ShopDelivery) 
                                        <li @if(old('shop_delivery_id') == $ShopDelivery->id) class="uk-active" @endif><a data-hidden="shop_delivery_id" data-id="{{ $ShopDelivery->id }}" href="javascript::void(0)" onclick="Cart.chooseDelivery($(this))">{{ $ShopDelivery->name }}</a></li>
                                    @endforeach
                                </ul>

                                <input type="hidden" name="shop_delivery_id" value="{{ $shopDeliveries[0]->id }}" />

                                <ul class="uk-switcher uk-margin">
                                    @foreach ($shopDeliveries as $k => $ShopDelivery) 
                                        <li>
                                            @foreach ($ShopDelivery->ShopDeliveryFields->where("parent", 0)->sortBy('sorting') as $ShopDeliveryField)

                                                @php
                                                $name = 'delivery_' . $ShopDeliveryField->shop_delivery_id . '_' . $ShopDeliveryField->field;
                                                @endphp
                                                
                                                @if ($ShopDeliveryField->type == 1)
                                                    <div class="uk-margin">
                                                        <label class="uk-form-label" for="form-stacked-text">{{$ShopDeliveryField->caption  }}</label>
                                                        <div class="uk-form-controls">
                                                            <input data-b="{{ $name }}" @if(null !== old($name)) value="{{ old($name) }}"  @endif class="uk-input" type="text" placeholder="{{$ShopDeliveryField->caption  }}" name="delivery_{{ $ShopDeliveryField->shop_delivery_id }}_{{ $ShopDeliveryField->field }}">
                                                        </div>
                                                    </div>
                                                @elseif($ShopDeliveryField->type == 2)
                                                    <input type="hidden" @if(null !== old($name)) value="{{ old($name) }}"  @endif name="delivery_{{ $ShopDeliveryField->shop_delivery_id }}_{{ $ShopDeliveryField->field }}">
                                                @elseif($ShopDeliveryField->type == 3 && !empty($ShopDeliveryField->options_from))
                                                    @php
                                                        $className = $ShopDeliveryField->options_from;
                                                    @endphp

                                                    <div class="uk-margin">
                                                        <label class="uk-form-label" for="form-stacked-text">{{$ShopDeliveryField->caption  }}</label>
                                                        <div class="uk-form-controls">
                                                            <select id="delivery_{{ $ShopDeliveryField->shop_delivery_id }}_{{ $ShopDeliveryField->field }}" name="delivery_{{ $ShopDeliveryField->shop_delivery_id }}_{{ $ShopDeliveryField->field }}">
                                                                <option value="">...</option>
                                                                @if ($ShopDeliveryField->frontend_select_type == 0)
                                                                    @foreach ($className::orderBy("name", "ASC")->get() as $Value)
                                                                        <option data-id="{{ $Value->id }}" value="{{ $Value->name }}">{{ $Value->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>

                                     
                                                @elseif($ShopDeliveryField->type == 4)

                                                    @php
                                                        $SubFields = \App\Models\ShopDeliveryField::where("parent", $ShopDeliveryField->id)->orderBy("sorting")->get();
                                                    @endphp

                                                    <input type="hidden" name="delivery_{{ $ShopDeliveryField->shop_delivery_id }}_{{ $ShopDeliveryField->field }}" value="{{ $SubFields[0]->id }}" />

                                                    <ul class="uk-subnav uk-subnav-pill" uk-switcher>

                                                        @foreach ($SubFields as $SubField)
                                                            <li>
                                                                <a data-id="{{ $SubField->id }}" data-hidden="delivery_{{ $ShopDeliveryField->shop_delivery_id }}_{{ $ShopDeliveryField->field }}" href="javascript::void(0)" onclick="Cart.chooseDelivery($(this))">{{ $SubField->caption }}</a>
                                                            </li>
                                                        @endforeach
                                                     
                                                    </ul>

                                                    <ul class="uk-switcher uk-margin">
                                                        @foreach ($SubFields as $SubField)

                                                            @php
                                                            $SubName = 'delivery_' . $SubField->shop_delivery_id . '_' . $SubField->field;
                                                            @endphp

                                                            @if ($SubField->type == 1)
                                                                <div class="uk-margin">
                                                    
                                                                    <div class="uk-form-controls">
                                                                        <input data-b="{{ $SubName }}" class="uk-input" type="text" placeholder="{{$SubField->caption  }}" name="delivery_{{ $SubField->shop_delivery_id }}_{{ $SubField->field }}">
                                                                    </div>
                                                                </div>
                                                            @elseif($SubField->type == 3 && !empty($SubField->options_from))
                                                                @php
                                                                    $className = $ShopDeliveryField->options_from;
                                                                @endphp
            
                                                                <div class="uk-margin">
                                                           
                                                                    <div class="uk-form-controls">
                                                                        <select id="delivery_{{ $SubField->shop_delivery_id }}_{{ $SubField->field }}" name="delivery_{{ $SubField->shop_delivery_id }}_{{ $SubField->field }}">
                                                                            <option value="">...</option>
                                                                            @if ($SubField->frontend_select_type == 0)
                                                                                @foreach ($className::orderBy("name", "ASC")->get() as $Value)
                                                                                    <option data-id="{{ $Value->id }}" value="{{ $Value->name }}">{{ $Value->name }}</option>
                                                                                @endforeach
                                                                            @endif
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                            @endif

                                                        @endforeach
                                                    </ul>

                                                @endif
                                            @endforeach

                                        </li>
                                    @endforeach
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
                                            <li><label><input {{ $k == 0 || old('shop_payment_system_id') == $Payment->id ? 'checked' : '' }} class="uk-radio" type="radio" value="{{ $Payment->id }}" name="shop_payment_system_id"> {{ $Payment->name }}</label></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                        <div class="uk-card uk-card-default uk-card-body uk-card-small">
                            <div class="uk-h4">Контактные данные</div>
                            <hr />
                    
                            <div class="uk-margin">
                                <label class="uk-form-label" for="form-stacked-text">Имя</label>
                                <div class="uk-form-controls">
                                    <input value="{{ old('name') ?? $client->name ?? '' }}" class="uk-input @error('name') is-invalid @enderror" name="name" required="" id="form-stacked-text" type="text" placeholder="Введите имя...">
                                </div>
                            </div>
                            <div class="uk-margin">
                                <label class="uk-form-label" for="form-stacked-text">Фамилия</label>
                                <div class="uk-form-controls">
                                    <input value="{{ old('surname') ?? $client->surname ?? '' }}" class="uk-input" name="surname" id="form-stacked-text" type="text" placeholder="Введите фамилию...">
                                </div>
                            </div>
                            <div class="uk-margin">
                                <label class="uk-form-label" for="form-stacked-text">Телефон</label>
                                <div class="uk-form-controls">
                                    <input value="{{ old('phone') ?? $client->phone ?? '' }}" class="uk-input @error('phone') is-invalid @enderror" name="phone" id="form-stacked-text" type="text" placeholder="Введите телефон...">
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="form-stacked-text">E-mail</label>
                                <div class="uk-form-controls">
                                    <input value="{{ old('email') ?? $client->email ?? '' }}" class="uk-input @error('email') is-invalid @enderror" name="email" required="" id="form-stacked-text" type="text" placeholder="Введите e-mail...">
                                </div>
                            </div>
                            <div class="uk-form-label">Комментарий к заказу (не обязательно)</div>    	    	
                            <div class="uk-margin">
                                <textarea name="description" class="uk-textarea" rows="5" placeholder="Textarea" aria-label="Textarea">{{ old('description') }}</textarea>
                            </div>
                            <label class="uk-form-label"><input value="{{ old('not_call') ?? 1 }}" @if (old('not_call') == 1) checked @endif name="not_call" class="uk-checkbox" type="checkbox"> Не звоните мне для подтверждения заказа</label>
                            <div class="uk-text-center uk-margin">
                                <button class="uk-button uk-button-primary uk-width-1-1">Оформить заказ</button>
                            </div>  
                
                        </div>
                    </form>
                </div>

                <div class="uk-width-expand@s cart-block-items" id="cart">
                    @include('shop.cart-items')
                </div>
            </div>
        @elseif (null !== session('success'))
            <div class="uk-flex uk-flex uk-align-center uk-flex-center uk-text-center uk-flex-middle uk-flex-column empty-cart">
                <div>
                    <a class="uk-navbar-item uk-logo" href="/" aria-label="Back to Home" tabindex="0" role="menuitem">HEISEN</a>
                </div>
                <h1>Ваш заказ оформлен</h1>
                <div>Мы уже работаем, чтобы отправить Ваш заказ как можно скорее.</div>
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

@endsection

@section("js")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-form-styler@2.0.2/dist/jquery.formstyler.min.js"></script>

    @php
        App\Services\Helpers\File::js('/js/cart.js');
    @endphp
    <script>
        $('#city-autocomplete').autocomplete({
            serviceUrl: '/get-cities',
            onSelect: function (suggestion) {
                $("#city_custom").val('');
                $("#city_id").val(suggestion.data);
                $("#city").val(suggestion.value);
                $("#chosenCity").html($("#chosenCity").data("chosen") +": "+ suggestion.value + "<a onclick='Cart.cancelChosenCity()' class='cancel-chosen-city' click=''>Отменить</a>");
            }
        });

        var delay = (function(){
            var timer = 0;
            return function(callback, ms){
                clearTimeout (timer);
                timer = setTimeout(callback, ms);
            };
        })();

        $(function(){
            $("[name='city_custom']").keyup(function(){
                let value = $(this).val();
                delay(function(){
                    $("#city").val(value);
                    $("#city-autocomplete").val('');
                    $("#chosenCity").html($("#chosenCity").data("chosen") +": "+ value + "<a onclick='Cart.cancelChosenCity()' class='cancel-chosen-city' click=''>Отменить</a>");
                }, 1000);
            });

            $('select').styler({
				selectSearch: true,
			});

            $('[name="phone"]').mask("+7 (999) 999-9999", {autoclear: false});

            $("body").on("change", "#delivery_7_region", function() {

                $("#delivery_7_office").html("<option>...</option>").trigger('refresh');

                let value = $(this).find(":selected").data("id");

                Spiner.show();

                $.ajax({
                    url: "/get-cdek-cities",
                    type: "POST",
                    data: {"region": value},
                    dataType: "html",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $("#delivery_7_city").html(data).trigger('refresh');
                        Spiner.hide();
                    },
                });
                
            });

            $("body").on("change", "#delivery_7_city", function() {

                let value = $(this).find(":selected").data("id");

                Spiner.show();

                $.ajax({
                    url: "/get-cdek-offices",
                    type: "POST",
                    data: {"city": value},
                    dataType: "html",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $("#delivery_7_office").html(data).trigger('refresh');
                        Spiner.hide();
                    },
                });

            });
            
            $("body").on("click", ".jq-selectbox", function() {
                if (!$(this).hasClass(".opened")) {
                    $(this).find("[type='search']").focus();
                }
            });

        });
        
    </script>
@endsection

@section("css")
    <link href="https://cdn.jsdelivr.net/npm/jquery-form-styler@2.0.2/dist/jquery.formstyler.min.css" rel="stylesheet">
    <link href="/css/jquery.formstyler.theme.css" rel="stylesheet">

    
    <style>
        .empty-cart {
            height: 300px;
        }
        .cancel-chosen-city {margin: 0 5px; font-weight: bold;border-bottom: 1px dashed;}

        @media (max-width: 640px) { 
            .cart-block-user-data {
                order: 1;
            }
            .cart-block-items {
                order: 0;
            }
        }
    </style>

@endsection