var Cart = {
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

                if ($("#modal-full").length) {
                    $("#modal-full").remove();
                }

                $("body").append(data);

                Spiner.hide();
                UIkit.modal("#modal-full").show();

                Cart.updateCart();
               
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
                        $(".little-cart").html(data);
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
               $(".little-cart").html(data);
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