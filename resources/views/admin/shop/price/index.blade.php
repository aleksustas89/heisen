@extends("admin.main")

@section('title', 'Редактирование цен Интернет-магазина')

@section('breadcrumbs')
    <div class="page-title-box d-flex flex-column">
        <div class="float-start">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                @endforeach
                <li class="breadcrumb-item">Изменение цен магазина</li>
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
                    </ul>
                </div>

                <form action="{{ route('shop.shop-price.update', ['shop' => 1, 'shop_price' => 1]) }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body tab-content">

                        <div class="tab-pane active" id="main">
                            
                            <div class="row mb-3">
                                <div class="col-6 col-sm-4">
                                    <label class="mb-1">Группа</label>
                                    <label class="mb-1">&nbsp;</label>
                                    <select name="shop_group_id" class="form-control">
                                        <option value="0">...</option>
                                        @php
                                            \App\Http\Controllers\ShopGroupController::showTreeGroupsAsOptions();
                                        @endphp
                                    </select>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <label class="mb-1">Величина</label>
                                    <div class="d-flex">
                                        <div>
                                            <input type="text" value="" name="value" class="form-control" placeholder="Величина">
                                        </div>
                                        
                                        <div>
                                            <select name="type" class="form-control">
                                                <option value="0">%</option>
                                                <option value="1">+</option>
                                            </select>
                                        </div>
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