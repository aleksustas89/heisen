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
                $(".favorites-count").html(data.count);
                Spiner.hide();
            },
        });
    }
}

$(function(){

    $(".search-autocomplete").autocomplete({
        serviceUrl: '/search/autocomplete',
        onSelect: function (suggestion) {
            $(this).parents("form").submit();
        }
    });

    $(function(){
        $('[name="phone"]').mask("+7 (999) 999-9999", {autoclear: false});

        $("#request-call-form").on("submit", function() {

            $.ajax({
                url: "/request-call",
                type: "POST",
                data: $("#request-call-form").serialize(),
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    $("#request-call-form").replaceWith(data);
                },
            });

            return false;
        });
    });

    
});