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
        <div class="col-12 col-lg-6">

            <div class="card" id="id_content">
                <form action="{{ route('shop-order.store') }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
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
                                            <div class="col-12 col-sm-4"> 
                                                <label class="mb-1 my-2">Номер заказа</label>
                                                <input type="text" disabled name="id" class="form-control" placeholder="Номер заказа">
                                            </div>
            
                                            <div class="col-12 col-sm-4"> 
                                                <label class="mb-1 my-2">Дата заказа</label>
                                                <input type="datetime-local" name="datetime" class="form-control" placeholder="Дата заказа">
                                            </div>
            
                                            <div class="col-12 col-sm-4"> 
                                                <label class="mb-1 my-2">Клиент</label>
            
                                                <select class="select" id="client_id" name="client_id">
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
                                            <div class="col-12 col-sm-4"> 
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
                                            <div class="col-12 col-sm-4"> 
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
                                        <i class="las la-balance-scale bg-soft-primary"></i>
                                    </div>
                                    <div class="activity-info-text">
                                        <div class="row mb-3">
                                            <div class="col-6 col-sm-4"> 
                                                <label class="mb-1 my-2">Вес</label>
                                                <input type="text" name="weight" class="form-control" placeholder="Вес заказа">
                                            </div>
                                            <div class="col-6 col-sm-4"> 
                                                <label class="mb-1 my-2">Ширина</label>
                                                <input type="text" name="width" class="form-control" placeholder="Ширина">
                                            </div>
                                            <div class="col-6 col-sm-4"> 
                                                <label class="mb-1 my-2">Высота</label>
                                                <input type="text" name="height" class="form-control" placeholder="Высота">
                                            </div>
                                            <div class="col-6 col-sm-4"> 
                                                <label class="mb-1 my-2">Глубина</label>
                                                <input type="text" name="length" class="form-control" placeholder="Глубина">
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
                                                       
                                                        <input id="delivery-{{ $ShopDelivery->id }}" onclick="radioTab.click($(this))" value="{{ $ShopDelivery->id }}" type="radio" class="btn-check" name="shop_delivery_id" autocomplete="off">
                                                        <label class="btn btn-outline-{{ $ShopDelivery->color }} btn-sm" for="delivery-{{ $ShopDelivery->id }}">{{ $ShopDelivery->name }}</label>
                                                    @endforeach

                                                    <div class="tab-content">        
                                                        <div @class(["tab-pane", "p-3"]) id="tab-delivery-7" role="tabpanel">
                                                            <div class="row">  
                                                                
                                                                <div class="form-group">
                                                                    <label class="label my-2">Коробка</label>
                                                                    <select id="cdek_dimension_id" name="cdek_dimension_id">
                                                                        <option value="0">Без коробки</option>
                                                                        @foreach ($CdekDimensions as $CdekDimension)
                                                                            <option value="{{ $CdekDimension->id }}">{{ intval($CdekDimension->length) }}x{{ intval($CdekDimension->width) }}x{{ intval($CdekDimension->height) }}, до {{$CdekDimension->weight / 1000}} кг</option>
                                                                        @endforeach
                                                                    </select>                                                       
                                                                </div> 
                                                                
                                                                <div class="form-group">
                                                                    <label class="label my-2">Город </label>
                                                    
                                                                    <input type="text" class="form-control" name="delivery_7_city">
                                                                    <input type="hidden" name="delivery_7_city_id">
                                                                </div>
                                                                            
                                                                <input type="hidden" name="delivery_7_delivery_type">

                                                                <div class="form-group my-3">
                                                                    <input data-id="11" id="delivery-11" data-hidden="delivery_7_delivery_type" onclick="radioTab.click($(this))" value="11" type="radio" class="btn-check" name="delivery_field_14" autocomplete="off">
                                                                    <label class="btn btn-outline-cdek btn-sm" for="delivery-11">Отделение</label>

                                                                    <input data-id="15" id="delivery-15" data-hidden="delivery_7_delivery_type" onclick="radioTab.click($(this))" value="15" type="radio" class="btn-check" name="delivery_field_14" autocomplete="off">
                                                                    <label class="btn btn-outline-cdek btn-sm" for="delivery-15">Курьер</label>

                                                                    <div class="tab-content">
                                                                                                                                                                            
                                                                        <div @class(["tab-pane", "p-3"]) id="tab-delivery-11" role="tabpanel">
                                                                            <div class="row">
                                                                                <div class="form-group">
                                                                                    <label class="label my-2">Отделение </label>
                                                                                    <input type="text" class="form-control" name="delivery_7_office">
                                                                                    <input type="hidden" value="" name="delivery_7_office_id">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                                                                                                                                                                                                                          
                                                                        <div @class(["tab-pane", "p-3"]) id="tab-delivery-15" role="tabpanel">
                                                                            <div class="row">
                                                                                <div class="form-group">
                                                                                    <label class="label my-2">Курьер </label>
                                                                                    <input type="text" class="form-control" value="" name="delivery_7_courier">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>                                                                                  
                                                            </div>

                                                        </div>

                                                        <div @class(["tab-pane", "p-3"]) id="tab-delivery-1" role="tabpanel">
                                                            <div class="row">                                 
                                                                <div class="form-group">
                                                                    <label class="label my-2">Город </label>
                                                                    <input type="text" class="form-control" value="" name="delivery_1_city">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="label my-2">Отделение </label>
                                                                    <input type="text" class="form-control" value="" name="delivery_1_office">
                                                                </div>                                                                            
                                                            </div>
                                                        </div>  
                                                        
                                                        <div @class(["tab-pane", "p-3"]) id="tab-delivery-8" role="tabpanel">

                                                            <div id="boxberry_result">
                                                            </div>
                                                            <input type="hidden" class="form-control" value="" name="delivery_8_city">
                                                            <input type="hidden" class="form-control" value="" name="delivery_8_address">

                                                            <div class="row form-group my-1">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <p><a class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#boxberryModal" href="javascript:void(0)">Выбрать</a></p>
                                                                    </div>
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
                                                    <label class="form-label my-2">Фамилия</label>
                                                    <input type="text" class="form-control" name="surname">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label class="form-label my-2">Имя</label>
                                                    <input type="text" class="form-control" name="name">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label class="form-label my-2">Отчество</label>
                                                    <input type="text" class="form-control" name="patronymic">
                                                </div>
                                            </div>                                              
                                        </div>
        
                                        <div class="row">
                                            <div class="col-12 col-sm-12">                            
                                                <div class="form-group">
                                                    <label class="form-label my-2">Email</label>
                                                    <input type="email" name="email" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12">                            
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
                                            <div class="col-12 col-sm-4">                            
                                                <div class="form-group">
                                                    <label class="form-label my-2">Информация к заказу</label>
                                                    <textarea name="description" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">                            
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

        <div class="col-12 col-lg-6">
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
    <script>
        var create_order_route = '{{ route("createCdekOrder") }}';
        new Selectr('#cdek_dimension_id');
        new Selectr('#client_id');
    </script>
    
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
    </style>    
@endsection