@extends("admin.main")

@section('title', 'Отзывы')

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
                    <a href="{{ route("comment.create") }}{{ $parent_id > 0 ? "?parent_id=" . $parent_id : "" }}" class="btn btn-success"><i class="fas fa-plus icon-separator"></i>Добавить</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 1%">№</th>
                                <th style="width: 300px">Автор</th>
                                <th style="width: 200px">Тема</th>
                                <th>Текст</th>
                                <th style="width: 100px">Оценка</th>
                                <th style="width: 100px">Активность</th>
                                <th class="td-actions"></th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($comments as $comment)

                                @php
                                    $isActive = $comment->active == 1 ? false : true;
                                    $subCount = App\Models\Comment::where("parent_id", $comment->id)->count();
                                    $href = route("comment.index") . "?parent_id=" . $comment->id;
                                    $ShopItem = !is_null($comment->CommentShopItem) && !is_null($comment->CommentShopItem->ShopItem)
                                                    ? $comment->CommentShopItem->ShopItem
                                                    : false;
                                @endphp

                                <tr @class([
                                    'off' => $isActive,
                                ])>
                                    <td>{{ $comment->id }}</td>
                                    <td>
                                        @if (!empty($comment->author))
                                            <a class="text-decoration-underline" {{ $href ? 'href=' . $href  : '' }}>{{ $comment->author }}</a>
                                            @if ($subCount > 0)
                                                <span class="badge-count btn-success">{{ $subCount }}</span>
                                            @endif

                                            @if ($ShopItem)
                                                <a href="{{ $ShopItem->url }}" target="_blank">
                                                    <i class="las la-external-link-alt"></i>
                                                </a>
                                            @endif

                                            @if (!empty($comment->email))
                                                <div class="small darkgray">E-mail: {{ $comment->email }}</div>
                                            @endif
                                            @if (!empty($comment->phone))
                                                <div class="small darkgray">Телефон: {{ $comment->phone }}</div>
                                            @endif
                                        @elseif($comment->user_id > 0 && !is_null($comment->User))

                                            <a class="text-decoration-underline" href="{{ $href }}">{{ $comment->User->name }}</a>
                                            
                                            @if ($subCount > 0)
                                                <span class="badge-count btn-success">{{ $subCount }}</span>
                                            @endif

                                            @if ($ShopItem)
                                                <a href="{{ $ShopItem->url }}" target="_blank">
                                                    <i class="las la-external-link-alt"></i>
                                                </a>
                                            @endif

                                        @endif


                                    </td>
                                    <td><span class="line-through-if-off">{{ $comment->subject }}</span></td>
                                    <td><span class="line-through-if-off">{!! $comment->text !!}</span></td>

                                    <td>
                                        @if ($comment->grade > 0)
                                            <span @class([
                                                "green" => $comment->grade == 5 ? true : false,
                                                "yellow" => $comment->grade == 4 ? true : false,
                                                "red" => $comment->grade == 3 ? true : false,
                                                "maroon" => $comment->grade < 3 ? true : false,
                                            ])>
                                                @for ($i = 1; $i <= $comment->grade; $i++)★@endfor
                                            </span>
                                        @endif
                                    </td>
                     
                                    <td class="text-center">
                                        <span onclick="toggle.init($(this))" @class([
                                            'pointer',
                                            'ico-inactive' => $isActive,
                                        ]) id="toggle_comment_active_{{ $comment->id }}">
                                
                                            <i class="lar la-lightbulb font-20"></i>
                                        </span>
                                    </td>
        
                                
                                    <td class="td-actions">
                                        <a href="{{ route('comment.edit', $comment->id) }}" class="mr-2"><i class="las la-pen text-secondary font-16"></i></a>
                                        <form action="{{ route('comment.destroy', $comment->id) }}" method="POST" class="d-inline">
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

                    {{ $comments->links() }}
                </div>
            </div>
        </div>
    </div>

    
@endsection


@section("css")
    <link href="/assets/css/colors.css" rel="stylesheet" type="text/css">
    <style>
        .table p{
            margin: 0;
            padding: 0;
        }
    </style>
@endsection

