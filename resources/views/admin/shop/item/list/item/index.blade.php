@extends("admin.main")

@section('title', 'Элементы списка')

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
                    <a href="{{ route('shopItemListItem.create') }}?list_id={{ $list_id }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить</a>
                </div>

                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 1%">#ID</th>
                                <th>Значение</th>
                                <th width="40px">
                                    <i data-feather="eye" title="Активность"></i>
                                </th>
                                <th class="controll-td"></th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($items as $item)
                                <tr>
                                    <td>
                                        {{ $item->id }}
                                    </td>

                                    <td>
                                        {{ $item->value }}
                                    </td>

                                    <td width="40px">
                                        @php
                                            $active = $item->active == 1 ? 'eye' : 'eye-off'
                                        @endphp

                                        <i data-feather="{{ $active }}" title="Активность"></i>
                                    </td>

                                    <td class="td-actions">
                                        <a href="{{ route('shopItemListItem.edit', $item->id) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                        <form action="{{ route('shopItemListItem.destroy', $item->id) }}" method="POST" style="display:inline-block;">
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
                
            </div>
        </div>
    </div>

    
@endsection