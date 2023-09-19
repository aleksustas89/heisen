var Spiner = {

    show: function() {
        $("body").append('<div class="spinner"><span uk-spinner="ratio: 4.5"></span></div>');
    },

    hide: function() {
        $(".spinner").remove();
    }
}

var Favorite = {
    add: function(elem, shop_item_id, route) {

        if (elem.hasClass("active")) {
            elem.removeClass("active");
        } else {
            elem.addClass("active");
        }
        
        Spiner.show();

        $.ajax({
            url: route,
            type: "POST",
            data: {
                "shop_item_id": shop_item_id, 
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (data) {
                $("#favorites-count").html(data.count);
                Spiner.hide();
            },
        });
    }
}