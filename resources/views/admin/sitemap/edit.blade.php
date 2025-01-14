@extends("admin.main")

@section('title', 'Редактирование')

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
            <li class="breadcrumb-item">Редактирование</li>
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
            
            <form action="{{ route('sitemap.update', $sitemap->id) }}" method="POST" id="formEdit">
                <div class="card">
                @csrf
                @method('PUT')


                <div class="card-primary">
                    <div class="card-body">

                        @foreach ($sitemapFields as $sitemapField)

                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <label class="mb-1">{{ $sitemapField->name }}</label>
                                    <input type="text" name="{{ $sitemapField->field }}" class="form-control" placeholder="{{ $sitemapField->name }}" value="{{ $sitemapField->value }}">
                                </div>
                            </div>

                        @endforeach

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
