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
                                            <div class="col-12 col-sm-4 d-flex"> 

                                                <div class="position-relative">
                                                    
                                                    
                                                    @if (!empty($prevOrder))
                                                        <div class="prev-order">
                                                            <a href="{{ $prevOrder }}" class="mr-2"><i class="las la-angle-left font-16"></i></a>
                                                        </div>
                                                    @endif

                                                    <label class="mb-1 my-2">Номер заказа</label>
                                                    <input type="text" name="order_id" value="{{ $order->id }}" class="form-control" placeholder="Номер заказа">

                                                    @if (!empty($nextOrder))
                                                        <div class="next-order">
                                                            <a href="{{ $nextOrder }}" class="mr-2"><i class="las la-angle-right font-16"></i></a>
                                                        </div>
                                                    @endif

                                                </div>

                                            </div>
            
                                            <div class="col-12 col-sm-4"> 
                                                <label class="mb-1 my-2">Дата заказа</label>
                                                <input type="datetime-local" name="datetime" value="{{ $order->created_at }}" class="form-control" placeholder="Дата заказа">
                                            </div>
            
                                            <div class="col-12 col-sm-4"> 
                                                <label class="mb-1 my-2">Клиент</label>

                                                @php
                                                    $value = $order->client_id > 0 ? $order->Client->surname . ' ' . $order->Client->name . ' [' . $order->client_id .']' : '';
                                                @endphp
                                                
                                                <input type="text" name="client" value="{{ $value }}" class="form-control" placeholder="Клиент">
                                                <input type="hidden" name="client_id" value="{{ $order->client_id }}" class="form-control">
                                
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
                                                    <label class="mb-1 my-2">Сумма</label>
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
                                                <label class="mb-1 my-2">Вес (гр.)</label>
                                                <input type="text" name="weight" value="{{ (int)$order->weight }}" class="form-control" placeholder="Вес заказа">
                                            </div>
                                            <div class="col-6 col-sm-4"> 
                                                <label class="mb-1 my-2">Ширина (см.)</label>
                                                <input type="text" name="width" value="{{ (int)$order->width }}" class="form-control" placeholder="Ширина">
                                            </div>
                                            <div class="col-6 col-sm-4"> 
                                                <label class="mb-1 my-2">Высота (см.)</label>
                                                <input type="text" name="height" value="{{ (int)$order->height }}" class="form-control" placeholder="Высота">
                                            </div>
                                            <div class="col-6 col-sm-4"> 
                                                <label class="mb-1 my-2">Глубина (см.)</label>
                                                <input type="text" name="length" value="{{ (int)$order->length }}" class="form-control" placeholder="Глубина">
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
                                                        <div @class(["tab-pane", "py-3", "active" => $order->shop_delivery_id == 7 ? true : false]) id="tab-delivery-7" role="tabpanel">


                                                            <div class="mb-3">
                                                               
                                                                <a data-bs-toggle="tooltip" data-bs-html="true" title="{{ $sCdekSender }}" class="text-decoration-underline" target="_blank" href="{{ route('cdek-sender.edit', 1) }}">
                                                                    Данные отправителя 
                                                                </a>
                                                            </div>

                                                            @if (is_null($CdekOrder))
                                                                {{-- <div class="row my-1">
                                                                    <div class="col-12">
                                                                        <a class="btn btn-outline-cdek active btn-sm" onclick="window.widget.open()" href="javascript:void(0)">Выбрать</a>
                                                                    </div>
                                                                </div> --}}

                                                                <div class="row my-1">
                                                                    <div class="col-12">
                                                                        <a class="btn btn-outline-cdek active btn-sm" data-bs-toggle="modal" data-bs-target="#cdekModal" href="javascript:void(0)">Выбрать</a>
                                                                    </div>
                                                                </div>

                                                            @endif

                                                            <input type="hidden" name="delivery_7_delivery_type" value="{{ $aDeliveryValues[14] ?? '' }}">                                 
                                                            <input type="hidden" name="delivery_7_office_id" value="{{ $aDeliveryValues[17] ?? '' }}">
                                                            <input type="hidden" name="delivery_7_city" value="{{ $aDeliveryValues[10] ?? '' }}"> 
                                                            <input type="hidden" name="delivery_7_office" value="{{ $aDeliveryValues[11] ?? '' }}"> 

                                                            
                                                            <div id="cdekResult" class="uk-margin-top my-3">

                                                                @if (isset($aDeliveryValues[10]) && !empty($aDeliveryValues[10]))
                                                                    <div class="my-1">Город: {{ $aDeliveryValues[10] }}</div>
                                                                @endif

                                                                @if (isset($aDeliveryValues[11]) && !empty($aDeliveryValues[11]))
                                                                    <div class="my-1">Отделение: {{ $aDeliveryValues[11] }}</div>
                                                                @endif

                                                            </div>
                                                            
                                                            @if (is_null($CdekOrder))

                                                                <div class="row">
                                                                    <div class="col-12 col-sm-6 mb-2">
                                                                        <label class="label">Коробка</label>
                                                                        <select id="cdek_dimension_id" name="cdek_dimension_id">
                                                                            <option value="0">Без коробки</option>
                                                                            @foreach ($CdekDimensions as $CdekDimension)
                                                                                @if (!empty($CdekDimension->box_name))
                                                                                    <option @if($CdekDimension->id == $order->cdek_dimension_id) selected @endif value="{{ $CdekDimension->id }}">{{ intval($CdekDimension->length) }}x{{ intval($CdekDimension->width) }}x{{ intval($CdekDimension->height) }}, до {{$CdekDimension->weight / 1000}} кг</option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>   
                                                                    </div>
                                                                    <div class="col-12 col-sm-6 mb-2">
                                                                        <label class="label">Цена доставки </label>
                                                                        <input type="number" class="form-control" value="0" name="delivery_price">
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="form-check form-switch form-switch-success">
                                                                            <input value="1" @if ($order->shop_payment_system_id == 5) checked @endif class="form-check-input" name="cash_on_delivery" type="checkbox" id="cash_on_delivery">
                                                                            <label for="cash_on_delivery">
                                                                                Оплата при получении
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            
                                                            @endif

                                                            <div class="my-3">

                                                                <div id="cdek_order_btn_create" @class(['d-none' => !is_null($CdekOrder)])>
                                                                    <button type="button" id="create_cdek_order_btn" onclick="Cdek.createOrder({{ $order->id }})" class="btn btn-outline-cdek active">Создать накладную</button>
                                                                </div>

                                                                <div id="cdek_order_btn_created">
                                                                    @if (!is_null($CdekOrder))
                                                                        <a href="{{ route("printCdekOrder", $CdekOrder->id) }}" target='_blank' class='btn btn-outline-cdek active'>Распечатать квитанцию</a>
                                                                        <button type="button" id="delete_cdek_order_btn" onclick="Cdek.deleteOrder('{{ route("deleteOrder", $CdekOrder->id) }}')" class="btn btn-outline-danger active">Удалить накладную</button>
                                                                    @endif
                                                                </div>

                                                                <div id="cdek-errors"></div>
                                                            </div>
                                                            
                                                        </div>

                                                        <div @class(["tab-pane", "py-3", "active" => $order->shop_delivery_id == 8 ? true : false]) id="tab-delivery-8" role="tabpanel">

                                                            <div class="mb-3">
                                                                <a data-bs-toggle="tooltip" data-bs-html="true" title="{{ $sBoxberrySender }}" class="text-decoration-underline" target="_blank" href="{{ route('boxberry-sender.edit', 1) }}">
                                                                    Данные отправителя 
                                                                </a>
                                                            </div>

                                                            <div class="row my-1">
                                                                <div class="col-12">
                                                                    <a class="btn btn-outline-boxberry active btn-sm" data-bs-toggle="modal" data-bs-target="#boxberryModal" href="javascript:void(0)">Выбрать</a>
                                                                </div>
                                                            </div>

                                                            <div id="boxberry_result" class="mt-2">
                                                                @if (!empty($aDeliveryValues[19]))
                                                                    <p>Адрес: {{ $aDeliveryValues[19] ?? '' }}</p>
                                                                @endif
                                                            </div>

                                                            <input type="hidden" class="form-control" value="{{ $aDeliveryValues[21] ?? '' }}" name="delivery_8_id">
                                                            <input type="hidden" class="form-control" value="{{ $aDeliveryValues[18] ?? '' }}" name="delivery_8_city">
                                                            <input type="hidden" class="form-control" value="{{ $aDeliveryValues[19] ?? '' }}" name="delivery_8_address">

                                                            @php
                                                                $BoxberryOrder = $order->BoxberryOrder;
                                                            @endphp

                                                            @if (is_null($BoxberryOrder))

                                                                <div id="boxberry_btns">

                                                                    <div class="row">
                                                                        <div class="col-12 col-sm-6 mb-2">
                                                                            <label class="label">Коробка</label>
                                                                            <select id="boxberry_dimension_id" name="boxberry_dimension_id">
                                                                                <option value="0">Без коробки</option>
                                                                                <option  value="1">Короб XS (150x150x,150)</option>
                                                                                <option  value="2">Короб S (200x200x,200)</option>
                                                                                <option  value="3">Короб M (350x200x,200)</option>
                                                                                <option  value="4">Короб L (350x300x,250)</option>
                                                                                <option  value="5">Короб XL (500x400x,350)</option>
                                                                            </select>   
                                                                        </div>
                                                                    </div>

                                                                    <div class="row my-1">
                                                                        <div class="col-12">
                                                                            <div class="form-check form-switch form-switch-success">
                                                                                <input value="1" @if ($order->shop_payment_system_id == 5) checked @endif class="form-check-input" name="pod" type="checkbox" id="pod">
                                                                                <label for="pod">
                                                                                    Оплата при получении
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <button type="button" id="create_boxberry_order_btn" onclick="Boxberry.createOrder({{ $order->id }})" class="btn btn-outline-boxberry active">Создать накладную</button>
                                                                    
                                                                </div>

                                                            @else
                                                                <a class="btn btn-outline-boxberry active" target="_blank" href="{{ $BoxberryOrder->url }}">Получить квитанцию</a>
                                                            @endif

                                                        </div>

                                                        <div @class(["tab-pane", "py-3", "active" => $order->shop_delivery_id == 1 ? true : false]) id="tab-delivery-1" role="tabpanel">
                                                            <div class="">      
                                                                
                                                                <div class="row my-1">
                                                                    <div class="col-12">
                                                                        <a class="btn btn-outline-pochta active btn-sm" data-bs-toggle="modal" data-bs-target="#pochta-rf-window" href="javascript:void(0)">Выбрать</a>
                                                                    </div>
                                                                </div>

                                                                <input type="hidden" name="delivery_1_address" value="{{ $aDeliveryValues[25] ?? '' }}" />
                                                                <input type="hidden" name="delivery_1_index" value="{{ $aDeliveryValues[26] ?? '' }}" />
                                                                <input type="hidden" name="delivery_1_region" value="{{ $aDeliveryValues[27] ?? '' }}" />
                                                                <input type="hidden" name="delivery_1_area" value="{{ $aDeliveryValues[28] ?? '' }}" />
                                                                <input type="hidden" name="delivery_1_city" value="{{ $aDeliveryValues[29] ?? '' }}" />
                                                                
                                                                @if ($order->shop_payment_system_id == 5)
                                                                    <b>Оплата при получении</a>
                                                                @endif
                                                                
                                                                <div id="prResult" class="mt-2">
                                                                    @php
                                                                        $aPrData = [];
                                                                        foreach ([26, 27, 28, 29, 25] as $i) {
                                                                            if (!empty($aDeliveryValues[$i])) {
                                                                                $aPrData[] = $aDeliveryValues[$i];
                                                                            }
                                                                        }
                                                                         
                                                                    @endphp

                                                                    @if (count($aPrData) > 0)
                                                                        <p>Адрес: {{ implode(", ", $aPrData) }}</p>
                                                                    @endif
                                                                </div>

                                                                <div id="pr_btns">
                                                                    @php
                                                                        $PrOrder = $order->PrOrder;
                                                                    @endphp

                                                                    @if (is_null($PrOrder))
                                                                        <button type="button" id="create_pr_order_btn" onclick="PochtaRossii.createOrder({{ $order->id }})" class="btn btn-outline-pochta active">Создать накладную</button>
                                                                    @else
                                                                        <div class='btn-outline-pochta fw-bold'>Трек: {{ $PrOrder->barcode }}</div>
                                                                    @endif
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
                <button type="button" id="cdek-close" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
            <div class="uk-modal-dialog uk-width-auto uk-modal-body p-3"  id="boxberry_map"></div>
            <div class="modal-footer">
                <button type="button" id="boxberryModalClose" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
              </div>
          </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="cdekModal">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-4">Выберите отделение</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
            <div class="uk-modal-dialog uk-width-auto uk-modal-body p-3"  id="cdek-map" style="width:100%; height:500px"></div>
            <div class="modal-footer">
                <button type="button" id="cdekModalClose" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
              </div>
          </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="pochta-rf-window">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-4">Выберите отделение</h1>
                <button type="button" id="pochta-rf-window-close" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

            <div class="pochta-rf-map uk-modal-dialog uk-width-auto uk-modal-body p-3" id="pochta-rf-map" style="height:500px">
                <script src="https://widget.pochta.ru/map/widget/widget.js"></script>
                <script>
                    function pochtaRfCallback(data) {

                        let aResult = new Array();

                        if (typeof data.indexTo != 'undefined' && data.indexTo != null) {
                            aResult[aResult.length] = data.indexTo;
                            $("[name='delivery_1_index']").val(data.indexTo);
                        }
                        if (typeof data.regionTo != 'undefined' && data.regionTo != null) {
                            aResult[aResult.length] = data.regionTo;
                            $("[name='delivery_1_region']").val(data.regionTo);
                        }
                        if (typeof data.areaTo != 'undefined' && data.areaTo != null) {
                            aResult[aResult.length] = data.areaTo;
                            $("[name='delivery_1_area']").val(data.areaTo);
                        }
                        if (typeof data.cityTo != 'undefined' && data.cityTo != null && data.cityTo != data.regionTo) {
                            aResult[aResult.length] = data.cityTo;
                            $("[name='delivery_1_city']").val(data.cityTo);
                        }  
                        if (typeof data.addressTo != 'undefined' && data.addressTo != null) {
                            aResult[aResult.length] = data.addressTo;
                            $("[name='delivery_1_address']").val(data.addressTo);
                        }  

                        let sResult = aResult.join(", ");

                        let Result = '<p>Адрес: ' + sResult + '</p>';

                        $("#prResult").html(Result);

                        $("#pochta-rf-window-close").click();
                    } 
                    ecomStartWidget({
                        id: 52409,
                        containerId: 'pochta-rf-map',                                
                        callbackFunction: pochtaRfCallback                                                                    
                    });
                </script>
            </div>

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
    <script type="text/javascript" src="//points.boxberry.ru/js/boxberry.js"> </script>
    <script src="/js/jquery.autocomplete.min.js"></script>
    <script>
        var BoxberryToken = '{{ $Boxberry->token }}';
        boxberry.openOnPage('boxberry_map');
        var boxberryCity = '{{ $aDeliveryValues[18] ?? '' }}';
        boxberry.open(boxberry_callback,BoxberryToken,boxberryCity,'', 1000, 500, 0, 20, 20, 20);
   
        function boxberry_callback(result) {

            $("[name='delivery_8_id']").val(result.id);
            $("[name='delivery_8_city']").val(result.name);
            $("[name='delivery_8_address']").val(result.address);

            $("#boxberry_result").html('<p>Адрес: ' + result.address + '</p>');

            $("#boxberryModalClose").click();

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

            var MapCdek = {

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

                            $("#cdekModalClose").click();    
                        },
                    });

                }
            }

            function init () {
                myMap = new ymaps.Map('cdek-map', {
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
                        balloonContentBody: '<p>' + aPointsData[index][2] + '</p><p>Время работы: ' + aPointsData[index][3] + '</p><p><a href="javascript:void(0)" onclick="MapCdek.chooseOffice(\''+ aPointsData[index][0] +'\')">Выбрать отделение</a></p>',
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
    
    <script>
        var create_boxberry_order_route = '{{ route("createBoxberryOrder") }}',
            create_cdek_order_route = '{{ route("createCdekOrder") }}',
            create_pr_order_route = '{{ route("createPrOrder") }}',
            routeGetOrders = '{{ route("getOrders") }}',
            routeGetClients = '{{ route("getClients") }}',
            currentOrder = '{{ $order->id }}'
            ;

        if ($("#cdek_dimension_id").length) {
            new Selectr('#cdek_dimension_id');
        }

        if ($("#boxberry_dimension_id").length) {
            new Selectr('#boxberry_dimension_id');
        }

        
        
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
        .tooltip-inner {text-align: start}
        .tooltip-inner {max-width: none;}
        .prev-order {margin: 0 0px 5px -15px;position: absolute;bottom: 0;}
        .next-order {margin: 0 -15px 5px 0px;position: absolute;bottom: 0;right: 0;}

        @media (min-width: 1600px) {
            .modal-xl {
                max-width: 1460px;
            }
        }
    </style>    
@endsection