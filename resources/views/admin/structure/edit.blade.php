@extends("admin.main")

@section('title', 'Редактирование структуры')

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
            <li class="breadcrumb-item">Редактирование структуры</li>
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

    <div class="row" id="id_content">
        <div class="col-lg-12">
            
            <form action="{{ route('structure.update', $structure->id) }}" method="POST" id="formEdit">
                <div class="card">
                @csrf
                @method('PUT')

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

                            <div class="row mb-3">
                                <div class="col-lg-9">
                                    <label for="name" class="mb-1">Название структуры</label>
                                    <input type="text" id="name" name="name" class="form-control form-control-lg" placeholder="Название структуры" value="{{ $structure->name }}" data-required="1">
                                    <div id="name_error" class="fieldcheck-error"></div>
                                </div>
                                <div class="col-lg-3">
                                    <label for="name" class="mb-1">Меню</label>
                                    <select name="structure_menu_id" class="form-select structure_menu_select">
                                        <option value="">...</option>
                                        @foreach ($menus as $menu)
                                            @php
                                                $current = $menu->id == $structure->structure_menu_id ? 'selected="selected"' : ''
                                            @endphp
                                            <option {{ $current }} value="{{ $menu->id }}">{{ $menu->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-lg-4">
                                    <label class="mb-1">Путь</label>
                                    <div>

                                        <input type="text" name="path" value="{{ $structure->path }}" class="form-control" placeholder="Путь" data-required="1" data-min="1" data-max="255">
                                        
                                        <a href="{{ $url }}" target="_blank" class="input-group-addon bg-blue bordered-blue" id="pathLink">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                    </div>
                                    <div id="path_error" class="fieldcheck-error"></div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="mb-1">Порядок сортировки</label>
                                    <div class="input-group">
                                        <input type="text" name="sorting" class="form-control" placeholder="Порядок сортировки" value="{{ $structure->sorting }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-lg-4">
                                    <div class="form-check form-switch form-switch-success">
                                        <input value="1" @if ($structure->active == 1) checked="" @endif class="form-check-input" name="active" type="checkbox" id="active">
                                        <label class="form-check-label" for="active">Активность страницы</label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-check form-switch form-switch-purple">

                                        <input value="1" @if ($structure->indexing == 1) checked="" @endif class="form-check-input" type="checkbox" name="indexing" id="indexing">
                                        <label class="form-check-label" for="indexing">Индексация</label>
                                    </div>
                                </div>
                            </div>

              
                            <ul class="nav btn-group button-items" role="tablist">
                                <li class="nav-item">
                                    <input type="radio" class="btn-check" name="type" id="type0" autocomplete="off" value="0" @if($structure->type == 0) checked @endif>
                                    <label class="btn btn-success btn-sm" for="type0" data-bs-toggle="tab" href="#document" role="tab" aria-selected="false">Документ</label>
                                </li>
                                <li class="nav-item">
                                    <input type="radio" class="btn-check" name="type" id="type1" autocomplete="off" value="1" @if($structure->type == 1) checked @endif>
                                    <label class="btn btn-warning btn-sm" for="type1" data-bs-toggle="tab" href="#redirect" role="tab" aria-selected="false">Ссылка</label>
                                </li>                                                
                            </ul>

                            <div class="tab-content">
                                <div @class(["tab-pane", "mt-3", "active" => $structure->type == 0 ? true : false]) id="document" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            
                                            <textarea type="text" name="text" class="form-control editor" placeholder="Описание">{{ $structure->text }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div @class(["tab-pane", "mt-3", "active" => $structure->type == 1 ? true : false]) id="redirect" role="tabpanel">

                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div>
                                                <input type="text" name="redirect" value="{{ $structure->redirect }}" class="form-control" placeholder="Ссылка">
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>                                                
                            </div>
                        </div>

                        <div class="tab-pane" id="seo">
                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <label class="mb-1">Заголовок страницы [Seo Title]</label>
                                    <input type="text" name="seo_title" class="form-control" placeholder="Заголовок страницы [Seo title]" value="{{ $structure->seo_title }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <label class="mb-1">Описание страницы [Seo Description]</label>
                                    <textarea name="seo_description" class="form-control" placeholder="Описание страницы [Seo description]">{{ $structure->seo_description }}</textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <label class="mb-1">Ключевые слова [Seo Keywords]</label>
                                    <input type="text" name="seo_keywords" class="form-control" placeholder="Ключевые слова [Seo Keywords]" value="{{ $structure->seo_keywords }}">
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="card-footer text-center">
                        <input type="hidden" name="parent_id" value="{{$structure->parent_id}}" />
                        <button type="submit" name="save" value="0" class="btn btn-primary">Сохранить</button>
                        <button type="submit" name="apply" value="1" class="btn btn-success">Применить</button>
                    </div>
                </div>
                </div>
            </form>
        </div>
    </div>
    
@endsection
