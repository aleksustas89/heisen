@extends("admin.main")

@section('title', 'Новая группа')

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
                <li class="breadcrumb-item">Новая группа</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">

            <div class="card" id="id_content">
                
                <form action="{{ route('shop.shop-group.store', ['shop' => $shop->id]) }}" method="POST" id="formEdit" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <div class="card">

                        <div class="p-2">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#main" data-bs-toggle="tab" role="tab">
                                        <i class="la la-home " title="Основные"></i>
                                    </a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="#seo" data-bs-toggle="tab" role="tab">SEO</a></li>
                            </ul>
                        </div>
    
                        <div class="card-primary">
                            <div class="card-body tab-content">

                                <div class="tab-pane active" id="main">

                                    <div class="mb-3">
                                        <label class="mb-1">Название</label>
                                        <input id="name" type="text" name="name" class="form-control form-control-lg" placeholder="Название группы" data-min="1"  data-max="255" data-required="1">
                                        <div id="name_error" class="fieldcheck-error"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="mb-1">Заголовок h1</label>
                                        <input type="text" name="h1" class="form-control" placeholder="Заголовок h1">
                                    </div>

                                    <div class="mb-3">
                                        <label class="mb-1">Краткое описание</label>
                                        <textarea type="text" name="description" class="form-control editor" placeholder="Описание группы"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="mb-1">Текст</label>
                                        <textarea type="text" name="text" class="form-control editor" placeholder="Описание группы"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="mb-1">Родительская группа</label>
                                        <select name="parent_id" class="form-control">
                                            <option value="0">...</option>
                                            @php
                                                \App\Http\Controllers\ShopGroupController::showTreeGroupsAsOptions(0);
                                            @endphp
                                        </select>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-xsl-6 mb-2 d-flex">
                                            <div class="input-group">
                                                <input type="file" name="image_large" class="form-control" id="image_large" aria-describedby="image_large" aria-label="Upload">
                                                <button class="btn btn-outline-secondary" type="button" id="image_large">Большое изображение</button>
                                            </div>
                                        </div>
                                        <div class="col-12 col-xsl-6 mb-2 d-flex">
                                            <div class="input-group">
                                                <input type="file" name="image_small" class="form-control" id="image_small" aria-describedby="image_small" aria-label="Upload">
                                                <button class="btn btn-outline-secondary" type="button" id="image_small">Малое изображение</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-lg-4">
                                            <label class="mb-1">Сортировка</label>
                                            <input type="text" name="sorting" class="form-control" placeholder="Сортировка">
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="mb-1">Путь</label>
                                            <input type="text" name="path" class="form-control" placeholder="Путь" data-min="2"  data-max="255" data-required="1">
                                        </div>
                                        <div class="col-lg-4">
                                            <label>&nbsp;</label>
                                            <div class="form-check field-check-center">
                                                <div class="form-check form-switch form-switch-success">
                                                    
                                                    <input value="1" checked class="form-check-input" name="active" type="checkbox" id="active" >
                                                    <label for="active">
                                                        Активность
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane" id="seo">
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
                            <div class="card-footer">
                                <input type="hidden" name="parent_id" value="{{$parent_id}}" />
                                <button type="submit" name="save" value="0" class="btn btn-primary" disabled>Сохранить</button>
                                <button type="submit" name="apply" value="1" class="btn btn-success" disabled>Применить</button>
                            </div>
                        </div>
                    </div>
                    
                </form>
                
            </div>
        </div>
    </div>

    
@endsection