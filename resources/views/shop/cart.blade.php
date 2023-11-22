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

        @if (isset($success))

            <div class="uk-flex uk-flex uk-align-center uk-flex-center uk-text-center uk-flex-middle uk-flex-column empty-cart">
                <div>
                    <a class="uk-navbar-item uk-logo" href="/" aria-label="Back to Home" tabindex="0" role="menuitem">HEISEN</a>
                </div>
                <h1>Ваш заказ оформлен</h1>
                <h3>Сейчас Вы будете перенаправлены на платежную систему</h3>

                <script>
                    setTimeout(function() {
                        window.location.href = "{{ $paymentUrl }}";
                    }, 3000);
                </script>

            </div>

        @elseif (isset($cartCount) && $cartCount > 0)

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
                    <p>Заполните поле Отделение</p>
                </div>
            @enderror
            @error('delivery_1_city')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Заполните поле Город</p>
                </div>
            @enderror
            @error('delivery_1_city')
                <div class="uk-alert-danger uk-margin-remove-top" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Заполните поле Отделение</p>
                </div>
            @enderror



            <div class="uk-grid" uk-grid class="cart-block">
                <div class="uk-width-2-3@s cart-block-user-data">
                    <form method="POST" id="cart-order">
                        @csrf

                        <div class="uk-card uk-card-default uk-card-body uk-card-small uk-form-stacked">
                            <div class="uk-h4">Доставка</div>
                            <hr />   	
                
                            @if (count($shopDeliveries))
                                <div class="uk-form-label">Выберете способ доставки</div>

                                <ul class="uk-subnav uk-subnav-pill" uk-switcher>
                                    @foreach ($shopDeliveries as $k => $ShopDelivery) 
                                        <li @if(old('shop_delivery_id') == $ShopDelivery->id) class="uk-active" @endif><a data-hidden="shop_delivery_id" data-id="{{ $ShopDelivery->id }}" href="javascript::void(0)" onclick="Cart.chooseDelivery($(this))">{{ $ShopDelivery->name }}</a></li>
                                    @endforeach
                                </ul>

                                <input type="hidden" name="shop_delivery_id" value="{{ $shopDeliveries[0]->id }}" />

                                <ul class="uk-switcher uk-margin">
                                     
                                    <li role="tabpanel" class="uk-active">
                                                                        
                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="form-stacked-text">Город</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input" type="text" placeholder="Город" name="delivery_7_city"  value="{{ old('delivery_7_city') }}">
                                                <div class="input-spiner"><span uk-spinner="ratio: 3" class="uk-icon uk-spinner" role="status"></span> Загрузка</div>
                                                <input type="hidden" name="delivery_7_city_id" value="{{ old('delivery_7_city_id') }}">
                                            </div>
                                        </div>
 
                                        <input type="hidden" name="delivery_7_delivery_type" value="{{ old('delivery_7_office_id') ?? 11 }}">

                                        <ul class="uk-subnav uk-subnav-pill" uk-switcher="">
                                            <li class="uk-active" role="presentation">
                                                <a data-id="11" data-hidden="delivery_7_delivery_type" href="javascript::void(0)" onclick="Cart.chooseDelivery($(this))" aria-selected="true" role="tab" id="uk-switcher-3-tab-0" aria-controls="uk-switcher-3-tabpanel-0">Отделение</a>
                                            </li>
                                            <li role="presentation">
                                                <a data-id="15" data-hidden="delivery_7_delivery_type" href="javascript::void(0)" onclick="Cart.chooseDelivery($(this))" aria-selected="false" tabindex="-1" role="tab" id="uk-switcher-3-tab-1" aria-controls="uk-switcher-3-tabpanel-1">Курьер</a>
                                            </li>                                             
                                        </ul>

                                        <ul class="uk-switcher uk-margin" role="presentation" >

                                            <li class="uk-margin uk-active">
                                                <div class="uk-form-controls">
                                                    <input @if(empty(old('delivery_7_city_id'))) disabled @endif class="uk-input" type="text" placeholder="Отделение" name="delivery_7_office" value="{{ old('delivery_7_office') }}">
                                                    <input type="hidden" name="delivery_7_office_id" value="{{ old('delivery_7_office_id') }}">
                                                    <div class="input-spiner"><span uk-spinner="ratio: 3" class="uk-icon uk-spinner" role="status"></span> Загрузка</div>
                                                </div>
                                            </li>
 
                                            <li class="uk-margin" id="uk-switcher-3-tabpanel-1">  
                                                <div class="uk-form-controls">
                                                    <input @if(empty(old('delivery_7_city_id'))) disabled @endif class="uk-input" type="text" placeholder="Адрес доставки" name="delivery_7_courier" value="{{ old('delivery_7_courier') }}">
                                                </div>
                                            </li>        
                                        </ul>                                              
                                    </li>
                                 
                                    <li>                                       
                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="form-stacked-text">Город</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input" type="text" placeholder="Город" name="delivery_1_city" value="{{ old('delivery_1_city') }}">
                                            </div>
                                        </div>
                                 
                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="form-stacked-text">Отделение</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input" type="text" placeholder="Отделение" name="delivery_1_office" value="{{ old('delivery_1_office') }}">
                                            </div>
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
                                            <li><label><input {{ $k == 0 || old('shop_payment_system_id') == $Payment->id ? 'checked' : '' }} class="uk-radio" type="radio" value="{{ $Payment->id }}" name="shop_payment_system_id"> {{ $Payment->name }}</label></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                        <div class="uk-card uk-card-default uk-card-body uk-card-small uk-margin">
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
                                <textarea name="description" class="uk-textarea" rows="5" placeholder="Textarea" aria-label="Textarea">{{ old('description') }}</textarea>
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

@endsection

@section("js")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
    <script src="/js/jquery.autocomplete.min.js"></script>

    @php
        App\Services\Helpers\File::js('/js/cart.js');
    @endphp
    <script>
        $('[name="delivery_7_city"]').autocomplete({
            serviceUrl: '/get-cdek-cities',
            minChars: 0,
            onSearchStart: function() {
                $(this).siblings(".input-spiner").show();
            },
            onSearchComplete: function() {
                $(this).siblings(".input-spiner").hide();
            },
            onSelect: function (suggestion) {
                $("[name='delivery_7_office']").val("").removeAttr("disabled");
                $("[name='delivery_7_courier']").removeAttr("disabled");
                $("[name='delivery_7_city_id']").val(suggestion.data);

                $('[name="delivery_7_office"]').autocomplete({
                    serviceUrl: '/get-cdek-offices',
                    params: {"city_id": suggestion.data},
                    minChars: 0,
                    onSelect: function (suggestion) {
                        $("[name='delivery_7_office_id']").val(suggestion.data);
                    },
                    onSearchStart: function() {
                        $(this).siblings(".input-spiner").show();
                    },
                    onSearchComplete: function() {
                        $(this).siblings(".input-spiner").hide();
                    }
                });
            }
        });

        if ($('[name="delivery_7_city_id"]').val().length) {
            $('[name="delivery_7_office"]').autocomplete({
                serviceUrl: '/get-cdek-offices',
                params: {"city_id": $('[name="delivery_7_city_id"]').val()},
                minChars: 0,
                onSelect: function (suggestion) {
                    $("[name='delivery_7_office_id']").val(suggestion.data);
                },
                onSearchStart: function() {
                    $(this).siblings(".input-spiner").show();
                },
                onSearchComplete: function() {
                    $(this).siblings(".input-spiner").hide();
                }
            });
        }

        $("[name='delivery_7_city']").keyup(function(){
            let value = $(this).val();
            if (!value.length) {
                delay(function() {
                    $("[name='delivery_7_office'], [name='delivery_7_office_id']").val("");
                }, 1000);
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

            $('[name="phone"]').mask("+7 (999) 999-9999", {autoclear: false});
        });
    </script>
@endsection

@section("css")

    <style>
        .empty-cart {
            height: 300px;
        }
        .cancel-chosen-city {margin: 0 5px; font-weight: bold;border-bottom: 1px dashed;}
        .input-spiner{font-size: 10px; position: absolute; display: none;}
        .input-spiner span{width: 10px; height: 10px;}

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