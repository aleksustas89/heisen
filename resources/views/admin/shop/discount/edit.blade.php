@extends("admin.main")

@section('title', 'Редактирование скидки')

@section('breadcrumbs')
    <div class="page-title-box d-flex flex-column">
        <div class="float-start">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                @endforeach
                <li class="breadcrumb-item">Редактирование скидки "{{ $discount->name }}"</li>
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
                <form action="{{ route('shopDiscount.update', $discount->id) }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('PUT')
                    
                    <div class="card-primary">
                        <div class="card-body tab-content">

                            <div  class="mb-3">
                                <label class="mb-1">Название скидки</label>
                                <input id="name" value="{{ $discount->name }}" type="text" name="name" class="form-control form-control-lg" placeholder="Название скидки" data-min="1"  data-max="255" data-required="1">
                                <div id="name_error" class="fieldcheck-error"></div>
                            </div>

                            <div class="row mb-3">

                                <div class="col-3">
                                    <label class="mb-1">Действует от</label>
                                    <input type="datetime-local" value="{{ $discount->start_datetime }}" name="start_datetime" class="form-control" placeholder="Действует от">
                                </div>
                                <div class="col-3">
                                    <label class="mb-1">Действует до</label>
                                    <input type="datetime-local" value="{{ $discount->end_datetime }}" name="end_datetime" class="form-control" placeholder="Действует до">
                                </div>

                            </div>

                            <div class="row mb-3">

                                <div class="col-2">
                                    <label class="mb-1">Величина скидки</label>
                                    <div class="d-flex">
                                        <div>
                                            <input type="text" id="value" data-min="1" data-reg="[0-9\\.,:]" data-max="255" data-required="1" value="{{ $discount->value }}" name="value" class="form-control" placeholder="Величина скидки">
                                            <div id="value_error" class="fieldcheck-error"></div>
                                        </div>
                                        <div>
                                            <select name="type" class="form-control">
                                                @foreach ($types as $key => $type)
                                                    <option {{ $key == $discount->type ? 'selected' : '' }} value="{{ $key }}">{{ $type }}</option>
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
                                                @php
                                                $checked = $discount->active == 1 ? 'checked' : '';
                                                @endphp
                                                <input value="1" class="form-check-input" name="active" type="checkbox" id="active" {{ $checked }}>
                                                <label class="form-check-label" for="active">Активность</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-5" id="discountsFilter">

                                <div class="card-header">
                                    <h4>Фильтр по товарам</h4>
                                    <p class="text-muted mb-0">Используйте фильтр, для поиска товаров, которым нужно применить скидку</p>
                                </div>
    
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="mb-1">Название товара</label>
                                           
                                            <div class="input-group">
                                                <input type="text" name="shop_item_name" class="form-control" placeholder="Поиск по названию товара" aria-label="Recipient's username" aria-describedby="basic-addon2">
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
                                                <option value="0">...</option>
                                                @foreach ($propertys as $property)
                                                    <option data-list="{{ $property->shop_item_list_id }}" value="{{ $property->id }}">{{ $property->name }}</option>
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
                                    </div>
    
                                    @if (count($ShopItems) > 0)
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
                                                    @foreach ($ShopItems as $ShopItem)
                                                        @if ($oShopItem = $ShopItem->parentItemIfModification())
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                      
                                                                        <div class="form-check form-switch form-switch-success">
                                                                            <input value="{{ $ShopItem->id }}" name="applied[]" id="applied_{{$ShopItem->id}}" class="form-check-input" type="checkbox" checked="">
                                                                        </div>

                                                                        @foreach ($oShopItem->getImages(false) as $image)
                                                                            <div class="mx-2"><img src="{{ $image['image_small'] }}" alt="" height="80"></div>
                                                                        @endforeach 

                                                                        @php
                                                                        $ShopGroup = $oShopItem->ShopGroup;
                                                                        @endphp
                                                                        <label for="applied_{{$ShopItem->id}}">{{!is_null($ShopGroup) ? $ShopGroup->name : ''}} / {{ $ShopItem->name }}({{$ShopItem->id}})</label>
                                                                    </div>
                                                                </td>
                                                            
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
        
                                            </table>
                
                                        </div>
                                    @endif
    
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