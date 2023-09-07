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
        <div class="col-6">

            <div class="card" id="id_content">
                <form action="{{ route('shopOrder.update', $order->id) }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body tab-content">
                        <div class="slimscroll activity-scroll">
                            <div class="activity">
                                <div class="activity-info mt-3">
                                    <div class="icon-info-activity">
                                        <i class="las la-shopping-basket bg-soft-primary"></i>
                                    </div>
                                    <div class="activity-info-text">
                                        <div class="row mb-3">
                                            <div class="col-4"> 
                                                <label class="mb-1 my-2">Номер заказа</label>
                                                <input type="text" disabled name="id" value="{{ $order->id }}" class="form-control" placeholder="Номер заказа">
                                            </div>
            
                                            <div class="col-4"> 
                                                <label class="mb-1 my-2">Дата заказа</label>
                                                <input type="datetime-local" name="datetime" value="{{ $order->created_at }}" class="form-control" placeholder="Дата заказа">
                                            </div>
            
                                            <div class="col-4"> 
                                                <label class="mb-1 my-2">Клиент</label>
            
                                                <select class="select" name="client_id">
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
                                            <div class="col-5"> 
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
                                            <div class="col-7"> 
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

                                                <div class="form-group my-2">
                                                    <label class="label my-2">Город</label>
                                                    <input type="text" class="form-control" value="{{ $order->city }}" name="city">
                                                </div>
                                              

                                                <div>
                                                    @php
                                                        $shopDeliveries = App\Models\ShopDelivery::get();
                                                    @endphp

                                                    @foreach ($shopDeliveries as $k => $ShopDelivery) 
                                                        @php
                                                            $checked = '';
                                                            if ($order->shop_delivery_id == $ShopDelivery->id) {
                                                                $checked = 'checked';
                                                            } else if ($k == 0) {
                                                                $checked = 'checked';
                                                            }
                                                        @endphp
                                                        <input {{ $checked }} id="delivery-{{ $ShopDelivery->id }}" onclick="radioTab.click($(this))" value="{{ $ShopDelivery->id }}" type="radio" class="btn-check" name="shop_delivery_id" autocomplete="off">
                                                        <label class="btn btn-outline-{{ $ShopDelivery->color }} btn-sm" for="delivery-{{ $ShopDelivery->id }}">{{ $ShopDelivery->name }}</label>
                                                    @endforeach

                                                    <div class="tab-content">
                                                        @foreach ($shopDeliveries as $k => $ShopDelivery) 

                                                            @php
                                                                $active = '';
                                                                if ($order->shop_delivery_id > 0) {
                                                                    if ($order->shop_delivery_id == $ShopDelivery->id) {
                                                                        $active = 'active';
                                                                    }
                                                                } else {
                                                                    if ($k == 0) {
                                                                        $active = 'active';
                                                                    }
                                                                }
                                                                
                                                            @endphp

                                                            <div class="tab-pane p-3 {{ $active }}" id="tab-delivery-{{ $ShopDelivery->id }}" role="tabpanel">
                                                                <div class="row">

                                                                    @foreach ($ShopDelivery->ShopDeliveryFields as $ShopDeliveryField)
                                    
                                                                        @if ($ShopDeliveryField->type == 1)
                                                                            <div class="form-group">
                                                                                <label class="label my-2">{{$ShopDeliveryField->caption  }}</label>
                                                                                <input type="text" class="form-control" value="{{ $aDeliveryValues[$ShopDeliveryField->id] ?? '' }}" name="delivery_{{ $ShopDeliveryField->shop_delivery_id }}_{{ $ShopDeliveryField->field }}">
                                                                            </div>
                                                                        @elseif($ShopDeliveryField->type == 2)
                                                                            <input type="hidden" value="{{ $aDeliveryValues[$ShopDeliveryField->id] ?? '' }}" name="delivery_{{ $ShopDeliveryField->shop_delivery_id }}_{{ $ShopDeliveryField->field }}">
                                                                        @endif
                                                                    @endforeach

                                                                </div>
                                                            </div>
                                                        @endforeach
                
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
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label class="label my-2">Фамилия</label>
                                                    <input type="text" class="form-control" value="{{ $order->surname }}" name="surname">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label class="label my-2">Имя</label>
                                                    <input type="text" class="form-control" value="{{ $order->name }}" name="name">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label class="label my-2">Отчество</label>
                                                    <input type="text" class="form-control" value="{{ $order->patronymic }}" name="patronymic">
                                                </div>
                                            </div>                                              
                                        </div>
        
                                        <div class="row">
                                            <div class="col-6">                            
                                                <div class="form-group">
                                                    <label class="label my-2">Email</label>
                                                    <input type="email" class="form-control" name="email" value="{{ $order->email }}">
                                                </div>
                                            </div>
                                            <div class="col-6">                            
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
                                            <div class="col-6">                            
                                                <div class="form-group">
                                                    <label class="label my-2">Информация к заказу</label>
                                                    <textarea name="description" class="form-control">{{ $order->description }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-6">                            
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

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8 d-flex align-items-center">
                            <h4 class="card-title">Список товаров в заказе № {{ $order->id }}</h4>
                        </div>
                        <div class="col-4 d-flex justify-content-end">
                            <a href="{{ route("shopOrderItem.create") }}?shop_order_id={{ $order->id }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить</a>
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
                                <th>Сумма</th>
                                <th class="td-actions"></th>
                            </tr>
                            </thead>
                            <tbody>

                                @foreach ($order->ShopOrderItems as $orderItem) 
                                    <tr>
                                        <td class="td_editable">{{ $orderItem->name }}</td>
                                        <td class="td_editable">{{ $orderItem->quantity }}</td>
                                        <td class="td_editable">{{ $orderItem->price }}</td>
                                        <td>{{ $orderItem->price * $orderItem->quantity }}</td>
                                        <td class="td-actions">
                                            <a href="{{ route('shopOrderItem.edit', $orderItem->id) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                            <form action="{{ route('shopOrderItem.destroy', $orderItem->id) }}" method="POST" class="d-inline">
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

    
@endsection

@section('js')
    <script src="/assets/plugins/select/selectr.min.js"></script>          
    <script src="/assets/js/radioTab.js"></script>                              
    <script>new Selectr('.select');</script>
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
    </style>    
@endsection