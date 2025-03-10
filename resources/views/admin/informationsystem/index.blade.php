@extends("admin.main")

@section('title', 'Информационные системы')

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

    @if (session('error'))
        <div class="alert alert-danger border-0" role="alert">
            {{ session('error') }}
        </div>
    @endif


            
    <div class="row">
        <div class="col-12">

            <div class="card">

                <div class="card-header button-items">

                    <a href="{{ route('informationsystem.informationsystem-item.create', ['informationsystem' => $informationsystem->id]) }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить элемент</a>
                    <a href="{{ route('tag.index') }}" class="btn btn-warning"><i class="fas fa-tags icon-separator"></i>Теги</a>
                    <a class="btn btn-dark" href="{{ route('informationsystem.edit', ['informationsystem' => $informationsystem->id]) }}"><i class="fas fa-cogs icon-separator"></i>Настройки</a>

                </div>
   
                    <div class="card-body p-0">
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
                                        <th class="d-mob-none" width="40px"><i class="fa fa-lightbulb-o" title="Активность"></i></th>
                                        <th class="d-mob-none" width="60px"><i class="fas fa-sort-amount-down" title="—"></i></th>
                                        <th class="controll-td"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                 
                                    @foreach ($informationsystemItems as $informationsystemItem)

                                        @php
                                            $isActive = $informationsystemItem->active == 1 ? false : true;
                                        @endphp

                                        <tr @class([
                                            'off' => $isActive,
                                        ])>
                                            <td>
                                                <label>
                                                    <input name="informationsystem_items[{{ $informationsystemItem->id }}]" class="custom-control-input check-item" type="checkbox">
                                                </label>
                                            </td>
                                            <td>
                                                {{ $informationsystemItem->id }}
                                            </td>

                                            <td>
                                                
                                                <span class="product-name fw-semibold line-through-if-off">{{ $informationsystemItem->name }}</span>

                                                <a href="{{ $informationsystemItem->url }}" target="_blank">
                                                    <i class="las la-external-link-alt"></i>
                                                </a> 
                                            </td>
                                            
                    
                                            <td class="d-mob-none">

                                                <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="активен/не активен " onclick="toggle.init($(this))" onclick="toggle.init($(this))" @class([
                                                    'pointer',
                                                    'ico-inactive' => $isActive,
                                                ]) id="toggle_InformationsystemItem_active_{{ $informationsystemItem->id }}">
                                        
                                                    <i class="lar la-lightbulb font-20"></i>
                                                </span>

                                            </td>
                                        
                                            
                                            <td class="d-mob-none" width="60px" class="td_editable"><span id="apply_check_informationsystemItem_sorting_{{ $informationsystemItem->id }}" class="editable">{{ $informationsystemItem->sorting }}</span></td>

                                            <td class="td-actions">
                                                <a href="{{ route('informationsystem.informationsystem-item.edit', ['informationsystem' => $informationsystemItem->informationsystem_id, 'informationsystem_item' => $informationsystemItem->id]) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                                <a href="javascript:void(0)" class="mr-2 deleting" onclick="Operation.set('delete')"><i class="las la-trash-alt text-secondary font-16"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach 
                                    
                                </tbody>
                            </table>

                            <div class="card-footer text-start">
                                <input type="hidden" name="operation" />
                                <button type="submit" class="btn btn-sm btn-danger group-deleting" onclick="Operation.set('delete')">
                                    <i class="las la-trash-alt font-16"></i>
                                    Удалить
                                </button>
                            </div>

                        </form>

                        {{ $informationsystemItems->links() }}
                    </div>
            </div>
        </div>
    </div>

@endsection


