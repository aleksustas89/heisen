@extends("admin.main")

@section('title', 'Редактирование быстрого заказа')

@section('breadcrumbs')
    <div class="page-title-box d-flex flex-column">
        <div class="float-start">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                @endforeach
                <li class="breadcrumb-item">Редактирование быстрого заказа № {{ $shopQuickOrder->id }}</li>
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
                <form action="{{ route('shopQuickOrder.update', $shopQuickOrder->id) }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body tab-content">

                        <div class="row mb-3">
                            <div class="col-12"> 
                                <label class="mb-1 my-2">Фио</label>
                                <input type="text" name="name" value="{{ $shopQuickOrder->name }}" class="form-control form-control-lg" placeholder="Фио">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12"> 
                                <label class="mb-1 my-2">Телефон</label>
                                <input type="text" name="phone" value="{{ $shopQuickOrder->phone }}" class="form-control" placeholder="Телефон">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12"> 
                                <label class="mb-1 my-2">Id товара</label>
                                <input type="text" name="shop_item_id" value="{{ $shopQuickOrder->shop_item_id }}" class="form-control" placeholder="Id товара">
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

@section('js')       
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>                   
    <script>

        $(function(){
            $('[name="phone"]').mask("+7 (999) 999-9999", {autoclear: false});
        });
        
    </script>
@endsection