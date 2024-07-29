@extends("admin.main")

@section('title', 'Редактирование группы')

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
                <li class="breadcrumb-item">Редактирование группы</li>
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
    
                <form action="{{ route('shop.shop-group.update', ['shop' => $shopGroup->shop_id, 'shop_group' => $shopGroup->id]) }}" method="POST" id="formEdit" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                        <div class="p-2">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#main" data-bs-toggle="tab" role="tab">
                                        <i class="la la-home " title="Основные"></i>
                                    </a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="#seo" data-bs-toggle="tab" role="tab">SEO</a></li>
                            </ul>
                        </div>
    
                        <div class="card-primary">
                            <div class="card-body tab-content">

                                <div class="tab-pane active" id="main">

                                    <div class="mb-3">
                                        <label class="mb-1">Название</label>
                                        <input id="name" value="{{ $shopGroup['name'] }}" type="text" name="name" class="form-control form-control-lg" placeholder="Название" data-min="1"  data-max="255" data-required="1">
                                        <div id="name_error" class="fieldcheck-error"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="mb-1">Заголовок h1</label>
                                        <input type="text"  value="{{ $shopGroup['h1'] }}" name="h1" class="form-control" placeholder="Заголовок h1">
                                    </div>

                                    <div class="mb-3">
                                        <label class="mb-1">Краткое описание</label>
                                        <textarea type="text" value="{{ $shopGroup['description'] }}" name="description" class="form-control editor" placeholder="Краткое описание">{{ $shopGroup['description'] }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="mb-1">Текст</label>
                                        <textarea type="text" name="text" class="form-control editor" placeholder="Текст">{{ $shopGroup['text'] }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="mb-1">Родительская группа</label>
                                        <select name="parent_id" class="form-control" data-p="{{ $shopGroup->parent_id }}">
                                            <option value="0">...</option>
                                            @php
                                                \App\Http\Controllers\ShopGroupController::showTreeGroupsAsOptions($shopGroup->parent_id);
                                            @endphp
                                        </select>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-xsl-6 mb-2 d-flex">
                             
                                     
                                            @php
                                                $addImage = !empty($shopGroup['image_large']) ? true : false;
                                                $editImage = !empty($shopGroup['image_large']) ? false : true;
                                            @endphp


                                            <div id="edit_large_image" @class([
                                                'hidden' => $editImage,
                                                'd-flex' => $addImage,
                                                'align-items-center',
                                                'flex-grow-1',
                                                'input-group'
                                                ])>
                                                <div class="file-caption mr-3 d-flex align-items-center height-100">
                                                    <i class="fa fa-file-image-o image-color margin-right-5"></i>
                                                    <a target="_blank" href="{{$store_path}}group_{{$shopGroup['id']}}/{{$shopGroup['image_large']}}">{{$shopGroup['image_large']}}</a>
                                                </div>

                                                <a class="btn btn-warning btn-sm mr-1 edit-delete-image" onclick="toggleBlocks('edit_large_image', 'add_large_image')"><i class="far fa-edit"></i></a>
                                                <a class="btn btn-danger btn-sm edit-delete-image" onclick="if(confirm('Вы действительно хотите удалить изображение?')) {toggleBlocks('edit_large_image', 'add_large_image'); shopGroup.deleteImage({{$shopGroup['id']}}, 'image_large')}"><i class="fas fa-trash"></i></a>
                                            </div>

                                            <div id="add_large_image" @class([
                                                'hidden' => $addImage,
                                                'd-flex',
                                                'align-items-center',
                                                'flex-grow-1',
                                                'input-group'
                                                ])>

                                                <input type="file" name="image_large" class="form-control" id="image_large" aria-describedby="image_large" aria-label="Upload">
                                                <button class="btn btn-outline-secondary" type="button" id="image_large">Большое изображение</button>
                                            </div>

                                        </div>
                                        <div class="col-12 col-xsl-6 mb-2 d-flex">
                             
                                     
                                            @php
                                                $addImage = !empty($shopGroup['image_small']) ? true : false;
                                                $editImage = !empty($shopGroup['image_small']) ? false : true;
                                            @endphp


                                            <div id="edit_image_small" @class([
                                                'hidden' => $editImage,
                                                'd-flex' => $addImage,
                                                'align-items-center',
                                                'flex-grow-1',
                                                'input-group'
                                                ])>
                                                <div class="file-caption mr-3 d-flex align-items-center height-100">
                                                    <i class="fa fa-file-image-o image-color margin-right-5"></i>
                                                    <a target="_blank" href="{{$store_path}}group_{{$shopGroup['id']}}/{{$shopGroup['image_small']}}">{{$shopGroup['image_small']}}</a>
                                                </div>

                                                <a class="btn btn-warning btn-sm mr-1 edit-delete-image" onclick="toggleBlocks('edit_image_small', 'add_image_small')"><i class="far fa-edit"></i></a>
                                                <a class="btn btn-danger btn-sm edit-delete-image" onclick="if(confirm('Вы действительно хотите удалить изображение?')) {toggleBlocks('edit_image_small', 'add_image_small'); shopGroup.deleteImage({{$shopGroup['id']}}, 'image_small')}"><i class="fas fa-trash"></i></a>
                                            </div>

                                            <div id="add_image_small" @class([
                                                'hidden' => $addImage,
                                                'd-flex',
                                                'align-items-center',
                                                'flex-grow-1',
                                                'input-group'
                                                ])>

                                                <input type="file" name="image_small" class="form-control" id="image_small" aria-describedby="image_small" aria-label="Upload">
                                                <button class="btn btn-outline-secondary" type="button" id="image_small">Малое изображение</button>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-4">
                                            <label class="mb-1">Сортировка</label>
                                            <input type="text" name="sorting" class="form-control" placeholder="Сортировка" value="{{ $shopGroup['sorting'] }}">
                                        </div>
                                        
                                        <div class="col-4">
                                            <label class="mb-1">Путь</label>
                                            <input type="text" name="path" class="form-control" placeholder="Путь" data-min="2"  data-max="255" data-required="1" value="{{ $shopGroup['path'] }}">
                                        </div>
                                        <div class="col-lg-4">
                                            <label>&nbsp;</label>
                                            <div class="form-check field-check-center">
                                                <div class="form-check form-switch form-switch-success">
                                                    
                                                    <input value="1" @if ($shopGroup->active == 1) checked="" @endif class="form-check-input" name="active" type="checkbox" id="active" >
                                                    <label for="active">
                                                        Активность
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane" id="seo">
                                    <div class="mb-3">
                                        <label class="mb-1">Заголовок [Seo Title]</label>
                                        <input type="text" value="{{ $shopGroup['seo_title'] }}" name="seo_title" value="" class="form-control" placeholder="Заголовок страницы [Seo title]">
                                    </div>

                                    <div class="mb-3">
                                        <label class="mb-1">Описание [Seo Description]</label>
                                        <textarea name="seo_description" class="form-control" placeholder="Описание страницы [Seo description]">{{ $shopGroup['seo_description'] }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="mb-1">Ключевые слова [Seo Keywords]</label>
                                        <input value="{{ $shopGroup['seo_keywords'] }}" type="text" name="seo_keywords" value="" class="form-control" placeholder="Ключевые слова [Seo Keywords]">
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" name="save" value="0" class="btn btn-primary" disabled>Сохранить</button>
                                <button type="submit" name="apply" value="1" class="btn btn-success" disabled>Применить</button>
                            </div>
                        </div>
                    
                    
                </form>

                <script>
                    $(function () {

                        var shopGroup = {
                            deleteImage: function (group_id, field) {
                                $.ajax({
                                    url: "/admin/shopGroup/" + group_id + "/delete/" + field,
                                    type: "GET",
                                    dataType: "json",
                                });
                            }
                        }
                    });
                </script>

            </div>
        </div>
    </div>

    
@endsection