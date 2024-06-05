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
                            <a class="nav-link active" href="#individual" data-bs-toggle="tab" role="tab">
                                Выборочно
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#main" data-bs-toggle="tab" role="tab">
                                Глобально
                            </a>
                        </li>
                    </ul>
                </div>


                    
                    <div class="card-body tab-content">

                        <div class="tab-pane active" id="individual">

                            <form action="{{ route('shop.shop-price.update', ['shop' => 1, 'shop_price' => 1]) }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                                @csrf
                                @method('PUT')

                                <input type="text" name="search" class="form-control" placeholder="Поиск по товарам">

                                <div id="search_price_result"></div>

                                <input name="type" type="hidden" value="0" />

                                <div class="card-footer">
                                    <button type="submit" name="save" value="0" class="btn btn-primary">Сохранить</button>
                                    <button type="submit" name="apply" value="1" class="btn btn-success">Применить</button>
                                </div>
                            </form>


                        </div>

                        <div class="tab-pane" id="main">

                            <form action="{{ route('shop.shop-price.update', ['shop' => 1, 'shop_price' => 1]) }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                                @csrf
                                @method('PUT')

                                <input name="type" type="hidden" value="1" />
                            
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

                                <div class="card-footer">
                                    <button type="submit" name="save" value="0" class="btn btn-primary">Сохранить</button>
                                    <button type="submit" name="apply" value="1" class="btn btn-success">Применить</button>
                                </div>
                            </form>

                        </div>

                    </div>

            </div>

        </div>
    </div>

    
@endsection

@section("js")

    <script>

        var routeShopPriceFilter = '{{ route("shopPriceFilter") }}';

        $("[name='search']").keyup(function() 
        {
            $this = $(this);

            delay(function() {

                if ($this.length > 0) {
                    $.ajax({
                        url: routeShopPriceFilter,
                        type: "GET",
                        data: {
                            "search": $("[name='search']").val()
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "html",
                        success: function (data) {
                            $("#search_price_result").html(data);
                        },
                    });
                } else {
                    $("#search_price_result").html("");
                } 

            }, 1000 );
        });

    </script>

@endsection