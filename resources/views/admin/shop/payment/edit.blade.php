@extends("admin.main")

@section('title', 'Редактирование платежной системы')

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
                <li class="breadcrumb-item">Редактирование платежной системы</li>
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

    <div class="row">
        <div class="col-12">

            <div class="card" id="id_content">
                <form action="{{ route('shop.shop-payment-system.update', ['shop' => $shop->id, 'shop_payment_system' => $shopPaymentSystem->id]) }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body tab-content">

                        <div class="row mb-3">
                            <div class="col-12"> 
                                <label class="mb-1 my-2">Название платежной системы</label>
                                <input type="text" name="name" value="{{ $shopPaymentSystem->name }}" class="form-control" placeholder="Название платежной системы">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6"> 
                                <label class="mb-1 my-2">Сортировка</label>
                                <input type="text" name="sorting" value="{{ $shopPaymentSystem->sorting }}" class="form-control" placeholder="Сортировка">
                            </div>
                            <div class="col-6 d-flex align-items-end">
                                
                                <div class="d-flex">

                                    <div class="form-check form-switch form-switch-success"> 
                                        <input value="1" @if ($shopPaymentSystem->active == 1) checked="" @endif class="form-check-input" name="active" type="checkbox" id="active">
                                        <label for="active">
                                            Активность
                                        </label>
                                    </div>
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

