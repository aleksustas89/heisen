@extends("admin.main")

@section('title', 'Настройки Boxberry отправителя')

@section('breadcrumbs')

    <div class="page-title-box d-flex flex-column">
        <div class="float-start">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb["url"] }}">{{ $breadcrumb["name"] }}</a></li>
                @endforeach
                <li class="breadcrumb-item">Настройки Boxberry отправителя</li>
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
                
                <form action="{{ route('boxberry-sender.update', $BoxberrySender->id) }}" method="POST" id="formEdit">
                    <div class="card card-primary">
                    @csrf
                    @method('PUT')
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="mb-1">Название отделения</label>
                            <input name="name" type="text" class="form-control" value="{{ $BoxberrySender->name }}" />
                            <input name="boxberry_office_id" type="hidden" value="{{ $BoxberrySender->boxberry_office_id }}" />
                        </div>

                        <p>
                            <a href="javascript:void(0)" class="btn btn-outline-boxberry active" data-bs-toggle="modal" data-bs-target="#boxberryModal">Выбрать отделение</a>
                        </p>

                        <div class="modal fade" id="boxberryModal" tabindex="-1" aria-labelledby="boxberryModal" aria-hidden="true">
                            <div class="modal-dialog" style="max-width: 90%; padding: 50px 30px;">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-content p-3">
                                    <div id="boxberry_map">
                                    </div>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                    <div class="card-footer">
                        <button type="submit" name="save" value="0" class="btn btn-primary">Сохранить</button>
                        <button type="submit" name="apply" value="1" class="btn btn-success">Применить</button>
                    </div>
                </form>

            </div>
            
        </div>
    </div>


    
@endsection

@section("js")


    <script type="text/javascript" src="//points.boxberry.ru/js/boxberry.js"> </script>

    <script>

        var BoxberryToken = '{{ isset($Boxberry) ? $Boxberry->token : '' }}';
        boxberry.openOnPage('boxberry_map');
        boxberry.open(boxberry_callback, BoxberryToken,'','', 1000, 500, 0, 20, 20, 20);

        function boxberry_callback(result) {

            $("[name='name']").val(result.address)
            $("[name='boxberry_office_id']").val(result.id);

            $("#boxberryModal").find(".btn-close").click();
        }

    </script>

@endsection