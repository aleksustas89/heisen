@extends("admin.main")

@section('title', 'Редактирование заказа')

@section('breadcrumbs')
    <div class="page-title-box d-flex flex-column">
        <div class="float-start">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                @endforeach
                <li class="breadcrumb-item">Редактирование заказа № {{ $order->id }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    @if (session('success'))
        <div class="alert alert-success border-0" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-0" role="alert">
            {{ session('error') }}
        </div>
    @endif


    <div class="row">
        <div class="col-12 col-lg-6">

            <div class="card" id="id_content">
                <form action="{{ route('shop-order.update', $order->id) }}" method="POST" id="formEdit" enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    @if ($order->not_call == 1)
                        <div class="alert alert-success border-0" role="alert">
                            <strong>Не звонить!</strong> Пользователь указал опцию отказа от подтвеждения заказа
                        </div>
                    @endif
             
                    
                    <div class="card-body tab-content">
                        <div class="slimscroll activity-scroll">
                            <div class="activity">
                                <div class="activity-info mt-3">
                                    <div class="icon-info-activity">
                                        <i class="las la-shopping-basket bg-soft-primary"></i>
                                    </div>
                                    <div class="activity-info-text">
                                        <div class="row mb-3">
                                            <div class="col-12 col-sm-4"> 
                                                <label class="mb-1 my-2">Номер заказа</label>
                                                <input type="text" disabled name="id" value="{{ $order->id }}" class="form-control" placeholder="Номер заказа">
                                            </div>
            
                                            <div class="col-12 col-sm-4"> 
                                                <label class="mb-1 my-2">Дата заказа</label>
                                                <input type="datetime-local" name="datetime" value="{{ $order->created_at }}" class="form-control" placeholder="Дата заказа">
                                            </div>
            
                                            <div class="col-12 col-sm-4"> 
                                                <label class="mb-1 my-2">Клиент</label>
            
                                                <select id="client_id" name="client_id">
                                                    <option value="0">...</option>
                                                    @foreach (App\Models\Client::get() as $Client)
                                                        @php
                                                            $selected = $Client->id == $order->client_id ? ' selected="selected"' : '';
                                                        @endphp
                                                        <option {{ $selected }} value="{{ $Client->id }}">{{ $Client->name }} [{{ $Client->id }}]</option>
                                                    @endforeach
                                                </select> 
                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="activity-info mt-3">
                                    <div class="icon-info-activity">
                                        <i class="las la-dollar-sign bg-soft-primary"></i>
                                    </div>
                                    <div class="activity-info-text">
                                        <div class="row mb-3">
                                            <div class="col-12 col-sm-4"> 
                                                <div class="d-inline-block order-col-sum">
                                                    <label class="mb-1 my-2">Сумма заказа</label>
                                                    <input type="text" disabled name="sum" value="{{ $order->getSum() }}" class="form-control" placeholder="Сумма заказа">
                                                </div>
                                                <div class="d-inline-block">
                                                    <select class="form-control" name="shop_currency_id">
                                         
                                                        @foreach (App\Models\ShopCurrency::orderBy("default", "Desc")->get() as $ShopCurrency) 
                                                            @php
                                                                $selected = $ShopCurrency->id == $order->shop_currency_id ? ' selected="selected"' : '';
                                                            @endphp
                                                            <option {{ $selected }} value="{{ $ShopCurrency->id }}">{{ $ShopCurrency->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            
                                            </div>
                                            <div class="col-12 col-sm-4"> 
                                                <label class="mb-1 my-2">Способ оплаты</label>
                                                <select class="form-control" name="shop_payment_system_id">
                                                    <option value="0">...</option>
                                                    @foreach (App\Models\ShopPaymentSystem::get() as $ShopPaymentSystem) 
                                                    @php
                                                        $selected = $ShopPaymentSystem->id == $order->shop_payment_system_id ? ' selected="selected"' : '';
                                                    @endphp
                                                    <option {{ $selected }} value="{{ $ShopPaymentSystem->id }}">{{ $ShopPaymentSystem->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 col-sm-4 d-flex align-items-end"> 
                                                @if ($order->paid == 1)
                                                    <div style="margin: 0 0 5px 0;">
                                                        <span style="color: green; font-size: 18px;"><i class="las la-check-double"></i> <b>Оплачено</b></span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="activity-info mt-3">
                                    <div class="icon-info-activity">
                                        <i class="las la-balance-scale bg-soft-primary"></i>
                                    </div>
                                    <div class="activity-info-text">
                                        <div class="row mb-3">
                                            <div class="col-6 col-sm-4"> 
                                                <label class="mb-1 my-2">Вес</label>
                                                <input type="text" name="weight" value="{{ $order->weight }}" class="form-control" placeholder="Вес заказа">
                                            </div>
                                            <div class="col-6 col-sm-4"> 
                                                <label class="mb-1 my-2">Ширина</label>
                                                <input type="text" name="width" value="{{ $order->width }}" class="form-control" placeholder="Ширина">
                                            </div>
                                            <div class="col-6 col-sm-4"> 
                                                <label class="mb-1 my-2">Высота</label>
                                                <input type="text" name="height" value="{{ $order->height }}" class="form-control" placeholder="Высота">
                                            </div>
                                            <div class="col-6 col-sm-4"> 
                                                <label class="mb-1 my-2">Глубина</label>
                                                <input type="text" name="length" value="{{ $order->length }}" class="form-control" placeholder="Глубина">
                                            </div>

                                        </div>
                                    </div>
                                </div>


                                <div class="activity-info mt-3">
                                    <div class="icon-info-activity">
                                        <i class="las la-truck bg-soft-primary"></i>
                                    </div>
                                    <div class="activity-info-text">
                                        <div class="row mb-3">

                                            <div class="col-12"> 
                                                <label class="mb-1 my-2">Доставка</label>

                                                <div>

                                                    @foreach ($shopDeliveries as $k => $ShopDelivery) 
                                                        @php
                                                            $checked = '';
                                                            if ($order->shop_delivery_id == $ShopDelivery->id) {
                                                                $checked = 'checked';
                                                            } else if ($k == 0) {
                                                                $checked = 'checked';
                                                            }
                                                        @endphp
                                                        <input {{ $checked }} id="delivery-{{ $ShopDelivery->id }}" onclick="radioTab.click($(this))" 
                                                            value="{{ $ShopDelivery->id }}" type="radio" class="btn-check" name="shop_delivery_id" autocomplete="off">
                                                        <label class="btn btn-outline-{{ $ShopDelivery->color }} btn-sm" for="delivery-{{ $ShopDelivery->id }}">{{ $ShopDelivery->name }}</label>
                                                    @endforeach

                                                    <div class="tab-content">        
                                                        <div @class([
                                                            "tab-pane", "p-3", "active" => $order->shop_delivery_id == 7 ? true : false
                                                        ]) id="tab-delivery-7" role="tabpanel">
                                                            <div class="row">     
                                                                
                                                                <div class="form-group">
                                                                    <label class="label my-2">Коробка</label>
                                                                    <select id="cdek_dimension_id" name="cdek_dimension_id">
                                                                        <option value="0">Без коробки</option>
                                                                        @foreach ($CdekDimensions as $CdekDimension)
                                                                            @if (!empty($CdekDimension->box_name))
                                                                                <option @if($CdekDimension->id == $order->cdek_dimension_id) selected @endif value="{{ $CdekDimension->id }}">{{ intval($CdekDimension->length) }}x{{ intval($CdekDimension->width) }}x{{ intval($CdekDimension->height) }}, до {{$CdekDimension->weight / 1000}} кг</option>
                                                                            @endif
                                                                        @endforeach
                                                                    </select>         
                                                                                                   
                                                                </div>                                                       
                                                                
                                                                <div class="form-group">
                                                                    <label class="label my-2">Город </label>
                                                    
                                                                    <input type="text" class="form-control" value="{{ $aDeliveryValues[10] ?? '' }}" name="delivery_7_city">
                                                                    <input type="hidden" value="{{ $aDeliveryValues[16] ?? '' }}" name="delivery_7_city_id">
                                                                </div>
                                                                            
                                                                <input type="hidden" name="delivery_7_delivery_type" value="{{ $aDeliveryValues[14] ?? '' }}">

                                                                <div class="form-group my-3">
                                                                    <input @if(isset($aDeliveryValues[14]) && $aDeliveryValues[14] == 11) checked="" @endif data-id="11" id="delivery-11" data-hidden="delivery_7_delivery_type" onclick="radioTab.click($(this))" value="11" type="radio" class="btn-check" name="delivery_field_14" autocomplete="off">
                                                                    <label class="btn btn-outline-cdek btn-sm" for="delivery-11">Отделение</label>

                                                                    <input @if(isset($aDeliveryValues[14]) && $aDeliveryValues[14] == 15) checked="" @endif data-id="15" id="delivery-15" data-hidden="delivery_7_delivery_type" onclick="radioTab.click($(this))" value="15" type="radio" class="btn-check" name="delivery_field_14" autocomplete="off">
                                                                    <label class="btn btn-outline-cdek btn-sm" for="delivery-15">Курьер</label>

                                                                    <div class="tab-content">
                                                                                                                                                                            
                                                                        <div @class(["tab-pane", "p-3", "active" => isset($aDeliveryValues[14]) && $aDeliveryValues[14] == 11 ? true : false]) id="tab-delivery-11" role="tabpanel">
                                                                            <div class="row">
                                                                                <div class="form-group">
                                                                                    @php
                                                                                        $CdekOffice = null;
                                                                                        if (isset($aDeliveryValues[17]) && $aDeliveryValues[17] > 0) {
                                                                                            $CdekOffice = \App\Models\CdekOffice::find($aDeliveryValues[17]);
                                                                                        }
                                                                                    @endphp
                                                                                    <label class="label my-2">
                                                                                        Отделение 
                                                                                        @if (!is_null($CdekOffice) && $CdekOffice->active == 0) 
                                                                                            <span style="color: red">(Отделение было отключено)</span> 
                                                                                        @endif
                                                                                    </label>
                                                                                    <input type="text" class="form-control" value="{{ $aDeliveryValues[11] ?? '' }}" name="delivery_7_office">
                                                                                    <input type="hidden" value="{{ $aDeliveryValues[17] ?? '' }}" name="delivery_7_office_id">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                                                                                                                                                                                                                          
                                                                        <div @class(["tab-pane", "p-3", "active" => isset($aDeliveryValues[14]) && $aDeliveryValues[14] == 15 ? true : false]) id="tab-delivery-15" role="tabpanel">
                                                                            <div class="row">
                                                                                <div class="form-group">
                                                                                    <label class="label my-2">Курьер </label>
                                                                                    <input type="text" class="form-control" value="{{ $aDeliveryValues[15] ?? '' }}" name="delivery_7_courier">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>                                                                                  
                                                            </div>

                                                            <div class="form-group my-3">
                                                                <div class="form-check form-switch form-switch-success">
                                                                    <input value="1" class="form-check-input" name="cash_on_delivery" type="checkbox" id="cash_on_delivery">
                                                                    <label for="cash_on_delivery">
                                                                        Наложенный платеж
                                                                    </label>
                                                                </div>
                                                            </div>

                                                            <div class="form-group my-3">
                                                                <label class="label my-2">Цена доставки </label>
                                                
                                                                <input type="number" class="form-control" value="" name="delivery_price">
                                                            </div>

                                                            <div class="card card-warning">
                                                                <div class="card-header d-flex">
                                                                    <h3 class="card-title">Данные отправителя <a target="_blank" href="{{ route('cdek-sender.edit', 1) }}"><i class="las la-pen text-secondary font-16"></i></a></h3>
                                                                    
                                                                </div>
                                                                <div class="card-body" style="display: block;">
                                                                    
                                                                    <div class="row form-group my-1">
                                                                        <div class="col-12">
                                                                            Город отправления: {{ $cdekSender->CdekCity->name }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="row form-group my-1">
                                                                        <div class="col-12">
                                                                            Тип: {{ App\Models\CdekSender::$Types[$cdekSender->type]["name"] }}
                                                                        </div>
                                                                    </div>
                                                                    @if (!is_null($cdekSender->CdekOffice) && $cdekSender->type == 0 && $cdekSender->cdek_office_id > 0)
                                                                        <div class="row form-group my-1">
                                                                            <div class="col-12">
                                                                                Офис: {{ $cdekSender->CdekOffice->name }}
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    @if ($cdekSender->type == 1 && !empty($cdekSender->address))
                                                                        <div class="row form-group my-1">
                                                                            <div class="col-12">
                                                                                Адрес: {{ $cdekSender->address }}
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    @if (!empty($cdekSender->name))
                                                                        <div class="row form-group my-1">
                                                                            <div class="col-12">
                                                                                Имя отправителя: {{ $cdekSender->name }}
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                </div>
                                                            </div>


                                                            
                                                            <div class="my-3">

                                                                @if (is_null($CdekOrder))
                                                                    <button type="button" id="create_cdek_order_btn" onclick="Cdek.createOrder({{ $order->id }})" class="btn btn-danger">Создать Cdek заказ</button>
                                                                @elseif(!is_null($CdekOrder) && strtotime('+1 hour', strtotime($CdekOrder->updated_at)) > strtotime(date("Y-m-d H:i:s")) && !empty($CdekOrder->url))
                                                                    <a href="{{ route("printCdekOrder", $CdekOrder->id) }}" target='_blank' class='btn btn-success mx-1'>Распечатать квитанции</a>
                                                                @elseif(!is_null($CdekOrder))
                                                                    <button type="button" id="create_cdek_order_btn" onclick="Cdek.createOrder({{ $order->id }})" class="btn btn-warning">Получить квитанции</button>
                                                                @endif
                                                            
                                                                <div id="cdek-errors"></div>
                                                            </div>
                                                            

                                                        </div>

                                                        <div @class([
                                                            "tab-pane", "p-3", "active" => $order->shop_delivery_id == 8 ? true : false
                                                        ]) id="tab-delivery-8" role="tabpanel">

                                                            <div id="boxberry_result">
                                                                <p>Адрес: {{ $aDeliveryValues[19] ?? '' }}</p>
                                                            </div>
                                                            <input type="hidden" class="form-control" value="{{ $aDeliveryValues[18] ?? '' }}" name="delivery_8_city">
                                                            <input type="hidden" class="form-control" value="{{ $aDeliveryValues[19] ?? '' }}" name="delivery_8_address">

                                                            <div class="row form-group my-1">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <p><a class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#boxberryModal" href="javascript:void(0)">Выбрать</a></p>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <div @class([
                                                            "tab-pane", "p-3", "active" => $order->shop_delivery_id == 1 ? true : false
                                                        ]) id="tab-delivery-1" role="tabpanel">
                                                            <div class="row">                                 
                                                                <div class="form-group">
                                                                    <label class="label my-2">Город </label>
                                                                    <input type="text" class="form-control" value="{{ $aDeliveryValues[12] ?? '' }}" name="delivery_1_city">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="label my-2">Отделение </label>
                                                                    <input type="text" class="form-control" value="{{ $aDeliveryValues[1] ?? '' }}" name="delivery_1_office">
                                                                </div>                                                                            
                                                            </div>
                                                        </div>       
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="activity-info mt-3">
                                    <div class="icon-info-activity">
                                        <i class="las la-address-book bg-soft-primary"></i>
                                    </div>
                                    <div class="activity-info-text">
                                        <div class="row">
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label class="label my-2">Фамилия</label>
                                                    <input type="text" class="form-control" value="{{ $order->surname }}" name="surname">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label class="label my-2">Имя</label>
                                                    <input type="text" class="form-control" value="{{ $order->name }}" name="name">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label class="label my-2">Отчество</label>
                                                    <input type="text" class="form-control" value="{{ $order->patronymic }}" name="patronymic">
                                                </div>
                                            </div>                                              
                                        </div>
        
                                        <div class="row">
                                            <div class="col-12 col-sm-12">                            
                                                <div class="form-group">
                                                    <label class="label my-2">Email</label>
                                                    <input type="email" class="form-control" name="email" value="{{ $order->email }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12">                            
                                                <div class="form-group">
                                                    <label class="label my-2">Телефон</label>
                                                    <input type="text" class="form-control" name="phone" value="{{ $order->phone }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="activity-info mt-3">
                                    <div class="icon-info-activity">
                                        <i class="las la-info bg-soft-primary"></i>
                                    </div>
                                    <div class="activity-info-text">
                                        <div class="row">
                                            <div class="col-12 col-sm-6">                            
                                                <div class="form-group">
                                                    <label class="label my-2">Информация к заказу</label>
                                                    <textarea name="description" class="form-control">{{ $order->description }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">                            
                                                <div class="form-group">
                                                    <label class="label my-2">Информация к доставке</label>
                                                    <textarea name="delivery_information" class="form-control">{{ $order->delivery_information }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php
                                $source = $order->Source;
                                @endphp

                                @if (!is_null($source))
                                    <div class="activity-info mt-3">
                                        <div class="icon-info-activity">
                                            <i class="las la-tags bg-soft-primary"></i>
                                        </div>
                                        <div class="activity-info-text mt-2">
                                            <h6 class="m-0 w-75">Метки</h6>
                                            <div class="row">
                                                <div class="col-12">
                                                    @if (!empty($source->service))
                                                        <div><span class="badge bg-primary">Рекламный сервис: {{ $source->service }}</span></div>
                                                    @endif

                                                    @if (!empty($source->campaign))
                                                        <div><span class="badge bg-secondary">Название рекламной кампании: {{ $source->campaign }}</span></div>
                                                    @endif

                                                    @if (!empty($source->ad))
                                                        <div><span class="badge bg-success">Рекламное объявление: {{ $source->ad }}</span></div>
                                                    @endif

                                                    @if (!empty($source->source))
                                                        <div><span class="badge bg-danger">Место размещения: {{ $source->source }}</span></div>
                                                    @endif

                                                    @if (!empty($source->medium))
                                                        <div><span class="badge bg-warning">Средство маркетинга: {{ $source->medium }}</span></div>
                                                    @endif
                                                    
                                                    @if (!empty($source->content))
                                                        <div><span class="badge bg-info">Дополнительная информация: {{ $source->content }}</span></div>
                                                    @endif

                                                    @if (!empty($source->term))
                                                        <div><span class="badge bg-dark">Ключевые слова: {{ $source->term }}</span></div>
                                                    @endif
                       
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" name="save" value="0" class="btn btn-primary">Сохранить</button>
                        <button type="submit" name="apply" value="1" class="btn btn-success">Применить</button>
                    </div>
                </form>
            </div>

        </div>

        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 col-sm-8 d-flex align-items-center">
                            <h4 class="card-title">Список товаров в заказе № {{ $order->id }}</h4>
                        </div>
                        <div class="col-6 col-sm-4 d-flex justify-content-end align-items-start">
                            <a href="{{ route("shop-order.shop-order-item.create", ['shop_order' => $order->id]) }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить</a>
                        </div>
                    </div>
                    
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 table-centered">
                            <thead>
                            <tr>
                                <th>Название</th>
                                <th>Кол-во</th>
                                <th>Цена</th>
                                <th class="d-mob-none">Сумма</th>
                                <th class="td-actions"></th>
                            </tr>
                            </thead>
                            <tbody>

                                @foreach ($order->ShopOrderItems()->where("deleted", 0)->get() as $orderItem) 
                                    <tr>
                                        <td class="td_editable">{{ $orderItem->name }}</td>
                                        <td class="td_editable">{{ $orderItem->quantity }}</td>
                                        <td class="td_editable">{{ $orderItem->price }}</td>
                                        <td class="d-mob-none">{{ $orderItem->price * $orderItem->quantity }}</td>
                                        <td class="td-actions">
                                            <a href="{{ route('shop-order.shop-order-item.edit', ['shop_order' => $order->id, 'shop_order_item' => $orderItem->id]) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                            <form action="{{ route('shop-order.shop-order-item.destroy', ['shop_order' => $order->id, 'shop_order_item' => $orderItem->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete($(this).parents('form'))" class="td-list-delete-btn">
                                                    <i class="las la-trash-alt text-secondary font-16"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="boxberryModal">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-4">Выберите отделение</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
            <div class="uk-modal-dialog uk-width-auto uk-modal-body p-3"  id="boxberry_map"></div>
            <div class="modal-footer">
                <button type="button" id="boxberryModalClose" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
              </div>
          </div>
        </div>
    </div>

    
@endsection

@section('js')
    <script src="/assets/plugins/select/selectr.min.js"></script>          
    <script src="/assets/js/radioTab.js"></script>           
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script> 
    <script src="/js/jquery.autocomplete.min.js"></script>
    <script type="text/javascript" src="//points.boxberry.ru/js/boxberry.js"> </script>
    <script>
        var BoxberryToken = '{{ $Boxberry->token }}';
        boxberry.openOnPage('boxberry_map');
        var boxberryCity = '{{ $aDeliveryValues[18] ?? '' }}';
        boxberry.open(boxberry_callback,BoxberryToken,boxberryCity,'', 1000, 500, 0, 50, 50, 50);
   
        function boxberry_callback(result) {

            $("[name='delivery_8_city']").val(result.name);
            $("[name='delivery_8_address']").val(result.address);

            $("#boxberry_result").html('<p>Адрес: ' + result.address + '</p>');

            $("#boxberryModalClose").click();

        }
    </script>
    
    <script>
        var create_order_route = '{{ route("createCdekOrder") }}';
        new Selectr('#cdek_dimension_id');
        new Selectr('#client_id');
    </script>

    @php
        App\Services\Helpers\File::js('/assets/js/pages/shopOrder.js');
    @endphp

@endsection


@section('css')
    <link href="/assets/plugins/select/selectr.min.css" rel="stylesheet" type="text/css" />
    <style>
        .order-col-sum {
            width: 65%;
        }
        .badge {
            color: #fff !important;
        }
        .btn-success {
            color: #fff !important;
        }
        @media (min-width: 1600px) {
            .modal-xl {
                max-width: 1460px;
            }
        }
    </style>    
@endsection