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
                    <div class="dataTable-top">

                        @if (count($colors) > 0)
                            <div class="dataTable-dropdown">
                                <label>
                                    <select onchange="Filter.init()" name="color" class="dataTable-selector">
                                        
                                        <option value="0">Все цвета</option>

                                        @foreach ($colors as $color)
                                            <option @if($color->id == $current_color_id) selected @endif value="{{ $color->id }}">{{ $color->value }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                        @endif
                    </div>

                </form>


                <div class="admin-table">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 1%">#ID</th>
                                <th>Название</th>
                                <th class="text-center" width="200px">Всего куплено</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($shopOrderItems as $shopOrderItem)
                                <tr>
                                    <td>
                                        {{ $shopOrderItem->shop_item_id }}
                                    </td>
                                    <td>
                                        {{ $shopOrderItem->ShopItem->name }}
                                    </td>
                                    <td class="text-center">
                                        {{ (int)$shopOrderItem->total_quantity }}
                                    </td>
                                </tr>
                            @endforeach



                        </tbody>
                    </table>
                </div>

                {{ $shopOrderItems->links() }}

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

@endsection