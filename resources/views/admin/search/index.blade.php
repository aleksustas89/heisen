@extends("admin.main")

@section('title', 'Поиск')

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
        </ol>
    </div>
</div>

@endsection

@section('content')

    @if (session('success'))
        <div class="alert alert-success border-0" role="alert">
            {{ session('success') }}
        </div>
    @else
        <div class="alert alert-success border-0" role="alert">
            Проиндексировано на текущий момент {{ $count }} страниц
        </div>
    @endif
            
    <div class="row">
        <div class="col-12">
            <div class="card" id="id_content">

                <div class="p-2">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#main" data-bs-toggle="tab" role="tab">
                                <i class="la la-home " title="Основные"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body tab-content">

        
                    <div class="row mb-3">
                        <div class="col-12">
                            <div id="result"></div>
                        </div>
                        <div class="col-12">
                            <button data-title="Индексировать" onclick="Search.init()" type="submit" name="apply" value="1" class="btn btn-success">Индексировать</button>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    
@endsection

@section('js')

    <script type=""></script>
    <script src="/assets/js/pages/search.js"></script>

@endsection