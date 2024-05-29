@extends("admin.main")

@section('title', 'Редактирование Интернет-магазина')

@section('breadcrumbs')
    <div class="page-title-box d-flex flex-column">
        <div class="float-start">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                @endforeach
                <li class="breadcrumb-item">Настройки магазина</li>
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
        <div class="col-lg-12">

            <div class="card" id="id_content">

                <div class="p-2">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#main" data-bs-toggle="tab" role="tab">
                                <i class="la la-home " title="Основные"></i>
                            </a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#seo" data-bs-toggle="tab" role="tab">SEO</a></li>
                        <li class="nav-item"><a class="nav-link" href="#images" data-bs-toggle="tab" role="tab">Изображения</a></li>
                    </ul>
                </div>

                <form action="{{ route('shop.update', $shop['id']) }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body tab-content">

                        <div class="tab-pane active" id="main">

                            <div class="card">
                                
                                <div class="card-body">  

                                    <div class="row mb-3">
                                        <div class="col-12 col-sm-6">
                                            <label class="mb-1">Название интернет-магазина</label>
                                            <input type="text" name="name" value="{{ $shop['name'] }}" class="form-control" placeholder="Название интернет-магазина" data-min="2"  data-max="255" data-required="1">
                                            <div id="name_error" class="fieldcheck-error"></div>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <label class="mb-1">Путь</label>
                                            <input type="text" name="path" value="{{ $shop['path'] }}" class="form-control" placeholder="Путь" data-min="2"  data-max="255" data-required="1">
                                        </div>
                                        <div class="col-12 col-sm-3 align-items-end d-flex">
                                            <div class="form-check form-switch form-switch-success">
                                                <input {{ $shop->active == 1 ? 'checked' : '' }} value="1" class="form-check-input" name="active" type="checkbox" id="active">
                                                <label class="form-check-label" for="active">Активность</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-sm-3">
                                            <label class="mb-1">Кол-во элементов на странице</label>
                                            <input type="text" name="items_on_page" value="{{ $shop['items_on_page'] }}" class="form-control" placeholder="Кол-во элементов на странице">
                                        </div>
                                        
                                        <div class="col-12 col-sm-3">
                                            <label class="mb-1">Валюта магазина</label>
                                            <select id="currency_id" name="currency_id"  class="form-control">  
                                                <option value="0">...</option>  
        
                                                @foreach ($currencies as $currency)
                                                    <option {{ $currency->id == $shop["currency_id"]  ? 'selected="selected"' : '' }} value="{{ $currency->id }}">{{ $currency->name }}</option>  
                                                @endforeach                                    
                                            </select> 
                                        </div>

                                        <div class="col-12 col-sm-3">
                                            <label class="mb-1">E-mail</label>
                                            <input type="text" name="email" value="{{ $shop['email'] }}" class="form-control input-lg" placeholder="E-mail">
                                        </div>
                                        
        
                                    </div>

                                </div>                                
                            </div>


                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">                      
                                            <h4 class="card-title">Цены</h4>                 
                                        </div>                                                                         
                                    </div>                            
                                </div>
                                <div class="card-body">    
                                    <div class="row">
                                        <div class="col-12 ">

                                            <div class="form-check form-switch form-switch-success">
                                                <input {{ $shop->apply_items_price_to_modifications == 1 ? 'checked' : '' }} value="1" class="form-check-input" name="apply_items_price_to_modifications" type="checkbox" id="apply_items_price_to_modifications">
                                                <label class="form-check-label" for="apply_items_price_to_modifications">Применять цену товара модификациям</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>                               
                            </div>

                            

                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">                      
                                            <h4 class="card-title">Главная страница</h4>                  
                                        </div>                                                                         
                                    </div>                            
                                </div>
                                <div class="card-body">    
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
        
                                            <label class="mb-1">Тип вывода новинок на главной</label>
                                            <select name="new_items"  class="form-control">  
                                                <option value="0">...</option>  
        
                                                <option @if($shop['new_items'] == 1) selected @endif value="1">Показывать рамдомно товары со всего магазина</option>
                                                <option @if($shop['new_items'] == 2) selected @endif value="2">Показывать последние добавленные товары</option>                                   
                                            </select> 
                           
                                        </div>
                                        <div class="col-12 col-sm-6">
        
                                            <label class="mb-1">Количество товаров</label>
                                            <input type="text" name="new_items_count" value="{{ $shop['new_items_count'] }}" class="form-control" placeholder="Количество товаров">
        
                                        </div>
                                    </div>
                                </div>                               
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Описание интернет-магазина</label>
                                <textarea type="text" name="description" class="form-control editor" placeholder="Описание интернет-магазина">{{ $shop['description'] }}</textarea>
                            </div>
                        </div>

                        <div class="tab-pane" id="seo">

                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">                      
                                            <h4 class="card-title">Шаблоны группы</h4>                  
                                        </div>                                                                         
                                    </div>                            
                                </div>
                                <div class="card-body">    
                                    <div class="row">
                                        <div class="col-12">

                                            <div class="mb-3">
                                                <label class="mb-1">Шаблон title</label>
                                                <input type="text" name="seo_group_title_template" value="{{ $shop['seo_group_title_template'] }}" class="form-control" placeholder="Шаблон title">
                                            </div>
                
                                            <div class="mb-3">
                                                <label class="mb-1">Шаблон description</label>
                                                <textarea name="seo_group_description_template" class="form-control" placeholder="Шаблон description">{{ $shop['seo_group_description_template'] }}</textarea>
                                            </div>
                
                                            <div class="mb-3">
                                                <label class="mb-1">Шаблон keywords</label>
                                                <input type="text" name="seo_group_keywords_template" value="{{ $shop['seo_group_keywords_template'] }}" class="form-control" placeholder="Шаблон keywords">
                                            </div>
                                
                                        </div>
                                    </div>
                                </div>                               
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">                      
                                            <h4 class="card-title">Шаблоны товаров</h4>                  
                                        </div>                                                                         
                                    </div>                            
                                </div>
                                <div class="card-body">    
                                    <div class="row">
                                        <div class="col-12">

                                            <div class="mb-3">
                                                <label class="mb-1">Шаблон title</label>
                                                <input type="text" name="seo_item_title_template" value="{{ $shop['seo_item_title_template'] }}" class="form-control" placeholder="Шаблон title">
                                            </div>
                
                                            <div class="mb-3">
                                                <label class="mb-1">Шаблон description</label>
                                                <textarea name="seo_item_description_template" class="form-control" placeholder="Шаблон description">{{ $shop['seo_item_description_template'] }}</textarea>
                                            </div>
                
                                            <div class="mb-3">
                                                <label class="mb-1">Шаблон keywords</label>
                                                <input type="text" name="seo_item_keywords_template" value="{{ $shop['seo_item_keywords_template'] }}" class="form-control" placeholder="Шаблон keywords">
                                            </div>
                                
                                        </div>
                                    </div>
                                </div>                               
                            </div>



                        </div>

                        <div class="tab-pane" id="images">

                            <div class="row mb-3">
                                <div class="col-12 col-sm-6">
                                    <label class="mb-1">Максимальная ширина большого изображения товара</label>
                                    <input type="text" name="image_large_max_width" value="{{ $shop['image_large_max_width'] }}" class="form-control" placeholder="Максимальная ширина большого изображения товара">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="mb-1">Максимальная высота большого изображения товара</label>
                                    <input type="text" name="image_large_max_height" value="{{ $shop['image_large_max_height'] }}" class="form-control" placeholder="Максимальная высота большого изображения товара">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-sm-6">
                                    <label class="mb-1">Максимальная ширина малого изображения товара</label>
                                    <input type="text" name="image_small_max_width" value="{{ $shop['image_small_max_width'] }}" class="form-control" placeholder="Максимальная ширина малого изображения товара">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="mb-1">Максимальная высота малого изображения товара</label>
                                    <input type="text" name="image_small_max_height" value="{{ $shop['image_small_max_height'] }}" class="form-control" placeholder="Максимальная высота малого изображения товара">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-sm-6">

                                    <div class="form-check form-switch form-switch-success">
                                        <input {{ $shop->preserve_aspect_ratio == 1 ? 'checked' : '' }} value="1" class="form-check-input" name="preserve_aspect_ratio" type="checkbox" id="preserve_aspect_ratio">
                                        <label class="form-check-label" for="preserve_aspect_ratio">Сохранять пропорции изображения товара</label>
                                    </div>

                                </div>
                                <div class="col-12 col-sm-6">

                                    <div class="form-check form-switch form-switch-success">
                                        <input {{ $shop->preserve_aspect_ratio_small == 1 ? 'checked' : '' }} value="1" class="form-check-input" name="preserve_aspect_ratio_small" type="checkbox" id="preserve_aspect_ratio_small">
                                        <label class="form-check-label" for="preserve_aspect_ratio_small">Сохранять пропорции малого изображения товара</label>
                                    </div>

                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-sm-6">
                                    <label class="mb-1">Максимальная ширина большого изображения группы</label>
                                    <input type="text" name="group_image_large_max_width" value="{{ $shop['group_image_large_max_width'] }}" class="form-control" placeholder="Максимальная ширина большого изображения группы">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="mb-1">Максимальная высота большого изображения группы</label>
                                    <input type="text" name="group_image_large_max_height" value="{{ $shop['group_image_large_max_height'] }}" class="form-control" placeholder="Максимальная высота большого изображения группы">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-sm-6">
                                    <label class="mb-1">Максимальная ширина малого изображения группы</label>
                                    <input type="text" name="group_image_small_max_width" value="{{ $shop['group_image_small_max_width'] }}" class="form-control" placeholder="Максимальная ширина малого изображения группы">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="mb-1">Максимальная высота малого изображения группы</label>
                                    <input type="text" name="group_image_small_max_height" value="{{ $shop['group_image_small_max_height'] }}" class="form-control" placeholder="Максимальная высота малого изображения группы">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-sm-6">

                                    <div class="form-check form-switch form-switch-success">
                                        <input {{ $shop->preserve_aspect_ratio_group == 1 ? 'checked' : '' }} value="1" class="form-check-input" name="preserve_aspect_ratio_group" type="checkbox" id="preserve_aspect_ratio_group">
                                        <label class="form-check-label" for="preserve_aspect_ratio_group">Сохранять пропорции изображения группы</label>
                                    </div>
                   
                                </div>
                                <div class="col-12 col-sm-6">

                                    <div class="form-check form-switch form-switch-success">
                                        <input {{ $shop->preserve_aspect_ratio_group_small == 1 ? 'checked' : '' }} value="1" class="form-check-input" name="preserve_aspect_ratio_group_small" type="checkbox" id="preserve_aspect_ratio_group_small">
                                        <label class="form-check-label" for="preserve_aspect_ratio_group_small">Сохранять пропорции изображения группы</label>
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
    </div>

    
@endsection