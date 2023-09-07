@extends("admin.main")

@section('title', 'Редактирование элемента списка')

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
                <li class="breadcrumb-item">Редактирование элемента списка</li>
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
    
                <form action="{{ route('shopItemListItem.update', $list_item->id) }} " method="POST" id="formEdit" enctype="multipart/form-data">
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
                                        <label class="mb-1">Значение</label>
                                        <input id="value" value="{{ $list_item->value }}" type="text" name="value" class="form-control form-control-lg" placeholder="Значение" data-min="1"  data-max="255" data-required="1">
                                        <div id="value_error" class="fieldcheck-error"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="mb-1">Описание</label>
                                        <textarea id="name" type="text" name="description" class="form-control" placeholder="Описание">{{ $list_item->description }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="mb-1">Сортировка</label>
                                        <input type="text" value="{{ $list_item->sorting }}" name="sorting" class="form-control" placeholder="Сортировка">
                                    </div>

                                    <div class="row mb-3">

                                        <div class="col-1">
                                            <label for="exampleColorInput" class="mb-1">Цвет</label>
                                            <div class="col-sm-10">
                                                <input name="color" type="color" class="form-control form-control-color" title="Выберите цвет"  value="{{ $list_item->color }}">
                                            </div>
                                        </div>

                                        <div class="col-2 d-flex align-items-end">
                                            <div class="form-check form-switch form-switch-success mb-2">

                                                @php
                                                
                                                $checked = $list_item->active == 1 ? 'checked=""' : ''

                                                @endphp
                                                
                                                <input class="form-check-input" name="active" type="checkbox" id="active" {{ $checked }}>
                                                
                                                <label class="form-check-label" for="active">Активность</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <input type="hidden" name="shop_item_list_id" value="{{ $list_item->shop_item_list_id }}" />
                                <button type="submit" name="save" value="0" class="btn btn-primary">Сохранить</button>
                                <button type="submit" name="apply" value="1" class="btn btn-success">Применить</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>

    
@endsection