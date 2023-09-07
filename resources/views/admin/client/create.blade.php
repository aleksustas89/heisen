@extends("admin.main")

@section('title', 'Новый клиент')

@section('breadcrumbs')

    <div class="page-title-box d-flex flex-column">
        <div class="float-start">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                @endforeach
                <li class="breadcrumb-item">Новый клиент</li>
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

                <div class="p-2">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#main" data-bs-toggle="tab" role="tab">
                                <i class="la la-home " title="Основные"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <form action="{{ route('client.store') }}" method="POST" id="formEdit">
                    @csrf

                    <div class="card card-primary">
                        <div class="card-body">

                            <div class="mb-3">
                                <label class="mb-1">E-mail</label>
                                <input id="email" data-reg="^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$" type="text" name="email" value="" class="form-control input-lg" placeholder="E-mail пользователя" data-min="2"  data-max="255" data-required="1">
                                <div id="email_error" class="fieldcheck-error"></div>
                            </div>

                            <div class=" mb-3">
                                <label class="mb-1">Фамилия, Имя</label>
                                <input id="name" type="text" name="name" value="" class="form-control input-lg" placeholder="Фамилия, Имя пользователя" data-min="2"  data-max="255" data-required="1">
                                <div id="name_error" class="fieldcheck-error"></div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                             
                                    <label class="mb-1">Пароль</label>
                                    <div>
                                        <input type="text" name="password_first" value="" class="form-control" placeholder="Пароль" id="password_first" data-min="9" data-required="1">
                                        <div id="password_first_error" class="fieldcheck-error"></div>
                                    </div>
                               
                                </div>
                                <div class="col-6">
                                  
                                    <label class="mb-1">Подтверждение пароля</label>
                                    <div>
                                        <input type="text" name="password_second" id="password_second" value="" data-equality="password_first" data-min="9" class="form-control" placeholder="Подтверждение пароля" data-required="1">
                                        <div id="password_second_error" class="fieldcheck-error"></div>
                                    </div>
                                
                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <button type="submit" name="save" value="0" class="btn btn-primary" disabled>Сохранить</button>
                            <button type="submit" name="apply" value="1" class="btn btn-success" disabled>Применить</button>
                        </div>
                    </div>
                </form>

            </div>
            
        </div>
    </div>

    
@endsection