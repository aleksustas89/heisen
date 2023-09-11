@extends("admin.main")

@section('title', 'Структура сайта')

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

                <div class="card-header">
                    <a href="{{ route('structure.create') }}{{ $parent > 0 ? '?parent_id=' . $parent : '' }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить</a>
                    <a href="{{ route('structureMenu.index') }}" class="btn btn-info"><i class="fas fa-bars icon-separator"></i>Меню</a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 1%">#ID</th>
                                    <th>Название структуры</th>
                                    <th>Путь</th>
                                    <th width="200px">Меню</th>
                                    <th class="td_editable">Сортировка</th>
                                    <th width="40px">
                                        <i data-feather="eye" title="Активность"></i>
                                    </th>
                                    <th width="40px">
                                        <i data-feather="search"></i>
                                    </th>
                                    <th class="controll-td"></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($structures as $structure)
                                <tr class="{{ $structure->active == 0 ? 'off' : '' }}">
                                    <td>
                                        {{ $structure->id }}
                                    </td>
                                    <td>
                                        <div>
                                            <a class="line-through-if-off" href="?parent_id={{ $structure->id }}">{{ $structure->name }}</a>
                                            @if (($subCount = $structure->getChildCount()) > 0)
                                                <span class="badge badge-secondary admin-badge mx-1">{{ $subCount }}</span>
                                            @endif
    
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $url = "/" . $structure->path();
                                        @endphp
                                        <a class="line-through-if-off" target="_blank" href="{{ $url }}">{{ $url }}</a>
                                    </td>
                                    <td>
                                        {{ $structure->structure_menu_id > 0 ? $structure->StructureMenu->name : '' }}
                                    </td>
                                    <td class="td_editable">
                                        <div>
                                            <span id="apply_check_structure_sorting_{{ $structure->id }}" class="editable">{{ $structure->sorting }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $notActive = $structure->active == 0 ? true : false;
                                        @endphp
                                        <span onclick="toggle.init($(this))" @class([
                                            'pointer',
                                            'ico-inactive' => $notActive,
                                        ]) id="toggle_structure_active_{{ $structure->id }}">
                                            <i data-feather="eye" title="Активность"></i>
                                        </span>
                                        
                                    </td>
                                    <td>
                                        @php
                                            $notIndexing = $structure->indexing == 0 ? true : false;
                                        @endphp
                                        <span onclick="toggle.init($(this))" @class([
                                            'pointer',
                                            'ico-inactive' => $notIndexing,
                                        ]) id="toggle_structure_indexing_{{ $structure->id }}">
                                            <i data-feather="search" title="Индексация"></i>
                                        </span>
                                    </td>
                        
                                    <td class="td-actions">
                                        <a href="{{ route('structure.edit', $structure->id) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                        <form action="{{ route('structure.destroy', $structure->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete($(this).parents('form'))" class="td-list-delete-btn">
                                                <i class="las la-trash-alt text-secondary font-16"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $structures->appends($parent > 0 ? ["parent_id" => $parent] : [])->links() }}

                </div>

               

            </div>
        </div>
    </div>
    
@endsection