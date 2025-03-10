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

                    <a href="{{ route('tag.create') }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить элемент</a>
                </div>
   
                    <div class="card-body p-0">
                        <div class="admin-table">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 1%">#ID</th>
                                        <th>Название</th>
                                        <th>Кол-во элементов</th>
                                        <th class="controll-td"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                 
                                    @foreach ($tags as $tag)

                                        <tr>
                                            <td>
                                                {{ $tag->id }}
                                            </td>

                                            <td>
                                                <span class="product-name fw-semibold line-through-if-off">{{ $tag->name }}</span>
                                            </td>

                                            <td>
                                                <span>{{ $tag->informationsystemItemTags()->count() }}</span>
                                            </td>
                                            
                                            <td class="td-actions">
                                                <form action="{{ route('tag.destroy', $tag->id) }}" method="POST" class="d-inline">
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

                        {{ $tags->links() }}
                    </div>
            </div>
        </div>
    </div>

@endsection


