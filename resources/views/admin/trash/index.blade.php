@extends("admin.main")

@section('title', 'Корзина')

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

    @if (session('success'))
        <div class="alert alert-success border-0" role="alert">
            {{ session('success') }}
        </div>
    @endif
            
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-body">
                    <div class="table-responsive">
                        <form class="admin-table"> 
                     
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 1%">
                                            <label>
                                                <input class="custom-control-input check-all" type="checkbox">
                                            </label>
                                        </th>
                                        <th style="width: 1%">#ID</th>
                                        <th>Название</th>
                                        <th>Количество</th>
                                        <th class="td-actions"></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @if($root)

                                        @foreach ($models as $key => $model)
                                            @php
                                                $count = $model['model']->count();
                                            @endphp
                                            @if ($count > 0)
                                                <tr>
                                                    <td style="width: 1%"></td>
                                                    <td style="width: 1%"></td>
                                                    <td><a href="?model={{ $key }}">{{ $model['name'] }}</a></td>
                                                    <td>{{ $count }}</td>
                                                    <td class="td-actions">
                                                        <a href="javascript:void(0)" onclick="Set.value('operation', 'restore'); Set.value('model', {{ $key }}); Confirm.init($(this))" class="mr-2" data-confirmText="Вы уверенны, что хотите восстановить?">
                                                            <i class="las la-trash-restore text-secondary font-16"></i>
                                                        </a>
                                                        <a href="javascript:void(0)" onclick="Set.value('operation', 'delete'); Set.value('model', {{ $key }}); Confirm.init($(this))" class="mr-2" data-confirmText="Вы уверенны, что хотите удалить?">
                                                            <i class="las la-trash-alt text-secondary font-16"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach

                                    @else

                                        @php
                                            $property = isset($models[request()->model]['fieldToShow']) ? $models[request()->model]['fieldToShow'] : 'name';
                                        @endphp

                                        @if ($Items)
                                            @foreach ($Items as $Item)
                                                <tr>
                                                    <td>
                                                        <label>
                                                            <input name="items[{{ $Item->id }}]" class="custom-control-input check-item" type="checkbox">
                                                        </label>
                                                    </td>
                                                    <td style="width: 1%">{{ $Item->id }}</td>
                                                    <td><a>{{ $Item->$property }}</a></td>
                                                    <td></td>
                                                    <td class="td-actions">
                                                        <a href="javascript:void(0)" onclick="Set.value('operation', 'restore'); Set.value('model', {{ request()->model }}); Confirm.init($(this))" class="mr-2" data-confirmText="Вы уверенны, что хотите восстановить?">
                                                            <i class="las la-trash-restore text-secondary font-16"></i>
                                                        </a>
                                                        <a href="javascript:void(0)" class="mr-2" onclick="Set.value('operation', 'delete'); Set.value('model', {{ request()->model }}); Confirm.init($(this))" data-confirmText="Вы уверенны, что хотите удалить?">
                                                            <i class="las la-trash-alt text-secondary font-16"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif

                                    @endif
                        
                                </tbody>
                            </table>

                            <input type="hidden" name="operation" value="" />
                            <input type="hidden" name="model" value="{{ request()->model }}" />

                        </form>
                    </div>

                    @if ($Items)
                        {{ $Items->appends(request()->model > 0 ? ["model" => request()->model] : [])->links() }}
                    @endif

                </div>

            </div>
        </div>
    </div>
    
@endsection

@section("js")

<script>

    var Set = {
        value: function(input, value) {

            if ($("[name="+ input +"]").length) {
                $("[name="+ input +"]").val(value);
            }
        }
    }


    var Confirm = {

        init: function($this) {

            let form = $this.parents("form");

            Swal.fire({
                title: $this.attr("data-confirmText"),
                showCancelButton: true,
                confirmButtonText: 'Да',
                cancelButtonText: 'Отмена',
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log(1)
                    return form.submit();
                } else if (result.isDenied) {
                    console.log(0)
                    return false
                }
            })

            return false;
        }
    }

</script>

@endsection