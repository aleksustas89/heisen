@extends("admin.main")

@section('title', 'Настройки Информационной системы')

@section('breadcrumbs')
    <div class="page-title-box d-flex flex-column">
        <div class="float-start">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                @endforeach
                <li class="breadcrumb-item">Настройки Информационной системы</li>
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

                <form action="{{ route('informationsystem.update', $informationsystem->id) }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body tab-content">

                        <div class="tab-pane active" id="main">
                            <div class="card">
                                <div class="card-body">  
                                    
                                    <div class="mb-3">
                                        <label class="mb-1">Название</label>
                                        <input type="text" name="name" value="{{ $informationsystem->name }}" class="form-control" placeholder="Название">
                                    </div>

                                    <div class="row mb-3">
                                        
                                        <div class="col-12 col-sm-3">
                                            <label class="mb-1">Путь</label>
                                            <input type="text" name="path" value="{{ $informationsystem->path }}" class="form-control" placeholder="Путь" data-min="2"  data-max="255" data-required="1">
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <label class="mb-1">Кол-во элементов на странице</label>
                                            <input type="text" name="items_on_page" value="{{ $informationsystem->items_on_page }}" class="form-control" placeholder="Кол-во элементов на странице">
                                        </div>
                                        <div class="col-12 col-sm-3 align-items-end d-flex">
                                            <div class="form-check form-switch form-switch-success">
                                                <input {{ $informationsystem->active == 1 ? 'checked' : '' }} value="1" class="form-check-input" name="active" type="checkbox" id="active">
                                                <label class="form-check-label" for="active">Активность</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                        </div>

                        <div class="tab-pane" id="seo">

                            <div class="mb-3">
                                <label class="mb-1">Заголовок страницы [Seo Title]</label>
                                <input type="text" name="seo_title" value="{{ $informationsystem->seo_title }}" class="form-control" placeholder="Заголовок страницы [Seo Title]">
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Заголовок страницы [Seo Description]</label>
                                <input type="text" name="seo_description" value="{{ $informationsystem->seo_description }}" class="form-control" placeholder="Заголовок страницы [Seo Description]">
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Ключевые слова [Seo Keywords]</label>
                                <input type="text" name="seo_keywords" value="{{ $informationsystem->seo_keywords }}" class="form-control" placeholder="Ключевые слова [Seo Keywords]">
                            </div>

                        </div>

                        <div class="tab-pane" id="images">

                            <div class="row mb-3">
                                <div class="col-12 col-sm-6">
                                    <label class="mb-1">Максимальная ширина большого изображения элемента</label>
                                    <input type="text" name="image_large_max_width" value="{{ $informationsystem->image_large_max_width }}" class="form-control" placeholder="Максимальная ширина большого изображения товара">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="mb-1">Максимальная высота большого изображения элемента</label>
                                    <input type="text" name="image_large_max_height" value="{{ $informationsystem->image_large_max_height }}" class="form-control" placeholder="Максимальная высота большого изображения товара">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-sm-6">
                                    <label class="mb-1">Максимальная ширина малого изображения товара</label>
                                    <input type="text" name="image_small_max_width" value="{{ $informationsystem->image_small_max_width }}" class="form-control" placeholder="Максимальная ширина малого изображения товара">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="mb-1">Максимальная высота малого изображения товара</label>
                                    <input type="text" name="image_small_max_height" value="{{ $informationsystem->image_small_max_height }}" class="form-control" placeholder="Максимальная высота малого изображения товара">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-sm-6">

                                    <div class="form-check form-switch form-switch-success">
                                        <input {{ $informationsystem->preserve_aspect_ratio == 1 ? 'checked' : '' }} value="1" class="form-check-input" name="preserve_aspect_ratio" type="checkbox" id="preserve_aspect_ratio">
                                        <label class="form-check-label" for="preserve_aspect_ratio">Сохранять пропорции изображения товара</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">

                                    <div class="form-check form-switch form-switch-success">
                                        <input {{ $informationsystem->preserve_aspect_ratio_small == 1 ? 'checked' : '' }} value="1" class="form-check-input" name="preserve_aspect_ratio_small" type="checkbox" id="preserve_aspect_ratio_small">
                                        <label class="form-check-label" for="preserve_aspect_ratio_small">Сохранять пропорции малого изображения</label>
                                    </div>

                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-sm-6">
                                    <div class="form-check form-switch form-switch-success">
                                        <input {{ $informationsystem->convert_webp == 1 ? 'checked' : '' }} value="1" class="form-check-input" name="convert_webp" type="checkbox" id="convert_webp">
                                        <label class="form-check-label" for="convert_webp">Конвертировать изображения в webp</label>
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