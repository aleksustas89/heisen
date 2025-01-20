@extends("admin.main")

@section('title', 'Статистика Интернет-магазина')

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
        </ol>
    </div>
</div>

@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">                      
                        <h4 class="card-title">Отчет продаж</h4>                      
                    </div>
                </div>                               
            </div>

            <div class="card-body p-0">

                <form id="filter" method="get" class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
                    <div class="dataTable-top row">

                    
                        <div class="col-2 dataTable-dropdown">

                            <div>
                                <b>Цвет</b>
                            </div>

                            <div>
                                <select onchange="Filter.init()" name="color" class="form-control">
                                    
                                    <option value="0">Все цвета</option>

                                    @foreach ($Colors as $color)
                                        <option @if($color->id == $current_color_id) selected @endif value="{{ $color->id }}">{{ $color->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                        </div>
                     

                        <div class="col-3">

                            <div>
                                <b>Время/Дата</b>
                            </div>

                            <div class="d-flex gap-3 align-items-center">
                                <input type="datetime-local" name="datetime_from" value="{{ request()->datetime_from ?? '' }}" class="form-control" placeholder="Дата от">

                                <input type="datetime-local" name="datetime_to" value="{{ request()->datetime_to ?? '' }}" class="form-control" placeholder="Дата до">
    
                                <a href="javascript:void(0)" onclick="Filter.init()"><i class="ti ti-check"></i></a>
                            </div>

                        </div>

                    </div>

                </form>


                <div class="admin-table">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 100px">#ID</th>
                                <th style="width: 300px">Название</th>
                                <th class="">Всего куплено</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if (count($shopItems) > 0)

                                @foreach ($shopItems as $marking => $shopItem)
                                    <tr>
                                        <td>
                                            {{ $marking }}
                                        </td>
                                        <td>
                                            {{ $shopItem['name'] }}
                                        </td>
                                        <td class="">
                                        
                                            @if (isset($shopItem["colors"]) && count($shopItem["colors"]) > 0)
                                                @foreach ($shopItem["colors"] as $color => $count)
                                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $Colors[$color]->value }}: {{ $count }}" class="color" style="background-image: url({{ $Colors[$color]->description }})">
                                                        {{ $count }}
                                                    </span>
                                                @endforeach
                                            @endif 
                                        </td>
                                    </tr>
                                @endforeach

                            @else
                                <tr>
                                    <td colspan="3">
                                        <p class="text-center">Нет результатов :(</p>
                                    </td>
                                </tr>
                            @endif



                        </tbody>
                    </table>
                </div>

                {{-- {{ $shopItems->links() }} --}}

            </div>

        </div>
    </div>
</div>

@endsection

@section("js")

    <script>

        var Filter = {

            init: function() {

                $("#filter").submit();
            }
        }

    </script>

@endsection

@section("css")

    <link href="/assets/plugins/datatables/datatable.css" rel="stylesheet" type="text/css" />

    <style>
        .color {
            background-size: cover;
            background-position: center center;
            border: 2px solid #fff;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
    </style>


@endsection