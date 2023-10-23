@extends("admin.main")

@section('title', 'Редактирование скидки')

@section('breadcrumbs')
    <div class="page-title-box d-flex flex-column">
        <div class="float-start">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                @endforeach
                <li class="breadcrumb-item">Создание скидки</li>
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
        <div class="col-12">

            <div class="card" id="id_content">
                <form action="{{ route('shopDiscount.store') }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('POST')
                    
                    <div class="card-primary">
                        <div class="card-body tab-content">

                            <div  class="mb-3">
                                <label class="mb-1">Название скидки</label>
                                <input id="name" value="" type="text" name="name" class="form-control form-control-lg" placeholder="Название скидки" data-min="1"  data-max="255" data-required="1">
                                <div id="name_error" class="fieldcheck-error"></div>
                            </div>

                            <div class="row mb-3">

                                <div class="col-3">
                                    <label class="mb-1">Действует от</label>
                                    <input type="datetime-local" value="" name="start_datetime" class="form-control" placeholder="Действует от">
                                </div>
                                <div class="col-3">
                                    <label class="mb-1">Действует до</label>
                                    <input type="datetime-local" value="" name="end_datetime" class="form-control" placeholder="Действует до">
                                </div>

                            </div>

                            <div class="row mb-3">

                                <div class="col-3">
                                    <label class="mb-1">Величина скидки</label>
                                    <div class="d-flex">
                                        <div>
                                            <input type="text" value="" id="value" data-min="1" data-reg="[0-9\\.,:]" data-max="255" data-required="1" name="value" class="form-control" placeholder="Величина скидки">
                                            <div id="value_error" class="fieldcheck-error"></div>
                                        </div>
                                        
                                        <div>
                                            <select name="type" class="form-control">
                                                @foreach ($types as $key => $type)
                                                    <option value="{{ $key }}">{{ $type }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <label class="mb-1">&nbsp;</label>
                                    <div class="d-flex">
                                        <div class="form-check field-check-center">
                                            <div class="form-check form-switch form-switch-success">
                                                <input value="1" class="form-check-input" name="active" type="checkbox" id="active" checked>
                                                <label class="form-check-label" for="active">Активность</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                           <!-- 
                            <div class="alert alert-danger border-0" role="alert">
                                <strong>Применить ко всем товарам и модификациям по свойству типа - список!</strong>
                            </div>

                            <div class="row ">

                            </div>
                        -->

                        <div class="card mt-5">

                            <div class="card-header">
                                <h4>Фильтр по товарам</h4>
                                <p class="text-muted mb-0">Используйте фильтр, для поиска товаров, которым нужно применить скидку</p>
                            </div>

                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="mb-1">Название товара</label>
                                       
                                        <div class="input-group">
                                            <input type="text" name="name" class="form-control" placeholder="Поиск по названию товара" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                            <button class="btn btn-secondary" type="button" id="button-addon2"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-2">
                                        <label class="mb-1">Поиск по группе</label>
                                        <label class="mb-1">&nbsp;</label>
                                        <select name="shop_group_id" class="form-control">
                                            <option value="0">...</option>
                                            @php
                                                \App\Http\Controllers\ShopGroupController::showTreeGroupsAsOptions();
                                            @endphp
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <label class="mb-1">Поиск по свойству</label>
                                        <select class="form-control" name="total_list_id">
                                            <option>...</option>
                                            @foreach ($propertys as $property)
                                                <option value="{{ $property->shop_item_list_id }}">{{ $property->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
        
                                </div>
                                <div class="row mb-3">
                                    <div class="col-2"></div>
                                    <div class="col-2 d-none">
                                        <select class="form-control" name="total_list_value">
                                        </select>
                                    </div>
                                </div>

                                <div id="filter_result">
                                    <!--
                                    <div class="card bg-warning text-white">
                                        <div class="card-body">
                                            <blockquote class="card-bodyquote mb-0">
                                                Применить скидку к найденным товарам?
                                            </blockquote>
                                        </div>
                                    </div>
                                    <div class="items-applied">

                                        <table class="table table-bordered">
    
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex">
                                                            <div class="form-check form-switch form-switch-warning">
                                                                <input onclick="SwitchAll.init($(this))" id="apply_all" class="form-check-input" type="checkbox">
                                                            </div>
                                                            <label for="apply_all"><b>Применить/Отменить для всех</b></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @foreach (App\Models\ShopItem::limit(15)->get() as $ShopItem)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex">
                                                                <div class="form-check form-switch form-switch-warning">
                                                                    <input id="apply_{{$ShopItem->id}}" class="form-check-input" type="checkbox">
                                                                </div>
                                                                <label for="apply_{{$ShopItem->id}}">{{$ShopItem->ShopGroup->name}} / {{ $ShopItem->name }}({{$ShopItem->id}})</label>
                                                            </div>
                                                        </td>
                                                      
                                                    </tr>
                                                @endforeach
                                            </tbody>
    
                                        </table>
            
                                    </div>
                                    -->
                                </div>


                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <blockquote class="card-bodyquote mb-0">
                                            Товары, к которым применена скидка:
                                        </blockquote>
                                    </div>
                                </div>
                                <div class="items-applied">

                                    <table class="table table-bordered">

                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="d-flex">
                                                        <div class="form-check form-switch form-switch-success">
                                                            <input id="applied_all" onclick="SwitchAll.init($(this))" checked class="form-check-input" type="checkbox">
                                                        </div>
                                                        <label for="applied_all"><b>Применить/Отменить для всех</b></label>
                                                    </div>
                                                </td>
                                            </tr>
                                            @foreach (App\Models\ShopItem::limit(15)->get() as $ShopItem)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex">
                                                            <div class="form-check form-switch form-switch-success">
                                                                <input id="applied_{{$ShopItem->id}}" class="form-check-input" type="checkbox" checked="">
                                                            </div>
                                                            <label for="applied_{{$ShopItem->id}}">{{$ShopItem->ShopGroup->name}} / {{ $ShopItem->name }}({{$ShopItem->id}})</label>
                                                        </div>
                                                    </td>
                                                  
                                                </tr>
                                            @endforeach
                                        </tbody>

                                    </table>
        
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

    </div>

    
@endsection

@section("js")
    <script src="/assets/js/pages/shopDiscount.js"></script>
@endsection

@section("css")
    <style>
        .items-applied {
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
@endsection