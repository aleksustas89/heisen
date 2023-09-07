@extends("admin.main")

@section('title', 'Создание заказа')

@section('breadcrumbs')
    <div class="page-title-box d-flex flex-column">
        <div class="float-start">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    @if (!empty($breadcrumb["url"]))
                        <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                    @else 
                        <li class="breadcrumb-item">{{ $breadcrumb["name"] }}</li>
                    @endif
                @endforeach
                <li class="breadcrumb-item">Создание заказа</li>
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
                <form action="{{ route('shopOrder.store') }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('POST')
                    
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
                                                <input type="text" disabled name="id" class="form-control" placeholder="Номер заказа">
                                            </div>
            
                                            <div class="col-4"> 
                                                <label class="mb-1 my-2">Дата заказа</label>
                                                <input type="datetime-local" name="datetime" class="form-control" placeholder="Дата заказа">
                                            </div>
            
                                            <div class="col-4"> 
                                                <label class="mb-1 my-2">Клиент</label>
            
                                                <select class="select" name="client_id">
                                                    <option value="0">...</option>
                                                    @foreach (App\Models\Client::get() as $Client)
                                                        <option value="{{ $Client->id }}">{{ $Client->name }} [{{ $Client->id }}]</option>
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
                                                    <input type="text" disabled name="sum" value="" class="form-control" placeholder="Сумма заказа">
                                                </div>
                                                <div class="d-inline-block">
                                                    <select class="form-control" name="shop_currency_id">
                                                        @foreach (App\Models\ShopCurrency::orderBy("default", "Desc")->get() as $ShopCurrency) 
                                                            <option value="{{ $ShopCurrency->id }}">{{ $ShopCurrency->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            
                                            </div>
                                            <div class="col-7"> 
                                                <label class="mb-1 my-2">Способ оплаты</label>
                                                <select class="form-control" name="shop_payment_system_id">
                                                    <option value="0">...</option>
                                                    @foreach (App\Models\ShopPaymentSystem::get() as $ShopPaymentSystem) 
                                                    <option value="{{ $ShopPaymentSystem->id }}">{{ $ShopPaymentSystem->name }}</option>
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

                                            <div class="col-6"> 
                                                <label class="mb-1 my-2">Способ доставки</label>
                                                <select class="form-control" name="shop_delivery_id">
                                                    <option value="0">...</option>
                                                    @foreach (App\Models\ShopDelivery::get() as $ShopShopDelivery) 
                                                    <option value="{{ $ShopShopDelivery->id }}">{{ $ShopShopDelivery->name }}</option>
                                                    @endforeach
                                                </select>
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
                                                    <label class="form-label my-2">Фамилия</label>
                                                    <input type="text" class="form-control" name="surname">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label class="form-label my-2">Имя</label>
                                                    <input type="text" class="form-control" name="name">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label class="form-label my-2">Отчество</label>
                                                    <input type="text" class="form-control" name="patronymic">
                                                </div>
                                            </div>                                              
                                        </div>
        
                                        <div class="row">
                                            <div class="col-6">                            
                                                <div class="form-group">
                                                    <label class="form-label my-2">Email</label>
                                                    <input type="email" name="email" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-6">                            
                                                <div class="form-group">
                                                    <label class="form-label my-2">Телефон</label>
                                                    <input type="text" name="phone" class="form-control">
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
                                                    <label class="form-label my-2">Информация к заказу</label>
                                                    <textarea name="description" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-6">                            
                                                <div class="form-group">
                                                    <label class="form-label my-2">Информация к доставке</label>
                                                    <textarea name="delivery_information" class="form-control"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                        <div class="col-12 d-flex align-items-center">
                            Сохраните заказ, чтобы добавлять товары к заказу
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
@endsection

@section('js')
    <script src="/assets/plugins/select/selectr.min.js"></script>                                   
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