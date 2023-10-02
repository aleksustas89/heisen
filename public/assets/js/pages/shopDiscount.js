$(function(){

    $("[name='total_list_id']").change(function() {
        let totalListId = $(this);
        $.ajax({
            url: "/admin/list/values",
            data: {"total_list_id": totalListId.val()},
            type: "GET",
            dataType: "json",
            success: function (data) {
                if (data.length) {
                    $("[name='total_list_value']").html(data).parent().removeClass("d-none");
                } else {
                    $("[name='total_list_value']").html(data).parent().addClass("d-none");
                }
            }
        });
    });
});