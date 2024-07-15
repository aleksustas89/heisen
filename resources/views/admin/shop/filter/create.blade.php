@extends("admin.main")

@section('title', 'Создание статического фильтра')

@section('breadcrumbs')
    <div class="page-title-box d-flex flex-column">
        <div class="float-start">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                @endforeach
                <li class="breadcrumb-item">Создание</li>
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

    @if ($errors->all())
		<div class="alert alert-danger">
			@foreach($errors->all() as $error)
			<p>{{ $error }}</p>
			@endforeach
		</div>
	@endif


    <div class="row">
        <div class="col-12">

            <div class="card" id="id_content">
                <form action="{{ route('shop.shop-filter.store', ['shop' => $shop->id]) }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('POST')
                    
                    <div class="card-primary">
                        <div class="card-body tab-content">

                            <div  class="mb-3">
                                <label class="mb-1">Группа</label>
                                <select name="shop_group_id" class="form-control" required="">
                                    <option value="0">...</option>
                                    @php
                                        \App\Http\Controllers\ShopGroupController::showTreeGroupsAsOptions();
                                    @endphp
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Url</label>
                                <input disabled type="text" name="url" value="" class="form-control" placeholder="Url">
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Заголовок [Seo Title]</label>
                                <input type="text" name="seo_title" value="" class="form-control" placeholder="Заголовок страницы [Seo title]">
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Описание [Seo Description]</label>
                                <textarea name="seo_description" class="form-control" placeholder="Описание страницы [Seo description]"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Ключевые слова [Seo Keywords]</label>
                                <input type="text" name="seo_keywords" value="" class="form-control" placeholder="Ключевые слова [Seo Keywords]">
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Текст</label>
                                <textarea type="text" name="text" class="form-control editor" placeholder="Текст"></textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label class="mb-1">Сортировка</label>
                                <select name="sorting" class="form-select">  
                                    <option value="0">...</option>
                                    <option value="1">Сортировка - новые</option>
                                    <option value="2">Сортировка - старые</option> 
                                </select>
                            </div>

                            @if (count($shopItemProperties) > 0)
                                <div class="properties-block">
                                    @foreach ($shopItemProperties as $k => $shopItemProperty)
                                        <div class="list-group-item">

                                            @if (!is_null($shopItemList = $shopItemProperty->shopItemList))
                                                <div class="row mb-3 admin-item-property" data-property="{{ $shopItemProperty->id }}" id="admin-item-property-{{ $shopItemProperty->id }}-{{ $k }}">
                                                    <div class="col-10">
                                                        <label class="mb-1">{{ $shopItemProperty->name }}</label>

                                                        <select data-name="property_{{ $shopItemProperty->id }}[]" name="property_{{ $shopItemProperty->id }}[]" class="form-select">
                                                            <option value="">...</option>

                                                            @foreach ($shopItemList->listItems as $ListItem)
                                                                <option value="{{ $ListItem->id }}">{{ $ListItem->value }}</option>
                                                            @endforeach
                                                            
                                                        </select>
                                                    </div>

                                                    <div class="col-2 d-flex align-items-end">
                                                        <div>
                                                            <button type="button" class="btn-upload btn btn-warning mt-1" onclick="adminProperty.copy($(this))"><i class="la la-plus"></i></button>
                                                            <button type="button" class="btn-upload btn btn-danger mt-1 delete-property" onclick="adminProperty.delete($(this))"><i class="la la-minus"></i></button>
                                                        </div>
                                                    </div>

                                                </div>
                                            @endif
                                           
                                        </div>
                                    @endforeach
                                </div>
                            @endif

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

    @php
    App\Services\Helpers\File::js('/assets/js/pages/shopItem.js');
    @endphp

@endsection