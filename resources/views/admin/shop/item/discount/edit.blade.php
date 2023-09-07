@extends("admin.main")

@section('title', 'Новый товар')

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
                <li class="breadcrumb-item">Применить скидку</li>
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
            
            <form action="{{ route('shopItemDiscount.update', $ItemDiscount->id) }}" method="POST" id="formEdit" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-primary">
                    <div class="card-body tab-content">

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="mb-1">Название скидки</label>
                                <select class="form-control" name="shop_item_discount_id">
                                    @foreach ($shopDiscounts as $shopDiscount)
                                        <option {{ $shopDiscount->id == $ItemDiscount->shop_discount_id ? 'selected' : '' }} value="{{ $shopDiscount->id }}">{{ $shopDiscount->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="radio-group" role="group">
                            
                            <label class="checkbox-inline">
                                <input value="1" type="radio" class="btn-check" name="action" autocomplete="off" checked>
                                <span class="btn btn-success">Применить к товару</span>
                            </label>
                            
                            <label class="checkbox-inline">
                                <input value="2" type="radio" class="btn-check" name="action" autocomplete="off">
                                <span class="btn btn-warning">Применить к модификациям</span>
                            </label>

                            <label class="checkbox-inline">
                                <input value="3" type="radio" class="btn-check" name="action" autocomplete="off">
                                <span class="btn btn-dark">Отменить скидку</span>
                            </label>
                          
                            <label class="checkbox-inline">
                                <input value="4" type="radio" class="btn-check" name="action" autocomplete="off">
                                <span class="btn btn-danger">Отменить у модификаций</span>
                            </label>
                        </div>

                    </div>
                    <div class="card-footer">
                        <input type="hidden" name="shop_item_id" value="{{ $ItemDiscount->shop_item_id }}" />
                        <button type="submit" name="save" value="0" class="btn btn-primary">Сохранить</button>
                        <button type="submit" name="apply" value="1" class="btn btn-success">Применить</button>
                    </div>
                </div>
                
            </form>
            
        </div>
    </div>
</div>
    
@endsection