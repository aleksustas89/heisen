@extends("admin.main")

@section('title', 'Меню сайта')

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
                    <a href="{{ route('structure-menu.create') }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить</a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 1%">#ID</th>
                                    <th>Название</th>
                                    <th class="controll-td"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($menus as $menu)
                                    <tr>
                                        <td>
                                            {{ $menu->id }}
                                        </td>
                                        <td>
                                            <div>
                                                {{ $menu->name }}
                                            </div>
                                        </td>
                                      
                                        <td class="td-actions">
                                            <a href="{{ route('structure-menu.edit', ['structure_menu' => $menu->id]) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                            <form action="{{ route('structure-menu.destroy', ['structure_menu' => $menu->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="if(confirm('Вы действительно хотите удалить?')){$(this).parents('form').submit()}" class="td-list-delete-btn">
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
    </div>
    
@endsection