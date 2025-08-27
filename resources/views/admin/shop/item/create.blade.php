@extends("admin.main")

@section('title', 'Новый товар')

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
                <li class="breadcrumb-item">Новый товар</li>
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
            
            <form action="{{ route('shop.shop-item.store', ['shop' => $shop->id]) }}" method="POST" id="formEdit" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="p-2">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#main" data-bs-toggle="tab" role="tab">
                                <i class="la la-home " title="Основные"></i>
                            </a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#images" data-bs-toggle="tab" role="tab">Изображения</a></li>
                        <li class="nav-item"><a class="nav-link" href="#description" data-bs-toggle="tab" role="tab">Описание</a></li>
                        <li class="nav-item"><a class="nav-link" href="#associated" data-bs-toggle="tab" role="tab">Сопутствующие</a></li>
                        <li class="nav-item"><a class="nav-link" href="#modifications" data-bs-toggle="tab" role="tab">Модификации</a></li>
                        <li class="nav-item"><a class="nav-link" href="#seo" data-bs-toggle="tab" role="tab">SEO</a></li>
                        @if (count($properties) > 0)
                            <li class="nav-item"><a class="nav-link" href="#properties" data-bs-toggle="tab" role="tab">Свойства</a></li>
                        @endif
                    </ul>
                </div>

                <div class="card-primary">
                    <div class="card-body tab-content">

                        <div class="tab-pane active" id="main">

                            <div  class="mb-3">
                                <label class="mb-1">Название товара</label>
                                <input id="name" type="text" name="name" class="form-control form-control-lg" placeholder="Название товара" data-min="1"  data-max="255" data-required="1">
                                <div id="name_error" class="fieldcheck-error"></div>
                            </div>

                            <div class="mb-3">
                                <div class="mb-1">Тип товара</div>

                                <input value="0" type="hidden" name="type">
                                
                                <ul class="nav" role="tablist">
                                    <li class="nav-item">
                                        <a onclick="$('[name=\'type\']').val(0)" @class(["btn btn-primary mx-1", "active"]) data-bs-toggle="tab" href="#type-1" role="tab" aria-selected="false">Обычный</a>
                                    </li>
                                    <li class="nav-item">
                                        <a onclick="$('[name=\'type\']').val(1)" @class(["btn btn-warning mx-1"]) data-bs-toggle="tab" href="#type-2" role="tab" aria-selected="false">Ссылка</a>
                                    </li>                                                
                                </ul>
                            </div>

                            <div class="tab-content">
                                <div @class(["tab-pane", "active"]) id="type-1">
                                    <div class="mb-3">
                                        <label class="mb-1">Группа</label>
                                        <select name="shop_group_id" class="form-control">
                                            <option value="0">...</option>
                                            @php
                                                \App\Http\Controllers\ShopGroupController::showTreeGroupsAsOptions($parent_id);
                                            @endphp
                                        </select>
                                    </div>
        
                                    <div class="mb-5">
                                        <label class="mb-1">Дополнительные группы</label>
                                        <input type="text" name="shortcut_group_id" class="form-control" placeholder="Пожалуйста, введите еще хотя бы 2 символа">
                                        <div class="shortcut_groups position-absolute"></div>
                                    </div>
        
                                    <div class="row mb-3">
                                        <div class="col-4">
                                            <label class="mb-1">Сортировка</label>
                                            <input type="text" name="sorting" class="form-control" placeholder="Сортировка">
                                        </div>
                                        <div class="col-4">
                                            <label class="mb-1">Артикул</label>
                                            <input id="marking" type="text" name="marking" class="form-control" placeholder="Артикул">
                                        </div>
                                        <div class="col-4">
                                            <label class="mb-1">Путь</label>
                                            <input type="text" name="path" class="form-control" placeholder="Путь" >
                                        </div>
                                    
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 d-flex align-items-end">
        
                                            <div class="d-flex">
        
                                                <div class="form-check form-switch form-switch-success">
                                                    <input value="1" class="form-check-input" name="active" type="checkbox" id="active" checked="">
                                                    <label for="active">
                                                        Активность
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="row mb-3">
                                        <div class="col-12">
        
                                            <div class="card card-warning">
                                                <div class="card-header">
                                                    <h3 class="card-title">Цена</h3>
                                                </div>
                                                <div class="card-body" style="display: block;">
                                                    
                                                    <div class="row form-group">
                            
                                                        <div class="col-lg-2">
                                                            <input type="text" name="price" class="form-control" placeholder="Цена" >
                                                        </div>
                                                        <div class="col-lg-2">
                                                            @if ($currencies)
                                                                <select name="shop_currency_id" class="form-select">
                                                                    @foreach ($currencies as $currency)
                                                                        @if ($currency->default == 1)
                                                                            <option selected="selected" value="{{ $currency->id }}">{{ $currency->name }}</option>
                                                                        @else
                                                                            <option value="{{ $currency->id }}">{{ $currency->name }}</option> 
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                        </div>
                                                    </div>
        
                                                </div>
                                            </div>
        
                                        </div>
                                    </div>
        
                                    <div class="row mb-3">
                                        <div class="col-12">
        
                                            <div class="card card-warning">
                                                <div class="card-header">
                                                    <h3 class="card-title">Габариты</h3>
                                                </div>
                                                <div class="card-body" style="display: block;">
                                                    
                                                    <div class="row form-group">
                                                        <div class="col-6 col-sm-3">
                                                            <label class="mb-1">Вес, гр.</label>
                                                            <input type="text" name="weight" class="form-control" placeholder="Вес" >
                                                        </div>
                                                        <div class="col-6 col-sm-3">
                                                            <label class="mb-1">Ширина, см.</label>
                                                            <input type="text" name="width" class="form-control" placeholder="Ширина" >
                                                        </div>
                                                        <div class="col-6 col-sm-3">
                                                            <label class="mb-1">Высота, см.</label>
                                                            <input type="text" name="height" class="form-control" placeholder="Высота" >
                                                        </div>
                                                        <div class="col-6 col-sm-3">
                                                            <label class="mb-1">Длина, см.</label>
                                                            <input type="text" name="length" class="form-control" placeholder="Длина" >
                                                        </div>
                                                    </div>
        
                                                </div>
                                            </div>
        
                                        </div>
                                    </div>
                                </div>
                                <div @class(["tab-pane"]) id="type-2">
                                    <div class="mb-3">
                                        <label class="mb-1">Ссылка</label>
                                        <input value="" type="text" name="link" class="form-control form-control-lg" placeholder="Ссылка">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane" id="images">

                            <div class="upload-call">
                                <div class="wrap">
                                    Сохраните товар, чтобы добавлять изображения!
                                </div>
                            </div>
                            

                        </div>

                        <div class="tab-pane" id="description">

                            <div class="mb-3">
                                <label class="mb-1">Краткое описание товара</label>
                                <textarea type="text" name="description" class="form-control editor" placeholder="Описание группы"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Описание товара</label>
                                <textarea type="text" name="text" class="form-control editor" placeholder="Описание группы"></textarea>
                            </div>

                        </div>

                        <div class="tab-pane" id="associated">

                            <div class="mb-3">
                                Сохраните товар, чтобы добавлять сопутствующие!
                            </div>
                        </div>

                        <div class="tab-pane" id="modifications">

                            <div class="mb-3">
                                Сохраните товар, чтобы добавлять модификации!
                            </div>
                        </div>

                        <div class="tab-pane" id="seo">

                            <div class="card card-warning">
                                <div class="card-header">
                                    <h3 class="card-title text-uppercase">Seo заголовки</h3>
                                </div>
                                <div class="card-body" style="display: block;">
                                    
                                    <div class="form-group">
            
                                        <div class="mb-3">
                                            <label class="mb-1">Заголовок [Seo Title]</label>
                                            <input type="text" name="seo_title" value="" class="form-control" placeholder="Заголовок страницы [Seo title]">
                                        </div>
            
                                        <div class="mb-3">
                                            <label class="mb-1">Описание [Seo Description]</label>
                                            <textarea name="seo_description" class="form-control" placeholder="Описание страницы [Seo description]"></textarea>
                                        </div>
            
                                        <div class="mb-3">
                                            <label class="mb-1">Ключевые слова [Seo Keywords]</label>
                                            <input type="text" name="seo_keywords" value="" class="form-control" placeholder="Ключевые слова [Seo Keywords]">
                                        </div>
                                      
                                    </div>

                                </div>
                            </div>

                        </div>

                        @if (count($properties) > 0)

                            <div class="tab-pane properties-block" id="properties">
                                @foreach ($properties as $property)

                                    @switch($property->type)
                                        @case(0)
               
                                            <div class="list-group-item">
                                                <div class="row mb-3 admin-item-property">
                                                    <div class="col-10">
                                                        <label class="mb-1">{{ $property->name }}</label>
                                                        <input type="text" data-name="property_{{ $property->id }}[]" name="property_{{ $property->id }}[]" class="form-control" placeholder="{{ $property->name }}">
                                                    </div>
    
                                                    @if ($property->multiple == 1)
                                                        <div class="col-2 d-flex align-items-end">
                                                            <div>
                                                                <button type="button" class="btn-upload btn btn-warning mt-1" onclick="adminProperty.copy($(this))"><i class="la la-plus"></i></button>
                                                                <button type="button" class="btn-upload btn btn-danger mt-1 delete-property" onclick="adminProperty.delete($(this))"><i class="la la-minus"></i></button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                        @break
                                        @case(1)

                                            <div class="list-group-item">
                                                <div class="row mb-3 admin-item-property">
                                                    <div class="col-10">
                                                        <label class="mb-1">{{ $property->name }}</label>
                                                        <input type="text" data-name="property_{{ $property->id }}[]" name="property_{{ $property->id }}[]" class="form-control" placeholder="{{ $property->name }}">
                                                    </div>
    
                                                    @if ($property->multiple == 1)
                                                        <div class="col-2 d-flex align-items-end">
                                                            <div>
                                                                <button type="button" class="btn-upload btn btn-warning mt-1" onclick="adminProperty.copy($(this))"><i class="la la-plus"></i></button>
                                                                <button type="button" class="btn-upload btn btn-danger mt-1 delete-property" onclick="adminProperty.delete($(this))"><i class="la la-minus"></i></button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                        @break
                                        @case(2)

                                            <div class="list-group-item">
                                                <div class="row mb-3 admin-item-property">
                                                    <div class="col-10">
                                                        <label class="mb-1">{{ $property->name }}</label>
                                                        <input data-required="1" data-name="property_{{ $property->id }}[]" data-reg="^[-+]?[0-9]{1,}\.{0,1}[0-9]*$" type="text" id="property_{{ $property->id }}" name="property_{{ $property->id }}[]" class="form-control" placeholder="{{ $property->name }}">
                                                        <div id="property_{{ $property->id }}_error" class="fieldcheck-error"></div>
                                                    </div>
    
                                                    @if ($property->multiple == 1)
                                                        <div class="col-2 d-flex align-items-end">
                                                            <div>
                                                                <button type="button" class="btn-upload btn btn-warning mt-1" onclick="adminProperty.copy($(this))"><i class="la la-plus"></i></button>
                                                                <button type="button" class="btn-upload btn btn-danger mt-1 delete-property" onclick="adminProperty.delete($(this))"><i class="la la-minus"></i></button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        
                                        @break
                                        @case(3)

                                            <div class="list-group-item">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <div class="form-check form-switch form-switch-success">
                                                            <input class="form-check-input" name="property_{{ $property->id }}" type="checkbox" id="property_{{ $property->id }}">
                                                            <label class="form-check-label" for="property_{{ $property->id }}">{{ $property->name }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        @break
                                        @case(4)
                                            @if (isset($lists[$property->shop_item_list_id]))
                                                <div class="list-group-item">
                                                    <div class="row mb-3 admin-item-property">
                                                        <div class="col-10">
                                                            <label class="mb-1">{{ $property->name }}</label>
                                                            <select data-name="property_{{ $property->id }}[]" name="property_{{ $property->id }}[]" class="form-select">
                                                                <option value="">...</option>
                                                                @foreach ($lists[$property->shop_item_list_id] as $key => $listItem)
                                                                    <option value="{{ $key }}">{{ $listItem }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @if ($property->multiple == 1)
                                                            <div class="col-2 d-flex align-items-end">
                                                                <div>
                                                                    <button type="button" class="btn-upload btn btn-warning mt-1" onclick="adminProperty.copy($(this))"><i class="la la-plus"></i></button>
                                                                    <button type="button" class="btn-upload btn btn-danger mt-1 delete-property"  onclick="adminProperty.delete($(this))"><i class="la la-minus"></i></button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                            @endif
                                        @break
                                        @default
                                            
                                    @endswitch

                                @endforeach
                            </div>

                        @endif

                    </div>
                    <div class="card-footer">
                        <button type="submit" name="save" value="0" class="btn btn-primary">Сохранить</button>
                        <button type="submit" name="apply" value="1" class="btn btn-success">Применить</button>
                    </div>
                </div>
                
            </form>
            
        </div>
    </div>
</div>

@endsection

@section("css")
    <link href="/assets/plugins/tobii/tobii.min.css" rel="stylesheet" type="text/css" />
    
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <style type="text/css">
        div.sortable {float: left;}
       .max-size-label {text-align: right; font-size: 12px;}
    </style>

@endsection

@section ("js")

    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

    @php
        App\Services\Helpers\File::js('/assets/image.js');
        App\Services\Helpers\File::js('/assets/js/pages/shopItem.js');
    @endphp

    <script>
        var routeGroupShortcut = '{{ route("getShortcutGroup") }}' + '?shop_group_id=' + {{ $parent_id }},
            BadgeClasses = [@foreach($BadgeClasses as $k => $BadgeClasse)'{{$BadgeClasse}}'@if($k < count($BadgeClasses)-1),@endif @endforeach];
    </script>

    <script src="/assets/image.js"></script>
    <script src="/assets//js/pages/shopItem.js"></script>
    <script src="/assets/pages/file-upload.init.js"></script>

@endsection