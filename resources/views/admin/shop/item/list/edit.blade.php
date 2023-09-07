@extends("admin.main")

@section('title', 'Редактирование списка')

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
                <li class="breadcrumb-item">Редактирование списка</li>
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
    
                <form action="{{ route('shopItemList.update', $list->id) }} " method="POST" id="formEdit" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                        <div class="p-2">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#main" data-bs-toggle="tab" role="tab">
                                        <i class="la la-home " title="Основные"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
    
                        <div class="card-primary">
                            <div class="card-body tab-content">

                                <div class="tab-pane active" id="main">

                                    <div class="mb-3">
                                        <label class="mb-1">Название списка</label>
                                        <input value="{{ $list->name }}" id="name" type="text" name="name" class="form-control form-control-lg" placeholder="Название списка" data-min="1"  data-max="255" data-required="1">
                                        <div id="name_error" class="fieldcheck-error"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="mb-1">Описание</label>
                                        <textarea id="name" type="text" name="description" class="form-control" placeholder="Описание">{{ $list->description }}</textarea>
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