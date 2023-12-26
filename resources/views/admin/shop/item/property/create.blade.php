@extends("admin.main")

@section('title', 'Новое свойство')

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
                <li class="breadcrumb-item">Новое свойство</li>
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

    <link href="/assets/plugins/select/selectr.min.css" rel="stylesheet" type="text/css" />
    <script src="/assets/plugins/select/selectr.min.js"></script>
    <script src="/assets/js/pages/shopItemProperty.js"></script>


    <div class="row">
        <div class="col-lg-12">

            <div class="card" id="id_content">
    
                <form action="{{ route('shopItemProperty.store') }} " method="POST" id="formEdit" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                        <div class="p-2">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#main" data-bs-toggle="tab" role="tab">
                                        <i class="la la-home " title="Основные"></i>
                                    </a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="#groups" data-bs-toggle="tab" role="tab">Доступность в группах</a></li>
                            </ul>
                        </div>
    
                        <div class="card-primary">
                            <div class="card-body tab-content">

                                <div class="tab-pane active" id="main">

                                    <div class="mb-3">
                                        <label class="mb-1">Название свойства</label>
                                        <input id="name" type="text" name="name" class="form-control form-control-lg" placeholder="Название свойства" data-min="1"  data-max="255" data-required="1">
                                        <div id="name_error" class="fieldcheck-error"></div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-sm-4 mb-3">
                                            <label class="mb-1">Тип</label>
                                            <select id="shop_item_property_type" name="type">
                                                @foreach ($types as $k => $type)
                                                    <option value="{{ $k }}">{{ $type }}</option>
                                                @endforeach
                                            </select> 
                                            <script>new Selectr('[name="type"]');</script>
                                        </div>
                                        <div class="col-12 col-sm-4 mb-3">
                                            <label class="mb-1">Предназначение</label>
                                            <select id="shop_item_property_destination" name="destination">
                                                <option value="0">По умолчанию</option>
                                                <option value="1">Цвет</option>
                                            </select> 
                                            <script>new Selectr('[name="destination"]');</script>
                                        </div>
                                        <div class="col-12 col-sm-4 mb-3">
                                            <div class="shop-property-lists hidden">
                                                <label class="mb-1">Списки</label>
                                                <select name="shop_item_list_id">
                                                    <option value="">...</option>
                                                    @foreach ($lists as $k => $list)
                                                        <option value="{{ $k }}">{{ $list->name }}</option>
                                                    @endforeach
                                                </select> 
                                                <script>new Selectr('[name="shop_item_list_id"]');</script>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3" id="row_multiple">
                                        <div class="form-check form-switch form-switch-purple">
                                            <input class="form-check-input" type="checkbox" name="multiple" id="multiple">
                                            <label class="form-check-label" for="multiple">Множественный</label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="mb-1">Сортировка</label>
                                        <input type="text" name="sorting" class="form-control" placeholder="Сортировка">
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-sm-4 mb-3">
                                            <div class="form-check form-switch form-switch-purple">
                                                <input class="form-check-input" type="checkbox" name="show_in_item" id="show_in_item">
                                                <label class="form-check-label" for="show_in_item">Показывать в товаре</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mb-3">
                                            <div class="form-check form-switch form-switch-purple">
                                                <input class="form-check-input" type="checkbox" name="show_in_filter" id="show_in_filter">
                                                <label class="form-check-label" for="show_in_filter">Показывать в фильтре</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane" id="groups">
                                    
                                    @foreach ($groups as $group)

                                        <div class="col-12 col-sm-3 d-flex align-items-end">

                                            <div class="d-flex">
                                                <div class="form-check field-check-center">
                                                    <div>
                                                        <input class="form-check-input" value="{{ $group->id }}" name="property_for_group[]" type="checkbox" id="property_for_group_{{ $group->id }}" >
                                                        <label for="property_for_group_{{ $group->id }}">
                                                            [{{ $group->id }}] {{ $group->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                    
                                        </div>

                                    @endforeach

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