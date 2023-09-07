@extends("admin.main")

@section('title', 'Создание поля доставки')

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
                <li class="breadcrumb-item">Создание доставки</li>
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
                <form action="{{ route('shopDeliveryField.update', $field->id) }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body tab-content">

                        <div class="row mb-3">
                            <div class="col-6"> 
                                <label class="mb-1 my-2">Название поля доставки</label>
                                <input type="text" value="{{ $field->caption }}" name="caption" class="form-control form-control-lg" placeholder="Название доставки">
                            </div>
                            <div class="col-6"> 
                                <label class="mb-1 my-2">Название поля в input</label>
                                <input type="text" value="{{ $field->field }}" name="field" class="form-control form-control-lg" placeholder="Название поля в input">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6"> 
                                <label class="mb-1 my-2">Тип</label>
                                <select class="form-control" name="type">
                                    
                                    @foreach ($types as $key => $type)
                                        <option {{ $key == $field->type ? ' selected=""' : '' }} value="{{ $key }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6"> 
                                <label class="mb-1 my-2">Сортировка</label>
                                <input value="{{ $field->sorting }}" type="text" name="sorting" class="form-control" placeholder="Сортировка">
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

