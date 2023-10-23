var SwitchAll = {
    init: function(elem) {

        elem.parents("tr").siblings("tr").each(function(){
            if (elem.prop("checked")) {
                $(this).find("input").prop("checked", true);
            } else {
                $(this).find("input").prop("checked", false);
            }
        });
    }
}

var DiscountFilter = {
    init: function() {
        $.ajax({
            url: "/admin/shop/discount/filter",
            data: $("#formEdit").serialize(),
            type: "GET",
            dataType: "html",
            success: function (data) {
                $("#filter_result").html(data);
            }
        });
    }
}

$(function(){

    $('[name="shop_item_name"]').keyup(function() {
        delay(function(){
            DiscountFilter.init();
        }, 1000 );
    });

    $("[name='shop_group_id'], [name='total_list_value']").change(function() {
        DiscountFilter.init();
    });

    $("[name='total_list_id']").change(function() {
        if (parseInt($(this).val()) == 0) {
            DiscountFilter.init();
        }
    });

    $("[name='total_list_id']").change(function() {
        let totalListId = $(this);
        $.ajax({
            url: "/admin/list/values",
            data: {"total_list_id": totalListId.val()},
            type: "GET",
            dataType: "json",
            success: function (data) {
                if (data.length) {
                    $("[name='total_list_value']").html('<option value="0">...</option>' + data).parent().removeClass("d-none");
                } else {
                    $("[name='total_list_value']").html(data).parent().addClass("d-none");
                }
            }
        });
    });


    
});