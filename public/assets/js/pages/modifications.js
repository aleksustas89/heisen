var adminModification = {

    chooseProperties: function(route) {
        $.ajax({
            url: route,
            type: "GET",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "html",
            success: function (data) {
                $("#modal-modifications").find(".modal-body").html(data);
            }, 
            error: function () {
            }
        });
    },

    createModifications(form)
    {

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: form.serialize(),
            dataType: "html",
            success: function (data) {

                $("#modifications-content").html(data);

                $('#create_modifications').modal('hide');

            }, 
            error: function () {
            }
        });


    },

    createWindow: function(route) {

        $('#modal-modifications').modal('hide');

        $.ajax({
            url: route,
            type: "GET",
            data: $("#choose_properties").serialize(),
            dataType: "html",
            success: function (data) {
                if($("#create_modifications").length) {
                    $("#create_modifications").remove();
                }
                $("body").append(data);
                var modal = new bootstrap.Modal(document.querySelector('#create_modifications'));
                modal.show();
            }, 
            error: function () {
            }
        });
    },
    delete: function(elem) {
        
        let count = elem.parents("table").find("tr").length;
        elem.parents("tr").remove();

        if (count == 1) {
            $('#create_modifications, .modal-backdrop').remove();
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
        $(".active-image").removeClass("active-image");
    },
    deleteImage: function(elem) {
        elem.parent().siblings("[type='hidden']").val("");
        elem.parent().replaceWith('<i class="la la-image font-40" title=""></i>');
        
    },

    backToChooseProperties(){
        $('#create_modifications').modal('hide');
        $('#modal-modifications').modal('show');
    },
    deleteModification: function(route, elem) {

        Swal.fire({
            title: "Вы уверенны, что хотите удалить?",
            showCancelButton: true,
            confirmButtonText: 'Да',
            cancelButtonText: 'Отмена',
        }).then((result) => {
            if (result.isConfirmed) {
                
                $.ajax({
                    url: route,
                    type: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function (data) {

                        if (data == true) {
                            elem.parents("tr").remove();
                        }
                    }, 
                    error: function () {
                        //location.reload();
                    }
                });

            } else if (result.isDenied) {
                return false
            }
        })
    },
}

$("[name='default_modification']").change(function() {

    let route = $(this).attr("data-route"),
        $this = $(this);   
        
        $this.parents("tr").addClass("default");
        $this.parents("tr").siblings("tr").removeClass("default");

        $.ajax({
            url: route,
            type: "GET",
            dataType: "json"
        });
});