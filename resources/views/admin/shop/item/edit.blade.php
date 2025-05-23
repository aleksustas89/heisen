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
                <li class="breadcrumb-item">Редактирование товара</li>
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
            
            <form action="{{ route('shop.shop-item.update', ['shop' => $shop->id, 'shop_item' => $shopItem->id]) }}" method="POST" id="formEdit" enctype="multipart/form-data">
                @csrf
                @method('PUT')

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

                            <div class="mb-3">
                                <label class="mb-1">Название товара</label>
                                <input id="name" value="{{ $shopItem->name }}" type="text" name="name" class="form-control form-control-lg" placeholder="Название товара" data-min="1"  data-max="255" data-required="1">
                                <div id="name_error" class="fieldcheck-error"></div>
                            </div>

                            <div class="mb-3">
                                <div class="mb-1">Тип товара</div>

                                <input value="{{ $shopItem->type }}" type="hidden" name="type">
                                
                                <ul class="nav" role="tablist">
                                    <li class="nav-item">
                                        <a onclick="$('[name=\'type\']').val(0)" @class(["btn btn-primary mx-1", "active" => $shopItem->type == 0]) data-bs-toggle="tab" href="#type-1" role="tab" aria-selected="false">Обычный</a>
                                    </li>
                                    <li class="nav-item">
                                        <a onclick="$('[name=\'type\']').val(1)" @class(["btn btn-warning mx-1", "active" => $shopItem->type == 1]) data-bs-toggle="tab" href="#type-2" role="tab" aria-selected="false">Ссылка</a>
                                    </li>                                                
                                </ul>
                            </div>

                            <div class="tab-content">
                                <div @class(["tab-pane", "active" => $shopItem->type == 0]) id="type-1">
                                    <div class="mb-3">
                                        <label class="mb-1">Группа</label>
                                        <select name="shop_group_id" class="form-control">
                                            <option value="0">...</option>
                                            @php
                                                \App\Http\Controllers\ShopGroupController::showTreeGroupsAsOptions($shopItem->shop_group_id);
                                            @endphp
                                        </select>
                                    </div>
        
                                    <div class="mb-5">
                                        <label class="mb-1">Дополнительные группы</label>
                                        <input type="text" name="shortcut_group_id" class="form-control" placeholder="Пожалуйста, введите еще хотя бы 2 символа">
                                        <div class="shortcut_groups position-absolute">
        
                                            @include("admin.shop.shortcuts", ["shopItem" => $shopItem])
                                        </div>
                                    </div>
        
                                    <div class="row mb-3">
                                        <div class="col-4">
                                            <label class="mb-1">Сортировка</label>
                                            <input type="text" value="{{ $shopItem->sorting }}" name="sorting" class="form-control" placeholder="Сортировка">
                                        </div>
                                        <div class="col-4">
                                            <label class="mb-1">Артикул</label>
                                            <input id="marking" value="{{ $shopItem->marking }}" type="text" name="marking" class="form-control" placeholder="Артикул">
                                        </div>
                                        <div class="col-4">
                                            <label class="mb-1">Путь</label>
                                            <input type="text" name="path" value="{{ $shopItem->path }}" class="form-control" placeholder="Путь">
                                        </div>
                                        
                                    
                                    </div>
        
                                    <div class="row mb-3">
                                        <div class="col-3 d-flex align-items-end">
        
                                            <div class="d-flex">
        
                                                <div class="form-check form-switch form-switch-success"> 
                                                    <input value="1" @if ($shopItem->active == 1) checked="" @endif class="form-check-input" name="active" type="checkbox" id="active">
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
                            
                                                        <div class="col-2 mb-2">
                                                            <input type="text" value="{{ $shopItem->price }}" name="price" class="form-control" placeholder="Цена" >
                                                        </div>
                                                        <div class="col-2 mb-2">
                                                            @if ($currencies)
                                                                <select name="shop_currency_id" class="form-select">
                                                                    @foreach ($currencies as $currency)
                                                                        @if ($currency->id == $shopItem->shop_currency_id)
                                                                            <option selected="selected" value="{{ $currency->id }}">{{ $currency->name }}</option>
                                                                        @else
                                                                            <option value="{{ $currency->id }}">{{ $currency->name }}</option> 
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                        </div>
                                                        <div class="col-3 mb-2 d-flex align-items-end">
                                                            <div class="d-flex">
                                                                <div class="form-check form-switch form-switch-purple mx-3">
                                                                    <input value="1" @if($shop->apply_items_price_to_modifications == 1) checked @endif class="form-check-input" name="apply_price_to_modifications" type="checkbox" id="apply_price_to_modifications">
                                                                    <label for="apply_price_to_modifications">
                                                                        Применить цену к модификациям
                                                                    </label>
                                                                </div>  
                                                            </div>
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
                                                        <div class="col-6 col-sm-3 mb-2">
                                                            <label class="mb-1">Вес, гр.</label>
                                                            <input type="text" value="{{ $shopItem->weight }}" name="weight" class="form-control" placeholder="Вес" >
                                                        </div>
                                                        <div class="col-6 col-sm-3 mb-2">
                                                            <label class="mb-1">Ширина, см.</label>
                                                            <input type="text" value="{{ $shopItem->width }}" name="width" class="form-control" placeholder="Ширина" >
                                                        </div>
                                                        <div class="col-6 col-sm-3 mb-2">
                                                            <label class="mb-1">Высота, см.</label>
                                                            <input type="text" value="{{ $shopItem->height }}" name="height" class="form-control" placeholder="Высота" >
                                                        </div>
                                                        <div class="col-6 col-sm-3 mb-2">
                                                            <label class="mb-1">Длина, см.</label>
                                                            <input type="text" value="{{ $shopItem->length }}" name="length" class="form-control" placeholder="Длина" >
                                                        </div>
                                                    </div>
        
                                                </div>
                                            </div>
        
                                        </div>
                                    </div>
                                </div>
                                <div @class(["tab-pane", "active" => $shopItem->type == 1]) id="type-2">
                                    <div class="mb-3">
                                        <label class="mb-1">Ссылка</label>
                                        <input value="{{ $shopItem->link }}" type="text" name="link" class="form-control form-control-lg" placeholder="Ссылка">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane" id="associated">

                            <div class="card">

                                <div class="card-header">
                                    <button type="button" onclick="Associated.showTab(0, {{ $shopItem->id }})" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-associated">
                                        <i class="fas fa-plus icon-separator"></i>Добавить сопутствующие
                                    </button>
                                </div>

                                <div class="card-body p-0">

                                    <div id="associated-content">
                                        @include("admin.shop.item.associated.list", ["shopItem" => $shopItem])
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane" id="images">

                            <div class=" mb-3">
                                <div action="{{ route('uploadShopItemImage', $shopItem->id) }}" class="dropzone" id="myDropzone">
                                    
                                </div>
                                <div class="max-size-label">Максимальный размер файла - {{ ini_get('upload_max_filesize') }}</div>
                            </div>

                            <div class="file-box-content mb-3 d-flex flex-wrap gallery" id="sortContainer">

                                @foreach ($images as $k => $image)
                                    <div class="file-box d-flex align-items-center justify-content-center sortable" id="{{ $k }}">

                                        <a href="javascript:void(0)" onclick="if(confirm('Вы действительно хотите удалить файл?')) {adminImage.remove('{{ route('deleteShopItemImage', [$shopItem->id, $k]) }}', $(this).parent())}">
                                            <i class="las la-times file-close-icon"></i>
                                        </a>

                                        <div class="text-center">
                                            <div class="file-box-image">
                                       
                              
                                                @if (!empty($image['image_small']))
                                                    <a @if (!empty($image['image_large'])) href="{{ $image['image_large'] }}" @endif class="lightbox"><img src="{{ $image['image_small'] }}"></a>
                                                @endif

                                                @if (!empty($image['file']))

                                                    <video width="100%" height="100px" preload="true" loop="loop" muted="muted" volume="0"> 
                                                        <source src="{{ $image['file'] }}"> 
                                                    </video>
                                                @endif

                                            </div>
                                        </div>  
{{-- 
                                        <div class="text-center">
                                            <div class="file-box-image">
                              
                                                @if (!empty($image['image_small']))
                                                    <a @if (!empty($image['image_large'])) href="{{ $image['image_large'] }}" @endif class="lightbox"><img src="{{ $image['image_small'] }}"></a>
                                                @endif
                                            </div>
                                        </div>                                                         --}}
                                    </div>
                                @endforeach
                                             
                            </div> 

                        </div>

                        <div class="tab-pane" id="description">

                            <div class="mb-3">
                                <label class="mb-1">Краткое описание товара</label>
                                <textarea type="text" name="description" class="form-control editor" placeholder="Описание группы">{{ $shopItem->description }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Описание товара</label>
                                <textarea type="text" name="text" class="form-control editor" placeholder="Описание группы">{{ $shopItem->text }}</textarea>
                            </div>

                        </div>

                        <div class="tab-pane" id="seo">

                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title text-uppercase">Seo заголовки</h3>
                                </div>
                                <div class="card-body" style="display: block;">
                                    
                                    <div class="form-group">
            
                                        <div class="mb-3">
                                            <label class="mb-1">Заголовок [Seo Title]</label>
                                            <input type="text" name="seo_title" value="{{ $shopItem->seo_title }}" class="form-control" placeholder="Заголовок страницы [Seo title]">
                                        </div>
            
                                        <div class="mb-3">
                                            <label class="mb-1">Описание [Seo Description]</label>
                                            <textarea name="seo_description" class="form-control" placeholder="Описание страницы [Seo description]">{{ $shopItem->seo_description }}</textarea>
                                        </div>
            
                                        <div class="mb-3">
                                            <label class="mb-1">Ключевые слова [Seo Keywords]</label>
                                            <input type="text" name="seo_keywords" value="{{ $shopItem->seo_keywords }}" class="form-control" placeholder="Ключевые слова [Seo Keywords]">
                                        </div>
                                      
                                    </div>

                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title text-uppercase">Каноникал</h3>
                                </div>
                                <div class="card-body" style="display: block;">
                                    
                                    <div class="form-group">
                                        @if ($shopItem->canonical > 0)
                                            <span class="canonical_value badge rounded-pill bg-primary"> 
                                                {{ $canonicalName }}
                                                <a href="javascript:void(0)" onclick="Canonical.delete()" class="mdi mdi-close"></a>
                                            </span>
                                        @endif

                                        <input @if($shopItem->canonical > 0) style="display:none;" @endif type="text" class="form-control" name="canonical_name" value="" />
                                        
                                        <input type="hidden" name="canonical" value="{{ $shopItem->canonical }}" />
                                    </div>
                                </div>
                            </div>

                            
                        </div>

                        <div class="tab-pane" id="modifications">

                            <div class="card">

                                <div class="card-header">
                                 
                                    <button type="button" onclick="adminModification.chooseProperties('{{ route('modification.create') }}?shop_item_id={{ $shopItem->id }}')" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-modifications">
                                        <i class="fas fa-plus icon-separator"></i>Создать модификации
                                    </button>
                                
                                </div>
                                <div id="modifications-content">
                                    @if (count($mShopItems) > 0)
                                        @include("admin.shop.modification.index", ["shopItems" => $mShopItems, "oShopItem" => $shopItem])
                                    @endif
                                </div>

                            </div>
                        </div>

                        @if (count($properties) > 0)
                            <div class="tab-pane properties-block" id="properties">
                                @foreach ($properties as $property)

                                    @switch($property->type)
                                        @case(0)
                                            @if (isset($property_value_strings[$property->id]))
                                                <div class="list-group-item">
                                                    @foreach ($property_value_strings[$property->id] as $k => $property_value_string) 
                                                        <div class="row mb-3 admin-item-property" data-property="{{ $property->id }}" data-id="{{ $k }}" id="admin-item-property-{{ $property->id }}-{{ $k }}">
                                                            <div class="col-10">
                                                                <label class="mb-1">{{ $property->name }}</label>
                                                                <input type="text" data-name="property_{{ $property->id }}[]" value="{{ $property_value_string }}" name="property_{{ $property->id }}_{{ $k }}" class="form-control" placeholder="{{ $property->name }}">
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
                                                    @endforeach
                                                </div>
                                            @else

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

                                            @endif
                                            
                                        @break
                                        @case(1)

                                            @if (isset($property_value_ints[$property->id]))
                                                <div class="list-group-item">
                                                    @foreach ($property_value_ints[$property->id] as $k => $property_value_int)
                                                        <div class="row mb-3 admin-item-property" data-property="{{ $property->id }}" data-id="{{ $k }}" id="admin-item-property-{{ $property->id }}-{{ $k }}">
                                                            <div class="col-10">
                                                                <label class="mb-1">{{ $property->name }}</label>
                                                                <input type="text" data-name="property_{{ $property->id }}[]" value="{{ $property_value_int }}" name="property_{{ $property->id }}_{{ $k }}" class="form-control" placeholder="{{ $property->name }}">
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
                                                    @endforeach
                                                </div>

                                            @else 

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

                                            @endif
                                            
                                        @break
                                        @case(2)

                                            @if (isset($property_value_floats[$property->id]))
                                                <div class="list-group-item">
                                                    @foreach ($property_value_floats[$property->id] as $k => $property_value_float)
                                                        <div class="row mb-3 admin-item-property" data-property="{{ $property->id }}" data-id="{{ $k }}" id="admin-item-property-{{ $property->id }}-{{ $k }}">
                                                            <div class="col-10">
                                                                <label class="mb-1">{{ $property->name }}</label>
                                                                <input data-required="1" data-name="property_{{ $property->id }}[]" data-reg="^[-+]?[0-9]{1,}\.{0,1}[0-9]*$" type="text" value="{{ $property_value_float }}" id="property_{{ $property->id }}_{{ $k }}" name="property_{{ $property->id }}_{{ $k }}" class="form-control" placeholder="{{ $property->name }}">
                                                                <div id="property_{{ $property->id }}_{{ $k }}_error" class="fieldcheck-error"></div>
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
                                                    @endforeach
                                                </div>

                                            @else

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

                                            @endif
                                        
                                        @break
                                        @case(3)

                                            @php
                                                $checkboxChecked = isset($property_value_ints[$property->id]) && $property_value_ints[$property->id][key($property_value_ints[$property->id])] == 1 ? 'checked="checked"' : '';
                                            @endphp

                                            <div class="list-group-item">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="form-check form-switch form-switch-success">
                                                            <input class="form-check-input" name="property_{{ $property->id }}" type="checkbox" id="property_{{ $property->id }}" {{ $checkboxChecked }}>
                                                            <label class="form-check-label" for="property_{{ $property->id }}">{{ $property->name }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        @break
                                        @case(4)

                                            @if (isset($property_value_ints[$property->id]))
                                                <div class="list-group-item">
                                                    @foreach ($property_value_ints[$property->id] as $propertyK => $propertyItem)
                                                        <div class="row mb-3 admin-item-property" data-property="{{ $property->id }}" data-id="{{ $propertyK }}" id="admin-item-property-{{ $property->id }}-{{ $propertyK }}">
                                                            <div class="col-10">
                                                                <label class="mb-1">{{ $property->name }}</label>
                                                                <select data-name="property_{{ $property->id }}[]" name="property_{{ $property->id }}_{{ $propertyK }}" class="form-select">
                                                                    <option value="">...</option>
                                                                    @foreach ($lists[$property->shop_item_list_id] as $key => $listItem)
                                                                        @php
                                                                            $isSelected = $propertyItem == $key ?  ' selected=selected ' : ''
                                                                        @endphp
                                                                        <option data-j="{{$key }}" data-l="{{$propertyK }}" {{ $isSelected }} value="{{ $key }}">{{ $listItem }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            @if ($property->multiple == 1)
                                                                <div class="col-2 d-flex align-items-end">
                                                                    <div>
                                                                        <button type="button" class="btn-upload btn btn-warning mt-1" onclick="adminProperty.copy($(this))"><i class="la la-plus"></i></button>
                                                                        <button type="button" class="btn-upload btn btn-danger mt-1 delete-property" onclick="adminProperty.delete($(this), '{{ route("deleteShopItemPropertyValue", ['shopItemProperty' => $property->id]) }}?id={{ $propertyK }}'); "><i class="la la-minus"></i></button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>

                                            @else

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
                                                                        <button type="button" class="btn-upload btn btn-danger mt-1 delete-property"  onclick="adminProperty.delete($(this));"><i class="la la-minus"></i></button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                @endif

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

            <div class="modal fade" id="modal-modifications" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title m-0">Создать модификации</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <div class="spinner-grow thumb-md text-secondary" role="status"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-de-secondary btn-sm close" data-bs-dismiss="modal">Закрыть</button>
                            @php
                                $route = route('modification.create') . "?window=1";
                            @endphp

                            <button type="button" class="btn btn-primary btn-sm" onclick="adminModification.createWindow('{{ $route }}')">Создать модификации</button>
                            
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<div class="modal fade" id="modal-associated" tabindex="-1" role="dialog" aria-hidden="true">
    <form class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0">Добавить сопутствующие товары</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div>
                    <input class="form-control" name="search_associated_by_name" placeholder="Поиск по наванию, id, артикулу" />
                    <hr>

                    <div>
                        <div class="text-center spinner-row d-none">
                            <div class="spinner-border thumb-md text-primary spinner-small" role="status"></div>
                        </div>
                        <div id="search_associated_by_name"></div>
                    </div>
                </div>

                <div id="modal-associated-result">
                    <div class="text-center">
                        <div class="spinner-border thumb-md text-primary spinner-small" role="status"></div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-de-secondary btn-sm close" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="Associated.saveChanges()">Сохранить изменения</button>
            </div>
        </div>
    </form>
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

@section("js")

    <script>
        var routeAddAssociated = '{{ route("addAssociated", $shopItem->id) }}',
            routeSaveAssociated = '{{ route("saveAssociated", $shopItem->id) }}',
            routeSearchShopItemFromAssosiated = '{{ route("searchShopItemFromAssosiated", $shopItem->id) }}';

    </script>

    <script src="/assets/plugins/tobii/tobii.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

    @php
        App\Services\Helpers\File::js('/assets/image.js');
        App\Services\Helpers\File::js('/assets/js/pages/shopItem.js');
    @endphp

    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.0/dropzone.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.0/dropzone.min.js"></script>

    <script>
        const tobii = new Tobii();

        var routeSearchCanonical = '{{ route("SearchCanonical", $shopItem->id) }}',
            routeGroupShortcut = '{{ route("getShortcutGroup") }}' + '?shop_group_id=' + {{ $shopItem->shop_group_id }},
            BadgeClasses = [@foreach($BadgeClasses as $k => $BadgeClasse)'{{$BadgeClasse}}'@if($k < count($BadgeClasses)-1),@endif @endforeach],
            routesortShopItemImages = '{{ route("sortingShopItemImages", $shopItem->id) }}',
            routeGetShopItemGallery = '{{ route("getShopItemGallery", $shopItem->id) }}';

        
        $(function() {
            $('#sortContainer').sortable({
                update: function() {
                    let aResult = [];

                    $('#sortContainer').find(".sortable").each(function(k) {
                        if ($(this).hasAttr("id")) {
                            aResult[k] = $(this).attr("id");
                        }
                    });

                    $.ajax({
                        url: routesortShopItemImages,
                        type: "GET",
                        data: {
                            "images": JSON.stringify(aResult) 
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                    });
                }
            });  
        });

        Dropzone.options.myDropzone = {

            acceptedFiles: "image/*,video/mp4,video/webm,video/ogg", // Уточненные типы
            maxFilesize: 100, // 100 МБ

            success: function (file, response) {
                console.log("Файл загружен:", response);
            },

            init: function() {

                this.on("success", function(file, responseText) {

                    this.removeFile(file);

                    delay(() => {
                        Gallery.update();
                    }, 1000);
                   
                });
                this.on("complete", function(file, responseText) {
                    this.removeFile(file);

                    delay(() => {
                        Gallery.update();
                    }, 1000);
                });
            }
        }


        var Gallery = {
            update: function() {

                $.ajax({
                    url: routeGetShopItemGallery,
                    type: "get",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "html",
                    success: function (data) {

                        $('.gallery').html(data);

                        const tobii = new Tobii()
                    },

                    error: function () {
            
                    },
                });
                
            }
        }
    </script>

    @php
    App\Services\Helpers\File::js('/assets/js/shortcut.js');
    App\Services\Helpers\File::js('/assets/js/pages/associated.js');
    App\Services\Helpers\File::js('/assets/js/pages/modifications.js');
    @endphp
@endsection