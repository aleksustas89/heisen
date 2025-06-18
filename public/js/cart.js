var Cart = {

    plus: function(route, id) {

        Spiner.show();

        $.ajax({
            url: route,
            type: "POST",
            data: {
                "id": id, 
                "count": 1
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "html",
            success: function (data) {

                Cart.updateCarts(data);

                Spiner.hide();
            },
        });

    },

    minus: function(route, id) {
        Spiner.show();

        $.ajax({
            url: route,
            type: "POST",
            data: {
                "id": id, 
                "count": -1
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "html",
            success: function (data) {

                Cart.updateCarts(data);
            
                Spiner.hide();
            },
        });
    },

    add: function(route, shop_item_id, count) {
        
        Spiner.show();

        let description = $("#personalization_desc").val(), 
            logo_select = $("#personalization_logo").val(); 

            console.log(shop_item_id)
            console.log(description)
            console.log(logo_select)

        $.ajax({
            url: route,
            type: "POST",
            data: {
                "shop_item_id": shop_item_id, 
                "count": count,
                "description": description,
                "logo_select": logo_select
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "html",
            success: function (data) {

                $("#personalization_logo").val(1);
                $("#personalization_desc").val("");

                Cart.updateCart();

                if (data.length) {
                    let cart = $("#cart").length ? $("#cart") : $(".little-cart");
                    cart.html(data);
                    Spiner.hide();
                } else {
                    location.reload();
                }

                if ($("#modal-full").length) {
                    $("#modal-full").remove();
                }

                $("body").append(data);

                UIkit.modal("#modal-full").show();
                

                Spiner.hide();

            },
        });

    }, 

    updateCarts(data) {
        if ($("#cart").length) {
            $("#cart").find(".uk-card-body").html($(data).find(".cart-items").html());
        }

        if ($(".little-cart").length) {
            $(".little-cart").html(data);
        }
    },

    delete: function(id, littleCart = 0) {

        UIkit.modal.confirm('Вы действительно хотите удалить товар из корзины? :(').then(function() {
       
            Spiner.show();

            $.ajax({
                url: "/delete-from-cart",
                type: "POST",
                data: {
                    "id": id,
                    "littleCart" : littleCart
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "html",
                success: function (data) {
                    if (data.length) {

                        Cart.updateCarts(data);
                        
                        Spiner.hide();
                    } else {
                        location.reload();
                    }
                },
            });

        });
    },

    updateCart: function(littleCart = 1) {
        $.ajax({
            url: "/get-cart",
            type: "POST",
            data: {
                "littleCart" : littleCart
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "html",
            success: function (data) {

                Cart.updateCarts(data);
            },
        });
    },

    cancelChosenCity: function() {
        $("#chosenCity").text($("#chosenCity").data("default"));
        $("#city-autocomplete, #city_id, #city_custom, #city").val('');
    },

    chooseDelivery: function(item) {
        $("[name='"+ item.data("hidden") +"']").val(item.data("id"));
        setTimeout(function() {
            $(item).parents("ul").siblings(".uk-switcher").find("select").trigger('refresh');
        }, 100);
        
    },

    chooseBoxberry: function() {
        UIkit.modal("#boxberry-modal").show();
    },
}

var delay = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

function pochtaRfCallback(data) {

    let aResult = new Array();

    if (typeof data.indexTo != 'undefined' && data.indexTo != null) {
        aResult[aResult.length] = data.indexTo;
        $("[name='delivery_1_index']").val(data.indexTo);
    }
    if (typeof data.regionTo != 'undefined' && data.regionTo != null) {
        aResult[aResult.length] = data.regionTo;
        $("[name='delivery_1_region']").val(data.regionTo);
    }
    if (typeof data.areaTo != 'undefined' && data.areaTo != null) {
        aResult[aResult.length] = data.areaTo;
        $("[name='delivery_1_area']").val(data.areaTo);
    }
    if (typeof data.cityTo != 'undefined' && data.cityTo != null && data.cityTo != data.regionTo) {
        aResult[aResult.length] = data.cityTo;
        $("[name='delivery_1_city']").val(data.cityTo);
    }  
    if (typeof data.addressTo != 'undefined' && data.addressTo != null) {
        aResult[aResult.length] = data.addressTo;
        $("[name='delivery_1_address']").val(data.addressTo);
    }  

    let sResult = aResult.join(", ");

    let Result = '<p>Адрес: ' + sResult + '</p>';

    $("#prResult").html(Result);

    UIkit.modal("#pochta-rf-window").hide();
} 