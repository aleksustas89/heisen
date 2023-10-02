@extends("admin.main")

@section('title', 'Редактирование скидки')

@section('breadcrumbs')
    <div class="page-title-box d-flex flex-column">
        <div class="float-start">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                @endforeach
                <li class="breadcrumb-item">Редактирование скидки "{{ $discount->name }}"</li>
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
                <form action="{{ route('shopDiscount.update', $discount->id) }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('PUT')
                    
                    <div class="card-primary">
                        <div class="card-body tab-content">

                            <div  class="mb-3">
                                <label class="mb-1">Название скидки</label>
                                <input id="name" value="{{ $discount->name }}" type="text" name="name" class="form-control form-control-lg" placeholder="Название скидки" data-min="1"  data-max="255" data-required="1">
                                <div id="name_error" class="fieldcheck-error"></div>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Описание скидки</label>
                                <textarea type="text" name="description" class="form-control editor" placeholder="Описание скидки">{{ $discount->description }}</textarea>
                            </div>

                            <div class="row mb-3">

                                <div class="col-3">
                                    <label class="mb-1">Действует от</label>
                                    <input type="datetime-local" value="{{ $discount->start_datetime }}" name="start_datetime" class="form-control" placeholder="Действует от">
                                </div>
                                <div class="col-3">
                                    <label class="mb-1">Действует до</label>
                                    <input type="datetime-local" value="{{ $discount->end_datetime }}" name="end_datetime" class="form-control" placeholder="Действует до">
                                </div>

                            </div>

                            <div class="row mb-3">

                                <div class="col-2">
                                    <label class="mb-1">Величина скидки</label>
                                    <div class="d-flex">
                                        <input type="text" value="{{ $discount->value }}" name="value" class="form-control" placeholder="Величина скидки">
                                        <select name="type" class="form-control">
                                            @foreach ($types as $key => $type)
                                                <option {{ $key == $discount->type ? 'selected' : '' }} value="{{ $key }}">{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <label class="mb-1">&nbsp;</label>
                                    <div class="d-flex">
                                        <div class="form-check field-check-center">
                                            <div class="form-check form-switch form-switch-success">
                                                @php
                                                $checked = $discount->active == 1 ? 'checked' : '';
                                                @endphp
                                                <input value="1" class="form-check-input" name="active" type="checkbox" id="active" {{ $checked }}>
                                                <label class="form-check-label" for="active">Активность</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-danger border-0" role="alert">
                                <strong>Применить ко всем товарам и модификациям по свойству типа - список!</strong>
                            </div>

                            <div class="row ">
                                <div class="col-2">
                                    <select class="form-control" name="apply_total_discount">
                                        <option value="">...</option>
                                        <option value="1">Применить</option>
                                        <option value="2">Удалить</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <select class="form-control" name="total_list_id">
                                        <option>...</option>
                                        @foreach ($propertys as $property)
                                            <option value="{{ $property->shop_item_list_id }}">{{ $property->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2 d-none">
                                    <select class="form-control" name="total_list_value">
                                    </select>
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

@section("js")
    <script src="/assets/js/pages/shopDiscount.js"></script>
@endsection
