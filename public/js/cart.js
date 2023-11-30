var Cart = {

    plus: function(route, id) {

        Spiner.show();

        $.ajax({
            url: route,
            type: "POST",
            data: {
                "id": id, 
                "quantity": 1,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "html",
            success: function (data) {
                $("#cart").html(data);
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
                "quantity": -1,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "html",
            success: function (data) {
                $("#cart").html(data);
                Spiner.hide();
            },
        });
    },

    add: function(route, id, quantity) {
       
        Spiner.show();

        $.ajax({
            url: route,
            type: "POST",
            data: {
                "id": id, 
                "quantity": quantity
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "html",
            success: function (data) {

                Cart.updateCart();

                if (data.length) {
                    $("#cart").html(data);
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
                        $("#cart").html(data);
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
                let cart = $("#cart").length ? $("#cart") : $(".little-cart");
                cart.html(data);
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
}