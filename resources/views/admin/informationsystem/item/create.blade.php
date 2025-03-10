@extends("admin.main")

@section('title', __(':new'))

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
                <li class="breadcrumb-item">{{ __(':new') }}</li>
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
            
            <form action="{{ route('informationsystem.informationsystem-item.store', ['informationsystem' => $informationsystem->id]) }}" method="POST" id="formEdit" enctype="multipart/form-data">
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
                        <li class="nav-item"><a class="nav-link" href="#seo" data-bs-toggle="tab" role="tab">SEO</a></li>
                    </ul>
                </div>

                <div class="card-primary">
                    <div class="card-body tab-content">

                        <div class="tab-pane active" id="main">

                            <div class="mb-3">

                                <label class="mb-1">Название</label>
                                <input type="text" name="name" class="form-control" placeholder="Название">
                            </div>

                            <div class="row">
                                <div class="col-12 col-sm-3 mb-3">
                                    <label class="mb-1">Сортировка</label>
                                    <input type="text" name="sorting" class="form-control" placeholder="Сортировка">
                                </div>
                                <div class="col-12 col-sm-3 mb-3">
                                    <label class="mb-1">Путь</label>
                                    <input type="text" name="path" class="form-control" placeholder="Путь" >
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
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
                        </div>

                        <div class="tab-pane" id="images">

                            <div class="upload-call">
                                <div class="wrap">
                                    Сохраните элемент, чтобы добавлять изображения!
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="description">

                            <div class="mb-3">

                                <label for="textarea_description" class="mb-1">Описание</label>
                                <textarea id="textarea_description" style="visibility:hidden" class="editor" aria-hidden="true" name="description"></textarea>
                            </div>

                            <div class="mb-3">

                                <label for="text" class="mb-1">Текст</label>
                                <textarea id="text" style="visibility:hidden" class="editor" aria-hidden="true" name="text"></textarea>
                            </div>

                        </div>

                        <div class="tab-pane" id="seo">
                            <div class="mb-3">
                                <label class="mb-1">Заголовок [Seo Title]</label>
                                <input type="text" name="seo_title" class="form-control" placeholder="Заголовок [Seo Title]">
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Описание [Seo Description]</label>
                                <input type="text" name="seo_description" class="form-control" placeholder="Описание [Seo Description]">
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Ключевые слова [Seo Keywords]</label>
                                <input type="text" name="seo_keywords" class="form-control" placeholder="Ключевые слова [Seo Keywords]">
                            </div>
                        </div>
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
