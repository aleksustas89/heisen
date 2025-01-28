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

                        <div class="col-6">
                            <div class="mt-3 dataTable-dropdown">

                                <div>
                                    <b>Цвет</b>
                                </div>
    
                                <div>
                                    <select name="color" class="form-control">
                                        
                                        <option value="0">Все цвета</option>
    
                                        @foreach ($Colors as $color)
                                            <option @if($color->id == $current_color_id) selected @endif value="{{ $color->id }}">{{ $color->value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                            </div>
    
                            <div class="row mt-3">

                                <div class="col-6">
                                    <div>
                                        <b>Цена</b>
                                    </div>

                                    <div class="d-flex gap-1 align-items-center">
                                        <input type="text" name="price_from" value="{{ request()->price_from ?? '' }}" class="form-control" placeholder="от">
        
                                        <input type="text" name="price_to" value="{{ request()->price_to ?? '' }}" class="form-control" placeholder="до">
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div>
                                        <b>Время/Дата</b>
                                    </div>
        
                                    <div class="d-flex gap-1 align-items-center">
                                        <input id="date_range" readonly name="datetime" value="{{ request()->datetime ?? '' }}" class="form-control" placeholder="Дата">
                                    </div>
                                </div>
    
                            </div>

                        </div>

                        <div class="col-6">
                            <div class="mt-3 dataTable-dropdown">
                                <div class="col-12">
                                    <div>
                                        <b>Группы</b>
                                    </div>
        
                                    <select name="shop_group_id" onchange="Filter.getShopGroupItems($(this).val())" class="form-control">
                                        <option value="0">...</option>
                                        @php
                                            \App\Http\Controllers\ShopGroupController::showTreeGroupsAsOptions($groupShopItems && request()->shop_group_id ? request()->shop_group_id : 0);
                                        @endphp
                                    </select>
                                </div>
                            </div>
        
                            <div class="mt-3 dataTable-dropdown">
                                <div class="col-12">
        
                                    <div>
                                        <b>Товары</b>
                                    </div>
        
                                    <div id="shop_items" style="overflow-y: auto; max-height: 300px;">
                                        @if ($groupShopItems)
                                            @include("admin.statistic.shop-items", [
                                                "shopItems" => $groupShopItems,
                                                "aCheckedIds" => $aCheckedIds
                                            ])
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center my-3">
                        <a href="{{ route("statistic.index") }}" class="btn btn-warning"> Сбросить фильтр </a>
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-check"></i> Применить 
                        </button>
                    </div>
                </form>

                <div class="admin-table">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 100px">Артикул</th>
                                <th style="width: 300px">Название</th>
                                <th style="width: 100px">Цена</th>
                                <th >Цвета</th>
                                <th class="text-center" style="width: 160px">Всего куплено</th>
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
                                        <td>
                                            {{ \App\Services\Helpers\Str::price($shopItem['price']) }}
                                        </td>
                                        <td>
                                        
                                            @if (isset($shopItem["colors"]) && count($shopItem["colors"]) > 0)
                                                @foreach ($shopItem["colors"] as $color => $count)
                                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $Colors[$color]->value }}: {{ $count }}" class="color" style="background-image: url({{ $Colors[$color]->description }})">
                                                        {{ $count }}
                                                    </span>
                                                @endforeach
                                            @endif 
                                        </td>
                                        <td class="text-center">
                                            {{ $shopItem['quantity'] }}
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
            </div>

        </div>
    </div>
</div>

@endsection

@section("js")

    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js" crossorigin="anonymous"></script>

    <script src="/assets/js/jquery.datepicker.extension.range.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">

    <script>

        $(function() {
            $('#date_range').datepicker({
                range: 'period', 
                numberOfMonths: 2,
                dateFormat: 'dd.mm.yy', 
                onSelect: function(dateText, inst, extensionRange) {
                    $('#date_range').val(extensionRange.startDateText + ' - ' + extensionRange.endDateText);
                    addClearButton(inst.input[0], inst); 
                },
                monthNames: [ "Янв", "Фев", "Мар", "Апр", "Май", "Июнь", "Июль", "Авг", "Сен", "Окт", "Ноя", "Дек" ],
                dayNamesMin: [ "Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб" ],
                firstDay: 1,
                showAnim: "fold",
                showTodayButton: true, 
                showButtonPanel: true,
        
                beforeShow: function (input, inst) {
                    addClearButton(input, inst);
                },
                onChangeMonthYear: function (year, month, inst) {
                    addClearButton(inst.input[0], inst); 
                }
            });

            function addClearButton(input, inst) {
                setTimeout(function () {
                    var buttonPane = $(inst.dpDiv).find(".ui-datepicker-buttonpane");
                    if (buttonPane.find(".ui-datepicker-clear").length === 0) {
                        $("<button>", {
                            text: "Очистить",
                            class: "ui-datepicker-clear ui-state-default ui-priority-primary ui-corner-all",
                            click: function () {
                                $(input).val('');
                                $(input).datepicker("hide"); 
                            }
                        }).appendTo(buttonPane);
                    }
                }, 1);
            }

        });

        var SwitchAll = {
            init: function(elem) {

                elem.parents("tr").siblings("tr").each(function(){
                    if (elem.prop("checked")) {
                        $(this).find("input").prop("checked", true);
                    } else {
                        $(this).find("input").prop("checked", false);
                    }
                });
            }
        }

        var Filter = {

            init: function() {

                $("#filter").submit();
            }, 

            getShopGroupItems(shop_group_id) {

                $.ajax({
                    url: '{{ route("statisticGetGroupItems") }}',
                    data: {"shop_group_id": shop_group_id},
                    type: "GET",
                    dataType: "html",
                    success: function (data) {
                        $("#shop_items").html(data);
                    }
                });                
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