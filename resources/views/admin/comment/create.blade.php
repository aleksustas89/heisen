@extends("admin.main")

@section('title', 'Редактирование Интернет-магазина')

@section('breadcrumbs')
    <div class="page-title-box d-flex flex-column">
        <div class="float-start">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                @endforeach
                <li class="breadcrumb-item">Новый комментарий</li>
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
        <div class="col-lg-12">

            <div class="card" id="id_content">

                <form action="{{ route('comment.store') }}" method="POST" id="formEdit" enctype="multipart/form-data">
             
                    @csrf
                    @method('POST')
                    
                    <div class="card-body tab-content">

                        <div class="mb-3">
                            <label class="mb-1">Тема</label>
                            <input id="subject" type="text" name="subject" value="" class="form-control form-control-lg" placeholder="Тема" data-min="2"  data-max="255" data-required="1">
                            <div id="subject_error" class="fieldcheck-error"></div>
                        </div>

                        <div class="mb-3">
                            <label class="mb-1">Текст комментария</label>
                            <textarea type="text" name="text" class="form-control" placeholder="Текст комментария"></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 col-sm-4">
                                <label class="mb-1">Автор</label>
                                <input type="text" name="author" value="" class="form-control" placeholder="Автор">
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="mb-1">E-mail</label>
                                <input type="text" name="email" value="" class="form-control" placeholder="E-mail">
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="mb-1">Телефон</label>
                                <input type="text" name="phone" value="" class="form-control" placeholder="Телефон">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 col-sm-4">
                                <label class="mb-1">Оценка (1-5)</label>
                                <input type="text" name="grade" value="" class="form-control" placeholder="Оценка (1-5)">
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="mb-1">#ID товара</label>
                                <input type="text" name="shop_item_id" value="" class="form-control" placeholder="#ID товара">
                            </div>
                            <div class="col-12 col-sm-4">
                                <label>&nbsp;</label>
                                <div class="form-check form-switch form-switch-success field-check-center">
                                    <div>
                                        <input value="1" class="form-check-input" name="active" type="checkbox" id="active" checked="">
                                        <label class="form-check-label" for="active">Активность</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <input type="hidden" name="parent_id" value="{{ $parent_id }}" />
                        <button type="submit" name="save" value="0" class="btn btn-primary">Сохранить</button>
                        <button type="submit" name="apply" value="1" class="btn btn-success">Применить</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    
@endsection