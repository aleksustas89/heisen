$(function() {

    $("[name='search_associated_by_name']").keyup(function() {
        let $this = $(this);
        delay(() => {

            $("#search_associated_by_name").siblings(".spinner-row").removeClass("d-none");

            $.ajax({
                url: routeSearchShopItemFromAssosiated,
                data: {"term": $this.val()},
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "html",
                success: function (data) {
                    $("#search_associated_by_name").html(data);
                    $("#search_associated_by_name").siblings(".spinner-row").addClass("d-none");
                },
    
                error: function () {
        
                },
            });
        }, 1000);
    });
});

var Associated = {

    showTab: function(shop_group_id, shop_item_id) {

        if ($("#associated-group-tab-" + shop_group_id).find('.sub').find(".spinner-small").length || shop_group_id == 0) {
            $.ajax({
                url: routeAddAssociated,
                data: {"shop_group_id": shop_group_id, "shop_item_id": shop_item_id},
                type: "get",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "html",
                success: function (data) {

                    if (shop_group_id > 0) {
                        $("#associated-group-tab-" + shop_group_id).find('.sub').html(data);      
                    } else {
                        $("#modal-associated-result").html(data);
                    }     
                },
    
                error: function () {
        
                },
            });
        }
    }, 

    saveChanges: function() {
        var form = $("#modal-associated").find("form");
        $.ajax({
            url: routeSaveAssociated,
            data: form.serialize(),
            type: "post",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "html",
            success: function (data) {

                $("#associated-content").html(data);

                form.find(".modal-body").html(form.find(".message").first().show());

                setTimeout(() => {
                    form.find("button.close").click()
                }, 2000);
            },

            error: function () {
    
            },
        });
    }, 

    delete: function(route) {
        $.ajax({
            url: route,
            type: "post",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "html",
            success: function (data) {

                $("#associated-content").html(data);
            },

            error: function () {
    
            },
        });
    },
}