@extends("admin.main")

@section('title', __(':edit'))

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
                <li class="breadcrumb-item">{{ __(':edit') }}</li>
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
            
            <form action="{{ route('informationsystem.informationsystem-item.update', ['informationsystem' => $informationsystemItem->informationsystem_id, 'informationsystem_item' => $informationsystemItem->id]) }}" method="POST" id="formEdit" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="p-2">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#main" data-bs-toggle="tab" role="tab">
                                <i class="la la-home " title="Основные"></i>
                            </a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#images" data-bs-toggle="tab" role="tab">Изображения</a></li>
                        <li class="nav-item"><a class="nav-link" href="#description" data-bs-toggle="tab" role="tab">Описание</a></li>
                        <li class="nav-item"><a class="nav-link" href="#seo" data-bs-toggle="tab" role="tab">SEO</a></li>
                    </ul>
                </div>

                <div class="card-primary">
                    <div class="card-body tab-content">

                        <div class="tab-pane active" id="main">

                            <div class="mb-3">
                                <label class="mb-1">Название</label>
                                <input value="{{ $informationsystemItem->name }}" type="text" name="name" class="form-control" placeholder="Название">
                            </div>

                            <div class="mb-4">
                                <label class="mb-1">Теги</label>
                                <input type="text" name="tags" class="form-control" placeholder="Название">

                                <div class="tags position-absolute">

                                    @include("admin.informationsystem.item.tags", ["informationsystemItem" => $informationsystemItem, "BadgeClasses" => $BadgeClasses])
                                </div>

                            </div>

                            
                            <div class="row">
                                <div class="col-12 col-sm-3  mb-3">
                                    <label class="mb-1">Сортировка</label>
                                    <input type="text" value="{{ $informationsystemItem->sorting }}" name="sorting" class="form-control" placeholder="Сортировка">
                                </div>
                                <div class="col-12 col-sm-3  mb-3">
                                    <label class="mb-1">Путь</label>
                                    <input type="text" name="path" value="{{ $informationsystemItem->path }}" class="form-control" placeholder="Путь">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-3 d-flex align-items-end">
                                    <div class="d-flex">
                                        <div class="form-check form-switch form-switch-success"> 
                                            <input value="1" @if ($informationsystemItem->active == 1) checked="" @endif class="form-check-input" name="active" type="checkbox" id="active">
                                            <label for="active">
                                                Активность
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="images">

                            <div class=" mb-3">
                                <div action="{{ route('uploadInformationsystemItemImage', $informationsystemItem->id) }}" class="dropzone" id="myDropzone">
                                    
                                </div>
                                <div class="max-size-label">Максимальный размер файла - {{ ini_get('upload_max_filesize') }}</div>
                            </div>
                   
                            <div class="file-box-content mb-3 d-flex flex-wrap gallery" id="sortContainer">

                                @foreach ($images as $k => $image)
                                    <div class="file-box d-flex align-items-center justify-content-center sortable" id="{{ $k }}">

                                        <a href="javascript:void(0)" onclick="if(confirm('Вы действительно хотите удалить изображение?')) {adminImage.remove('{{ route('deleteInformationsystemItemImage', [$informationsystemItem->id, $k]) }}', $(this).parent())}">
                                            <i class="las la-times file-close-icon"></i>
                                        </a>

                                        <div class="text-center">
                                            <div class="file-box-image">
                              
                                                @if (!empty($image['image_small']))
                                                    <a @if (!empty($image['image_large'])) href="{{ $image['image_large'] }}" @endif class="lightbox"><img src="{{ $image['image_small'] }}"></a>
                                                @endif
                                            </div>
                                        </div>                                                        
                                    </div>
                                @endforeach
                                             
                            </div> 

                        </div>

                        <div class="tab-pane" id="description">

                            <div class="mb-3">

                                <label for="textarea_description" class="mb-1">Описание</label>
                                <textarea id="textarea_description" style="visibility:hidden" class="editor" aria-hidden="true" name="description">{{ $informationsystemItem->description }}</textarea>
                            </div>

                            <div class="mb-3">

                                <label for="text" class="mb-1">Текст</label>
                                <textarea id="text" style="visibility:hidden" class="editor" aria-hidden="true" name="text">{{ $informationsystemItem->text }}</textarea>
                            </div>

                        </div>

                        <div class="tab-pane" id="seo">
                            <div class="mb-3">
                                <label class="mb-1">Заголовок [Seo Title]</label>
                                <input type="text" value="{{ $informationsystemItem->seo_title }}"  name="seo_title" class="form-control" placeholder="Заголовок [Seo Title]">
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Описание [Seo Description]</label>
                                <input type="text" value="{{ $informationsystemItem->seo_description }}" name="seo_description" class="form-control" placeholder="Описание [Seo Description]">
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Ключевые слова [Seo Keywords]</label>
                                <input type="text" value="{{ $informationsystemItem->seo_keywords }}" name="seo_keywords" class="form-control" placeholder="Ключевые слова [Seo Keywords]">
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

@section("css")
    <link href="/assets/plugins/tobii/tobii.min.css" rel="stylesheet" type="text/css" />
    
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <style type="text/css">
        div.sortable {float: left;}
       .max-size-label {text-align: right; font-size: 12px;}
       .spinner-small {width: 40px; height: 40px; margin: 20px 0;}
       .accordion-button.hide-arrov::after{display: none}
    </style>

@endsection

@section("js")

    <script>
        var routeSortImages = '{{ route("sortingInformationsystemItemImages", $informationsystemItem->id) }}',
            routeGetInformationsystemItemGallery = '{{ route("getInformationsystemItemGallery", $informationsystemItem->id) }}',
            BadgeClasses = [@foreach($BadgeClasses as $k => $BadgeClasse)'{{$BadgeClasse}}'@if($k < count($BadgeClasses)-1),@endif @endforeach];
    </script>

    <script src="/assets/image.js"></script>
    <script src="/assets/plugins/tobii/tobii.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>
        const tobii = new Tobii();
    </script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.0/dropzone.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.0/dropzone.min.js"></script>

    <script>

        var Tag = {

            delete: function(item, id) {
                
                $.ajax({
                    url: '{{ route("deleteTag") }}',
                    type: "GET",
                    data: {"id": id},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function (data) {

                        if (data == true) {
                            item.parent().remove();
                        }
                    }
                });
            }
        }

        $('[name="tags"]').autocomplete({
            source: '{{ route("getTags") }}',
            minLength: 1,
            select: function( event, ui ) {

                $.ajax({
                    url: '{{ route("addTag") }}',
                    data: {"tag": ui.item.label, "informationsystem_item_id": {{ $informationsystemItem->id }}},
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function (data) {
                        var getRandomBadgeClass = Math.floor( (Math.random() * BadgeClasses.length) + 0);
    
                        if (!$("#tag_" + ui.item.data).length) {
                            $(".tags")
                                .append('<span id="tag_' + ui.item.data + '" class="badge badge-soft-'+ BadgeClasses[getRandomBadgeClass] +'">'+ ui.item.label +'<a href="javascript:void(0)" onclick="Tag.delete($(this), '+ data +')" class="mdi mdi-close"></a><input type="hidden" name="tags[]" value="'+ ui.item.data +'"></span>');
                        }

                        $('[name="tags"]').val("");
                    }
                });


            }, 
        });
  
        $(function() {
            $('#sortContainer').sortable({
                update: function() {
                    let aResult = [];

                    $('#sortContainer').find(".sortable").each(function(k) {
                        if ($(this).hasAttr("id")) {
                            aResult[k] = $(this).attr("id");
                        }
                    });

                    $.ajax({
                        url: routeSortImages,
                        type: "GET",
                        data: {
                            "images": JSON.stringify(aResult) 
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                    });
                }
            });  
        });

        Dropzone.options.myDropzone = {
            init: function() {

                this.on("success", function(file, responseText) {

                    this.removeFile(file);

                    delay(() => {
                        Gallery.update();
                    }, 1000);
                   
                });
                this.on("complete", function(file, responseText) {
                    this.removeFile(file);

                    delay(() => {
                        Gallery.update();
                    }, 1000);
                });
            }
        }


        var Gallery = {
            update: function() {
                
                $.ajax({
                    url: routeGetInformationsystemItemGallery,
                    type: "get",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "html",
                    success: function (data) {

                        $('.gallery').html(data);

                        const tobii = new Tobii()
                    },

                    error: function () {
            
                    },
                });
                
            }
        }

        
    </script>


@endsection