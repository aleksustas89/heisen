@extends("admin.main")

@section('title', 'Новый товар')

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
                <li class="breadcrumb-item">Создание модификаций</li>
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
        <div class="card" id="id_content">
            <div class="card-primary">
                <div class="card-header">
                    <div>Создание модификаций</div>
                </div>
                <div class="card-body">

                    <form id="create_modifications">

                        @if (count($properties) > 0)
                        
                            <div class="tab-pane properties-block" id="properties">
                                @foreach ($properties as $property)

                                    @switch($property->type)
                                        
                                        @case(4)

                                            @if (isset($lists[$property->shop_item_list_id]))
                                                <div class="list-group-item">
                                                    <div class="row mb-3 admin-item-property">
                           
                                                        <div class="col-10">
                                                            <label class="mb-1">{{ $property->name }}</label>

                                                            @foreach ($lists[$property->shop_item_list_id] as $key => $listItem)

                                                                <div class="form-check form-switch form-switch-success">
                                                                    <input class="form-check-input" name="property_{{ $property->id }}[]" value="{{ $key }}" type="checkbox" id="{{ $property->id }}{{ $key }}">
                                                                    <label class="form-check-label" for="{{ $property->id }}{{ $key }}">{{ $listItem }}</label>
                                                                </div>
                                                        
                                                            @endforeach

                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                        @break
                                        @default
                                            
                                    @endswitch

                                @endforeach
                            </div>
                        @endif

                        <input type="hidden" name="shop_item_id" value="{{ $shop_item_id }}" />

                    </form>

                </div>

                <div class="card-footer">
                    <button type="button" onclick="adminModification.create()" name="create" value="1" class="btn btn-primary">Создать модификации</button>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section("js")
    <script>
        var adminModification = {
            create: function() {
                $.ajax({
                    url: '{{ route("modification.create") }}?window=1',
                    type: "GET",
                    data: $("#create_modifications").serialize(),
                    dataType: "html",
                    success: function (data) {
                        if($("#Modal").length) {
                            $("#Modal").remove();
                        }
                        $("body").append(data);
                        var modal = new bootstrap.Modal(document.querySelector('#Modal'));
                        modal.show();
                    }, 
                    error: function () {
                        //location.reload();
                    }
                });
            },
            delete: function(elem) {
                
                let count = elem.parents("table").find("tr").length;
                elem.parents("tr").remove();

                if (count == 1) {
                    $('#Modal, .modal-backdrop').remove();
                }
            }, 
            showImages: function(key) {

                $("#choose_modification_name").text($("[name='item_"+ key +"_name']").val());

                $("[name='item_"+ key +"_image']").addClass("active-image");

                $("#create_modifications_table").hide();
                $("#choose-mod-image-container").show();

            },
            chooseImg: function(elem) {

                $(".active-image").val(elem.data("id"));
                let replace = $(".active-image").siblings("i").length ? $(".active-image").siblings("i") : $(".active-image").siblings(".chosen-mod-image");
                $(".active-image").siblings(replace).replaceWith('<div class="chosen-mod-image" style="background-image: url(' + elem.data("img") + ')"><span onclick="adminModification.deleteImage($(this))" class="alert-danger"><i class="las la-trash-alt"></i></span></div>');

                $("#create_modifications_table").show();
                $("#choose-mod-image-container").hide();
            },
            deleteImage: function(elem) {
                elem.parent().siblings("[type='hidden']").val("");
                elem.parent().replaceWith('<i class="la la-image font-40" title=""></i>');
                
            }
        }
    </script>
@endsection
