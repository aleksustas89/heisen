@extends("admin.main")

@section('title', 'Создание статического фильтра')

@section('breadcrumbs')
    <div class="page-title-box d-flex flex-column">
        <div class="float-start">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                @endforeach
                <li class="breadcrumb-item">Редактирование</li>
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
                <form action="{{ route('shop.shop-filter.update', ['shop' => $shop->id, 'shop_filter' => $shopFilter->id]) }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('PUT')

                    <div class="p-2">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#main" data-bs-toggle="tab" role="tab">
                                    <i class="la la-home " title="Основные"></i>
                                </a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#description" data-bs-toggle="tab" role="tab">Описание</a></li>
                            <li class="nav-item"><a class="nav-link" href="#seo" data-bs-toggle="tab" role="tab">SEO</a></li>
                            <li class="nav-item"><a class="nav-link" href="#properties" data-bs-toggle="tab" role="tab">Свойства</a></li>
                        </ul>
                    </div>
                    
                    <div class="card-primary">
                        <div class="card-body tab-content">

                            <div class="tab-pane active" id="main">

                                <div  class="mb-3">
                                    <label class="mb-1">Группа</label>
                                    <select name="shop_group_id" class="form-control">
                                        <option value="0">...</option>
                                        @php
                                            \App\Http\Controllers\ShopGroupController::showTreeGroupsAsOptions($shopFilter->shop_group_id);
                                        @endphp
                                    </select>
                                </div>
    
                                <div class="mb-3">
                                    <label class="mb-1">Индексирование</label>
                                    <select name="indexing" class="form-select">  
                                        <option @if($shopFilter->indexing == 0) selected @endif value="0">index, follow</option>
                                        <option @if($shopFilter->indexing == 1) selected @endif value="1">noindex, nofollow</option> 
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <div>
                                        <ul class="nav gap-2" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <label for="static_url_checker_0" @class(['btn', 'btn-outline-boxberry', 'btn-sm', 'active' => $shopFilter->static_url == 0]) id="dinamic-url-tab" data-bs-toggle="tab" data-bs-target="#dinamic-url" type="button" role="tab" aria-controls="dinamic-url" aria-selected="true">Динамический url</label>
                                                <div class="v-hidden position-absolute">
                                                    <input @if($shopFilter->static_url == 0) checked @endif type="radio" id="static_url_checker_0" name="static_url_checker" value="0">
                                                </div>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <label for="static_url_checker_1" @class(['btn', 'btn-outline-cdek', 'btn-sm', 'active' => $shopFilter->static_url == 1]) id="static-url-tab" data-bs-toggle="tab" data-bs-target="#static-url" type="button" role="tab" aria-controls="static-url" aria-selected="false">Статический url</label>
                                                <div class="v-hidden position-absolute">
                                                    <input @if($shopFilter->static_url == 1) checked @endif type="radio" id="static_url_checker_1" name="static_url_checker" value="1">
                                                </div>
                                            </li>
                                        </ul>
    
                                        <div class="tab-content">      
                                            <div @class(['tab-pane', 'fade', 'show', 'active' => $shopFilter->static_url == 0]) id="dinamic-url" role="tabpanel" aria-labelledby="dinamic-url-tab">
                                                <div class="py-3">
                                                    <a target="_blank" href="https://{{ request()->getHost() }}{{ $shopFilter->url }}">{{ $shopFilter->url }}</a>
                                                </div>
                                            </div>
                                            <div @class(['tab-pane', 'fade', 'show', 'active' => $shopFilter->static_url == 1]) id="static-url" role="tabpanel" aria-labelledby="static-url-tab">
                                                <div class="py-3">
                                                    <input type="text" name="static_url" value="{{ $shopFilter->url }}" class="form-control" placeholder="Статический url">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="tab-pane" id="description">

                                <div class="mb-3">
                                    <label class="mb-1">Заголовок</label>
                                    <input type="text" name="h1" value="{{ $shopFilter->h1 }}" class="form-control" placeholder="Заголовок">
                                </div>

                                <div class="mb-3">
                                    <label class="mb-1">Сео заголовок</label>
                                    <input type="text" name="seo_h1" value="{{ $shopFilter->seo_h1 }}" class="form-control" placeholder="Сео заголовок">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="mb-1">Текст</label>
                                    <textarea type="text" name="text" class="form-control editor" placeholder="Текст">{{ $shopFilter->text }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="mb-1">Сео текст</label>
                                    <textarea type="text" name="seo_text" class="form-control editor" placeholder="Сео текст">{{ $shopFilter->seo_text }}</textarea>
                                </div>

                            </div>

                            <div class="tab-pane" id="seo">
                                <div class="mb-3">
                                    <label class="mb-1">Заголовок [Seo Title]</label>
                                    <input type="text" name="seo_title" value="{{ $shopFilter->seo_title }}" class="form-control" placeholder="Заголовок страницы [Seo title]">
                                </div>
    
                                <div class="mb-3">
                                    <label class="mb-1">Описание [Seo Description]</label>
                                    <textarea name="seo_description" class="form-control" placeholder="Описание страницы [Seo description]">{{ $shopFilter->seo_description }}</textarea>
                                </div>
    
                                <div class="mb-3">
                                    <label class="mb-1">Ключевые слова [Seo Keywords]</label>
                                    <input type="text" name="seo_keywords" value="{{ $shopFilter->seo_keywords }}" class="form-control" placeholder="Ключевые слова [Seo Keywords]">
                                </div>
                            </div>

                            <div class="tab-pane" id="properties">
                                <div class="form-group mb-3">
                                    <label class="mb-1">Сортировка</label>
                                    <select name="sorting" class="form-select">  
                                        <option value="0">...</option>
                                        <option @if($shopFilter->sorting == 1) selected @endif value="1">Сортировка - новые</option>
                                        <option @if($shopFilter->sorting == 2) selected @endif value="2">Сортировка - старые</option> 
                                    </select>
                                </div>
    
                                @if (count($shopItemProperties) > 0)
                                    <div class="properties-block">
                                        @foreach ($shopItemProperties as $k => $shopItemProperty)
    
                                            @php
                                                $ShopFilterPropertyValues = $shopFilter->ShopFilterPropertyValues()->where("property_id", $shopItemProperty->id)->get();
                                            @endphp
    
                                            @if (count($ShopFilterPropertyValues) > 0)
    
                                                @foreach ($ShopFilterPropertyValues as $ShopFilterPropertyValue)
                                                    <div class="list-group-item">
    
                                                        @if (!is_null($shopItemList = $shopItemProperty->shopItemList))
                                                            <div class="row mb-3 admin-item-property" data-property="{{ $shopItemProperty->id }}" id="admin-item-property-{{ $shopItemProperty->id }}-{{ $k }}">
                                                                <div class="col-10">
                                                                    <label class="mb-1">{{ $shopItemProperty->name }}</label>
    
                                                                    <select data-name="property_{{ $shopItemProperty->id }}[]" name="property_{{ $shopItemProperty->id }}_{{ $ShopFilterPropertyValue->id }}" class="form-select">
                                                                        <option value="">...</option>
    
                                                                        @foreach ($shopItemList->listItems as $ListItem)
                                                                            <option @if ($ShopFilterPropertyValue->value == $ListItem->id) selected @endif value="{{ $ListItem->id }}">{{ $ListItem->value }}</option>
                                                                        @endforeach
                                                                        
                                                                    </select>
                                                                </div>
    
                                                                <div class="col-2 d-flex align-items-end">
                                                                    <div>
                                                                        <button type="button" class="btn-upload btn btn-warning mt-1" onclick="adminProperty.copy($(this))"><i class="la la-plus"></i></button>
                                                                        <button type="button" class="btn-upload btn btn-danger mt-1 delete-property" onclick="adminProperty.delete($(this))"><i class="la la-minus"></i></button>
                                                                    </div>
                                                                </div>
    
                                                            </div>
                                                        @endif
                                                    
                                                    </div>
                                                @endforeach
    
                                            @else
    
                                                <div class="list-group-item">
    
                                                    @if (!is_null($shopItemList = $shopItemProperty->shopItemList))
                                                        <div class="row mb-3 admin-item-property" data-property="{{ $shopItemProperty->id }}" id="admin-item-property-{{ $shopItemProperty->id }}-{{ $k }}">
                                                            <div class="col-10">
                                                                <label class="mb-1">{{ $shopItemProperty->name }}</label>
    
                                                                <select data-name="property_{{ $shopItemProperty->id }}[]" name="property_{{ $shopItemProperty->id }}[]" class="form-select">
                                                                    <option value="">...</option>
    
                                                                    @foreach ($shopItemList->listItems as $ListItem)
                                                                        <option value="{{ $ListItem->id }}">{{ $ListItem->value }}</option>
                                                                    @endforeach
                                                                    
                                                                </select>
                                                            </div>
    
                                                            <div class="col-2 d-flex align-items-end">
                                                                <div>
                                                                    <button type="button" class="btn-upload btn btn-warning mt-1" onclick="adminProperty.copy($(this))"><i class="la la-plus"></i></button>
                                                                    <button type="button" class="btn-upload btn btn-danger mt-1 delete-property" onclick="adminProperty.delete($(this))"><i class="la la-minus"></i></button>
                                                                </div>
                                                            </div>
    
                                                        </div>
                                                    @endif
                                                
                                                </div>
                                            @endif
    
                                        @endforeach
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

    </div>

    
@endsection

@section("js")

    @php
    App\Services\Helpers\File::js('/assets/js/pages/shopItem.js');
    @endphp

@endsection

