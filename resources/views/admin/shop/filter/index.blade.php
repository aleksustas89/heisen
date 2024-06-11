@extends("admin.main")

@section('title', 'Статические фильтры интернет-магазина')

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
                <div class="card-header button-items">
                    <a href="{{ route("shop.shop-filter.create", ['shop' => $shop->id]) }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить</a>

                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 1%">№</th>
                                <th>Группа</th>
                                <th>Url</th>
                                <th class="td-actions"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($shopFilters as $shopFilter)
                                <tr>
                                    <td>{{ $shopFilter->id }}</td>
                                    <td>{{ $shopFilter->ShopGroup->name }}</td>
                                    <td><a target="_blank" href="https://{{ request()->getHost() }}{{ $shopFilter->url }}">{{ $shopFilter->url }}</a></td>
                                    <td class="td-actions">
                                        <a href="{{ route('shop.shop-filter.edit', ['shop' => $shop->id, 'shop_filter' => $shopFilter->id]) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                        <form action="{{ route('shop.shop-filter.destroy', ['shop' => $shop->id, 'shop_filter' => $shopFilter->id]) }}" method="POST" class="d-inline">
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

                    {{ $shopFilters->links() }}
                </div>
            </div>
        </div>
    </div>

    
@endsection