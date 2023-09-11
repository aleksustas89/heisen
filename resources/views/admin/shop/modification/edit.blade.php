@extends("admin.main")

@section('title', 'Редактирование Интернет-магазина')

@section('breadcrumbs')
    <div class="page-title-box d-flex flex-column">
        <div class="float-start">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                @endforeach
                <li class="breadcrumb-item">Редактирование модификации {{ $Modification->name }}</li>
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

                <div class="p-2">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#main" data-bs-toggle="tab" role="tab">
                                <i class="la la-home " title="Основные"></i>
                            </a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#images" data-bs-toggle="tab" role="tab">Изображения</a></li>
                        <li class="nav-item"><a class="nav-link" href="#properties" data-bs-toggle="tab" role="tab">Свойства</a></li>
                    </ul>
                </div>

                <form action="{{ route('modification.update', $Modification->id) }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body tab-content">

                        <div class="tab-pane active" id="main">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="mb-1">Название модификации</label>
                                    <input type="text" name="name" value="{{ $Modification->name }}" class="form-control form-control-lg" placeholder="Название модификации" data-min="2"  data-max="255" data-required="1">
                                    <div id="name_error" class="fieldcheck-error"></div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">

                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Цена</h3>
                                        </div>
                                        <div class="card-body" style="display: block;">
                                            
                                            <div class="row form-group">
                    
                                                <div class="col-lg-2">
                                                    <input type="text" value="{{ $Modification->price }}" name="price" class="form-control" placeholder="Цена" >
                                                </div>
                                                <div class="col-lg-2 d-flex align-items-center">
                                                    {{ $Modification->ShopCurrency->name }}
                                                    <input type="hidden" name="shop_currency_id" value="{{ $Modification->shop_currency_id }}" />
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>

                            

                            
  
                        </div>

                        <div class="tab-pane" id="images">

                            <div class="file-box-content mb-3 d-flex">
                            
                                    @php
                                        $path = $shopItem->path();
                                        $ShopModificationImage = $Modification->ShopModificationImage;
                                    @endphp

                                    <label onclick="adminModification.select($(this))" data-id="0" @class(['file-box', 'd-flex', 'align-items-center', 'justify-content-center', 'position-relative', 'pointer', 'active' => is_null($ShopModificationImage)])>
                                                                                
                                        <div class="text-center">
                                            <div class="file-box-image">
                                                <i class="la la-image " title=""></i>
                                            </div>
                                        </div>
                                        
                                    </label>

                                    @foreach ($shopItem->ShopItemImages as $ShopItemImage)

                                        @php
                                        $active = !is_null($ShopModificationImage) && $ShopModificationImage->shop_item_image_id == $ShopItemImage->id
                                                        ? true : false;
                                        @endphp

                                        <label onclick="adminModification.select($(this))" data-id="{{ $ShopItemImage->id }}" @class(['file-box', 'd-flex', 'align-items-center', 'justify-content-center', 'position-relative', 'pointer', 'active' => $active])>
                                            
                                            <div class="text-center">
                                                <div class="file-box-image">
                                                    <img src="{{ $path }}{{ $ShopItemImage->image_small ?? $ShopItemImage->image_large }}">
                                                </div>
                                            </div>
                                            
                                        </label>

                                    @endforeach

                                    <input type="hidden" name="shop_item_image_id" value="{{ $active ? $ShopModificationImage->shop_item_image_id : 0 }}" />

                                </div>
                         

                        </div>

                        <div class="tab-pane properties-block" id="properties">
                            <div class="row mb-3">
                                <div class="col-12">

                                    @foreach ($properties as $property)

                                        @if (isset($property_value_ints[$property->id]))
                                            <div class="list-group-item">
                                                @foreach ($property_value_ints[$property->id] as $propertyK => $propertyItem)
                                                    <div class="row mb-3 admin-item-property" data-property="{{ $property->id }}" data-id="{{ $propertyK }}" id="admin-item-property-{{ $property->id }}-{{ $propertyK }}">
                                                        <div class="col-10">
                                                            <label class="mb-1">{{ $property->name }}</label>
                                                            <select data-name="property_{{ $property->id }}[]" name="property_{{ $property->id }}_{{ $propertyK }}" class="form-select">
                                                                <option value="">...</option>
                                                                @foreach ($lists[$property->shop_item_list_id] as $key => $listItem)
                                                                    @php
                                                                        $isSelected = $propertyItem == $key ?  ' selected=selected ' : ''
                                                                    @endphp
                                                                    <option data-j="{{$key }}" data-l="{{$propertyK }}" {{ $isSelected }} value="{{ $key }}">{{ $listItem }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                        @else

                                            @if (isset($lists[$property->shop_item_list_id]))
                                                <div class="list-group-item">
                                                    <div class="row mb-3 admin-item-property">
                                                        <div class="col-10">
                                                            <label class="mb-1">{{ $property->name }}</label>
                                                            <select data-name="property_{{ $property->id }}[]" name="property_{{ $property->id }}[]" class="form-select">
                                                                <option value="">...</option>
                                                                @foreach ($lists[$property->shop_item_list_id] as $key => $listItem)
                                                                    <option value="{{ $key }}">{{ $listItem }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach

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
    <script>
        var adminModification = {
            select: function(elem) {
                elem.siblings("label").removeClass("active");
                elem.addClass("active");
                $("[name='shop_item_image_id']").val(elem.data("id"))
            }
        }
    </script>

@endsection