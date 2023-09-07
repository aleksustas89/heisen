@extends("admin.main")

@section('title', 'Клиенты')

@section('breadcrumbs')

<div class="page-title-box d-flex flex-column">
    <div class="float-start">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)
                <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
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
                    <a href="{{ route("client.create") }}" class="btn btn-success">Добавить</a>
                </div>
    
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 1%">#ID</th>
                                    <th>E-mail</th>
                                    <th>Фамилия, Имя</th>
                                    <th width="40px"><i class="fa fa-lightbulb-o" title="Активность"></i></th>
                                    <th class="controll-td"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $client)
                                    <tr>
                                        <td>
                                            {{ $client->id }}
                                        </td>
                                        <td>
                                            <div>
                                                {{ $client->email }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                {{ $client->name}}
                                            </div>
                                        </td>
                                        <td>
                                            @if ($client->active == 1)
                                                <i class="fa fa-lightbulb-o" title="Активность"></i>
                                            @else
                                                <i class="fa fa-lightbulb-o fa-inactive" title="Активность"></i>
                                            @endif
                                        </td>
                                        <td class="td-actions">
                                            <a href="{{ route('client.edit', $client->id) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                            <form action="{{ route('client.destroy', $client->id) }}" method="POST" style="display:inline-block;">
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
    </div>

@endsection