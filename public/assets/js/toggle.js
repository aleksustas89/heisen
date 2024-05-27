var toggle = {
    init: function(elem) {
        $.ajax({
            url: toggle_route + "?data=" + elem.attr("id"),
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                if (parseInt(data.value) == 0) {
                    elem.addClass("ico-inactive");
                } else {
                    elem.removeClass("ico-inactive");
                }

                if (typeof data.trClass != "undefined") {
                    elem.parents("tr").attr("class", data.trClass);
                }
            }, 
            error: function () {
                location.reload();
            }
        });
    }
}