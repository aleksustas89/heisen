@extends("admin.main")

@section('title', 'Настройки Cdek отправителя')

@section('breadcrumbs')

    <div class="page-title-box d-flex flex-column">
        <div class="float-start">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                @endforeach
                <li class="breadcrumb-item">Настройки Cdek отправителя</li>
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
                
                <form action="{{ route('cdek-sender.update', $CdekSender->id) }}" method="POST" id="formEdit">
                    <div class="card card-primary">
                    @csrf
                    @method('PUT')
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="mb-1">Регион</label>
                            
                            <select name="cdek_region_id">
                                @foreach ($CdekRegions as $CdekRegion)
                                    <option data-id="{{ $CdekRegion->id }}" @if($CdekRegion->id == $CdekSender->cdek_region_id) selected @endif value="{{ $CdekRegion->id }}">{{ $CdekRegion->name }}</option>
                                @endforeach 
                            </select>

                        </div>

                        <div class="mb-3">
                            <label class="mb-1">Город</label>
                            <select name="cdek_city_id">
                                @foreach ($CdekCities as $CdekCity)
                                    <option data-id="{{$CdekCity->id}}" @if($CdekCity->id == $CdekSender->cdek_city_id) selected @endif value="{{ $CdekCity->id }}">{{ $CdekCity->name }}</option>
                                @endforeach 
                            </select>
                        </div>

                        
                        <div class="mb-3">
                            <label class="mb-1">Отделение [Отправка со склада]</label>
                            <select name="cdek_office_id">
                                @foreach ($CdekOffices as $CdekOffice)
                                    <option @if($CdekOffice->id == $CdekSender->cdek_office_id) selected @endif value="{{ $CdekOffice->id }}">{{ $CdekOffice->name }} @if(!empty($CdekOffice->address_comment)) [{{ $CdekOffice->address_comment }}] @endif</option>
                                @endforeach 
                            </select>
                        </div>
        
                        <div class="mb-3">
                            <label class="mb-1">Адрес [Отправка с адреса]</label>
                            <input name="address" type="text" class="form-control" value="{{ $CdekSender->address }}" />
                        </div>
                       

                        <div class="mb-3">
                            <label class="mb-1">Имя отправителя</label>
                            <input name="name" type="text" class="form-control" value="{{ $CdekSender->name }}" />
                        </div>

                        <div class="mb-3">
                            <label class="mb-1">Телефон отправителя</label>
                            <input name="phone" type="text" class="form-control" value="{{ $CdekSender->phone }}" />
                        </div>

                        <div class="mb-3">
                            <label class="mb-1">Тип отправки</label>
                            <select name="type">
                                @foreach ($Types as $key => $Type)
                                    <option @if($key == $CdekSender->type) selected @endif value="{{ $key }}">{{ $Type["name"] }}</option>
                                @endforeach 
                            </select>
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

    <script src="https://cdn.jsdelivr.net/npm/jquery-form-styler@2.0.2/dist/jquery.formstyler.min.js"></script>

    <script>

        $(function(){

            $('select').styler({
				selectSearch: true,
			});

            $("body").on("change", "[name='cdek_region_id']", function() {

                let value = $(this).find(":selected").data("id");

                Spiner.show();

                $.ajax({
                    url: "/get-cdek-cities",
                    type: "POST",
                    data: {"region": value},
                    dataType: "html",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $("[name='cdek_city_id']").html(data).trigger('refresh');
                        Spiner.hide();
                    },
                });

            });

            $("body").on("change", "[name='cdek_city_id']", function() {

                let value = $(this).find(":selected").data("id");

                Spiner.show();

                $.ajax({
                    url: "/get-cdek-offices",
                    type: "POST",
                    data: {"city": value},
                    dataType: "html",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $("[name='cdek_office_id']").html(data).trigger('refresh');
                        Spiner.hide();
                    },
                });

            });

            $("body").on("click", ".jq-selectbox", function() {
                if (!$(this).hasClass(".opened")) {
                    $(this).find("[type='search']").focus();
                }
            });

        });

    </script>

@endsection

@section("css")

    <link href="https://cdn.jsdelivr.net/npm/jquery-form-styler@2.0.2/dist/jquery.formstyler.min.css" rel="stylesheet">
    <link href="/css/jquery.formstyler.theme.css" rel="stylesheet">

@endsection