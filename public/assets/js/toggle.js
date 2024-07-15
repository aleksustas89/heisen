var toggle = {
    init: function(elem) {
        $.ajax({
            url: toggle_route + "?data=" + elem.attr("id"),
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (data) {

                if (typeof data.class != "undefined") {
                    elem.attr("class", data.class);
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