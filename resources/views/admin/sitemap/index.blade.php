@extends("admin.main")

@section('title', 'Sitemap')

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

    @if (session('error'))
        <div class="alert alert-danger border-0" role="alert">
            {{ session('error') }}
        </div>
    @endif


    <div class="file-box-content">
        <div class="file-box">
            <div class="text-center">
                <a target="_blank" href="{{ route('getSitemap') }}"><i class="lar la-file-pdf text-info ico"></i></a>
                <h6 class="text-truncate">Sitemap</h6>
                @if (!empty($sitemapInfo["date"]))
                    <small class="text-muted">
                        {{ $sitemapInfo["date"] }} / {{ $sitemapInfo["filesize"] }}
                    </small>
                @endif
                
            </div>                                                        
        </div>
        <div class="file-box">
            <div class="text-center">
                <a target="_blank" href="{{ route('getImagemap') }}"><i class="lar la-file-pdf text-danger ico"></i></a>
                <h6 class="text-truncate">ImageMap</h6>
                @if (!empty($imagemapInfo["date"]))
                    <small class="text-muted">
                        {{ $imagemapInfo["date"] }} / {{ $imagemapInfo["filesize"] }}
                    </small>
                @endif
            </div>                                                        
        </div>                                             
    </div> 

@endsection

@section('css')

    <style>
        .file-box-content .file-box i.ico {font-size: 100px}
    </style>

@endsection
