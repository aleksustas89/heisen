new Selectr('.select');

var Cdek = {
    createOrder: function(shop_order_id, step = 0) {
        Spiner.show();
        $.ajax({
            url: create_order_route,
            type: "POST",
            data: {
                "shop_order_id" : shop_order_id,
                "step" : step,
            },
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
                        $("<a href='"+ data.printUrl +"' target='_blank' class='btn btn-success mx-1'>Распечатать квитанции (доступны в течении часа)</a>").insertAfter("#create_cdek_order_btn");
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

$(function(){
    $('[name="phone"]').mask("+7 (999) 999-9999", {autoclear: false});
});

$('[name="delivery_7_city"]').autocomplete({
    serviceUrl: '/get-cdek-cities',
    minChars: 0,
    onSelect: function (suggestion) {
        $("[name='delivery_7_office']").val("").removeAttr("disabled");
        $("[name='delivery_7_courier']").removeAttr("disabled");
        $("[name='delivery_7_city_id']").val(suggestion.data);

        $('[name="delivery_7_office"]').autocomplete({
            serviceUrl: '/get-cdek-offices',
            params: {"city_id": suggestion.data},
            minChars: 0,
            onSelect: function (suggestion) {
                $("[name='delivery_7_office_id']").val(suggestion.data);
            }
        });
    }
});

if ($('[name="delivery_7_city_id"]').val().length) {
    $('[name="delivery_7_office"]').autocomplete({
        serviceUrl: '/get-cdek-offices',
        params: {"city_id": $('[name="delivery_7_city_id"]').val()},
        minChars: 0,
        onSelect: function (suggestion) {
            $("[name='delivery_7_office_id']").val(suggestion.data);
        }
    });
}

$("[name='delivery_7_city']").keyup(function(){
    let value = $(this).val();
    if (!value.length) {
        delay(function() {
            $("[name='delivery_7_office'], [name='delivery_7_office_id']").val("");
        }, 1000);
    }
});

var delay = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();