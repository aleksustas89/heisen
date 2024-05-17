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

var SwitchAllWithAccordion = {
    init: function(elem) {

        $("#filter_result").find(".accordion-item").each(function(){
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
        Spiner.show();
        let shop_item_name = $("[name='shop_item_name']").val(),
            shop_group_id = $("[name='shop_group_id']").val(),
            total_list_id = $("[name='total_list_id']").val(),
            total_list_value = $("[name='total_list_value']").val();

        if (shop_item_name.length > 1 || shop_group_id > 0 || (total_list_id > 0 && total_list_value > 0)) {
            $.ajax({
                url: shopDiscountFilter,
                data: {"shop_item_name": shop_item_name, "shop_group_id": shop_group_id, "total_list_id": total_list_id, "total_list_value": total_list_value},
                type: "GET",
                dataType: "html",
                success: function (data) {
                    $("#filter_result").html(data);
                    Spiner.hide();
                }
            });
        } else {
            $("#filter_result").html("");
            Spiner.hide();
        }
    }
}

$(function(){

    $('[name="shop_item_name"]').keyup(function() {
        delay(function() {
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
        let val = $(this).find('option:selected').data("list");
        $.ajax({
            url: shopDiscountPropertyValues,
            data: {"total_list_id": val},
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