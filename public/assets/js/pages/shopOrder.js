
var Cdek = {

    deleteOrder: function(route) {

        Spiner.show();

        $.ajax({
            url: route,
            type: "GET",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (data) {

                $("#cdek_order_btn_create").removeClass('d-none');
                $("#cdek_order_btn_created").addClass('d-none');

                Spiner.hide();

            },
            error: function () {
                Spiner.hide();
                alert("Ошибка. Попробуйте немного позже.")
            },
        });
    },

    createOrder: function(shop_order_id, step = 0) {
        Spiner.show();

        let form = $("#formEdit").serializeArray();

        let Data = '?', i = 0;
        form.forEach(element => {
            if (i > 1) {
                Data += element["name"] + "=" + element["value"] + "&";
            }
            
            i++;
        });

        $.ajax({
            url: create_cdek_order_route,
            type: "POST",
            data: Data + "shop_order_id=" + shop_order_id + "&step=" + step,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (data) {

                if (typeof data.error != 'undefined') {

                    let errors = '';

                    data.error.forEach(element => {
                        errors += "<div>" + element + "</div>"
                    });

                    errors = '<div class="alert alert-danger border-0 my-2" role="alert">' + errors + '</div>';

                    $("#cdek-errors").html(errors);

                    Spiner.hide();

                } else {

                    if (parseInt(data.id) > 0) {

                        $("#cdek_order_btn_create").addClass('d-none');

                        let insert ="<a href='"+ data.printUrl +"' target='_blank' class='btn btn-success mx-1'>Распечатать квитанции</a>";

                            insert +="<button type='button' id='delete_cdek_order_btn' onclick='Cdek.deleteOrder(\""+ data.deleteOrder +"\")' class='btn btn-outline-danger active'>Удалить накладную</button>";

                        $("#cdek_order_btn_created").removeClass('d-none').html(insert);

                        Spiner.hide();
                    } else {

                        setTimeout(function() {
                            step++;
                            if (step < 6) {
                                console.log("пытаемся получить ссылку, попытка:" + step)
                                Cdek.createOrder(shop_order_id, step);
                            } else {
                                Spiner.hide();
                                alert("Error. Невозможно получить ссылку.");
                            }

                        }, 3000);
                    }
                }
            },

            error: function () {
                Spiner.hide();
                alert("Ошибка. Попробуйте немного позже.")
            },
        });
    }
}

var Boxberry = {
    createOrder: function(shop_order_id) {
        Spiner.show();

        let form = $("#formEdit").serializeArray();

        let Data = '?', i = 0;
        form.forEach(element => {
            if (i > 1) {
                Data += element["name"] + "=" + element["value"] + "&";
            }
            
            i++;
        });

        $.ajax({
            url: create_boxberry_order_route,
            type: "POST",
            data: Data + "shop_order_id=" + shop_order_id,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (data) {

                if (typeof data.err != 'undefined') {
                    alert(data.err);
                } else {
                    $("#boxberry_btns").html("<a href='"+ data.label +"' target='_blank' class='btn btn-outline-boxberry active'>Распечатать квитанцию</a>");
                }
  
                Spiner.hide();
            },

            error: function () {
                Spiner.hide();
                alert("Ошибка. Попробуйте немного позже.")
            },
        });
    }
}

var PochtaRossii = {

    createOrder: function(shop_order_id) {

        Spiner.show();

        let form = $("#formEdit").serializeArray();

        let Data = '?', i = 0;
        form.forEach(element => {
            if (i > 1) {
                Data += element["name"] + "=" + element["value"] + "&";
            }
            
            i++;
        });

        $.ajax({
            url: create_pr_order_route,
            type: "POST",
            data: Data + "shop_order_id=" + shop_order_id,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (data) {

                if (typeof data.error != 'undefined') {
                    alert(data.error);
                } else {
                    $("#pr_btns").html("<div class='btn-outline-pochta fw-bold'>Трек: " + data + "</div>");
                }
  
                Spiner.hide();
            },

            error: function () {
                Spiner.hide();
                alert("Ошибка. Попробуйте немного позже.")
            },
        });

    }
}


$(function() {
    $('[name="phone"]').mask("+7 (999) 999-9999", {autoclear: false});

    $('[name="client"]').autocomplete({
        source: routeGetClients,
        minLength: 1,
        select: function( event, ui ) {
            $("[name='client_id']").val(ui.item.data);
        }, 
    });

    $('[name="order_id"]').autocomplete({
        serviceUrl: routeGetOrders,
        minChars: 1,
        params: {"current_order": currentOrder},
        onSelect: function (suggestion) {
            if (suggestion.data.length) {
                document.location.href=suggestion.data
            }
            
        }
    });
});

var delay = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();