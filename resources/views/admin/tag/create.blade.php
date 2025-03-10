@extends("admin.main")

@section('title', 'Новый тег')

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
                <li class="breadcrumb-item">Новый тег</li>
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

    @if ($errors->any())
        <div class="alert alert-danger border-0" role="alert">
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif


    <div class="row">
        <div class="col-lg-12">

            <div class="card" id="id_content">

                <form action="{{ route('tag.store') }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('POST')
                    
                    <div class="card-body tab-content">

                        <div class="mb-3">
                            <label class="mb-1">Название тега</label>
                            <input id="name" type="text" name="name" value="" class="form-control form-control-lg" placeholder="Название тега" data-min="2"  data-max="255" data-required="1">
                            <div id="name_error" class="fieldcheck-error"></div>
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