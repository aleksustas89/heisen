@extends("admin.main")

@section('title', 'Новый элемент заказа')

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
                <li class="breadcrumb-item"><a href="{{ route('shopOrder.edit', $shop_order_id) }}">Редактирование заказа № {{ $shop_order_id }}</a></li>
                <li class="breadcrumb-item">Новый элемент заказа</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">

            <div class="card" id="id_content">
                <form action="{{ route('shopOrderItem.store') }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('POST')
                    
                    <div class="card-body tab-content">

                        <div class="row">
                            <div class="col-12"> 
                                <label class="mb-1 my-2">Название</label>
                                <input type="text" name="name" value="" class="form-control form-control-lg" placeholder="Название">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4"> 
                                <label class="mb-1 my-2">ID товара</label>
                                <input type="text" name="shop_item_id" class="form-control" placeholder="Количество">
                            </div>
                            <div class="col-4"> 
                                <label class="mb-1 my-2">Количество</label>
                                <input type="text" name="quantity" class="form-control" placeholder="Количество">
                            </div>
                            <div class="col-4"> 
                                <label class="mb-1 my-2">Цена</label>
                                <input type="text" name="price" class="form-control" placeholder="Цена">
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <input type="hidden" name="shop_order_id" value="{{ $shop_order_id }}" />
                        <button type="submit" name="apply" value="1" class="btn btn-success">Применить</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    
@endsection