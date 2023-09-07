@extends("admin.main")

@section('title', 'Редактирование валюты')

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
                <li class="breadcrumb-item">Редактирование валюты</li>
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

    <div class="row">
        <div class="col-12">

            <div class="card" id="id_content">
                
                <form action="{{ route('shopCurrency.update', $currency->id) }}" method="POST" id="formEdit" enctype="multipart/form-data">
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

                                <div  class="mb-3">
                                    <label class="mb-1">Название валюты</label>
                                    <input id="name" type="text" name="name" value="{{ $currency->name }}" class="form-control form-control-lg" placeholder="Название валюты" data-min="1"  data-max="255" data-required="1">
                                    <div id="name_error" class="fieldcheck-error"></div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-3">
                                        <label class="mb-1">Код</label>
                                        <input type="text" name="code" class="form-control" placeholder="Код" value="{{ $currency->code }}">
                                    </div>
                                    <div class="col-3">
                                        <label class="mb-1">Курс</label>
                                        <input type="text" name="exchange_rate" class="form-control" placeholder="Курс" value="{{ $currency->exchange_rate }}">
                                    </div>
                                    <div class="col-3">
                                        <label class="mb-1">Сортировка</label>
                                        <input type="text" name="sorting" class="form-control" placeholder="Сортировка" value="{{ $currency->sorting }}">
                                    </div>

                                    <div class="col-3 d-flex align-items-end">
                                        <div class="d-flex switch-col">
                                            <div class="form-check form-switch form-switch-success">
                                                @if ($currency->default == 1)
                                                    <input class="form-check-input" name="default" type="checkbox" id="default" checked="checked">
                                                @else
                                                    <input class="form-check-input" name="default" type="checkbox" id="default">
                                                @endif
                                                <label for="default" class="form-check-label">
                                                    Базовая
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                
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