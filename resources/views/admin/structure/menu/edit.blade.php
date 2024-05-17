@extends("admin.main")

@section('title', 'Редактирование меню')

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
            <li class="breadcrumb-item">Редактирование меню</li>
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
            
            <form action="{{ route('structure-menu.update', ['structure_menu' => $menu->id]) }}" method="POST" id="formEdit">

                @csrf
                @method('PUT')

                <div class="card">

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
                                <label for="name" class="mb-1">Название меню</label>
                                <input type="text" value="{{ $menu->name }}" id="name" name="name" class="form-control form-control-lg" placeholder="Название меню" data-required="1" data-min="2" data-max="255">
                                <div id="name_error" class="fieldcheck-error"></div>
                            </div>

                        </div>

                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" name="save" value="0" class="btn btn-primary">Сохранить</button>
                        <button type="submit" name="apply" value="1" class="btn btn-success">Применить</button>
                    </div>
                </div>
                </div>
            </form>
        </div>
    </div>

    
@endsection