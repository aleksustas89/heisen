var Modification = {
    choose: function(item) {

        $(".buy-btn")
            .removeAttr("onclick")
            .attr("uk-tooltip", $(".buy-btn")
            .attr("data-uk-tooltip"));

        
        item.parents("ul").siblings("[type='hidden']").val(item.data("id"));
        item.parent("li").addClass("active");
        item.parent("li").siblings("li").removeClass("active");
        $(".buy-btn").removeAttr("disabled").removeAttr("uk-tooltip");

        let error = 0;
        $("#add_to_cart").find("[type='hidden']").each(function(){
            if (!$(this).val().length) {
                error++;
            }
        });

        if (error == 0) {

            Spiner.show();

            $.ajax({
                url: "/get-modification",
                type: "POST",
                data: $("#add_to_cart").serialize(),
                dataType: "html",
                success: function (data) {

                    $("#item").replaceWith(data);

                    Spiner.hide();

                    // if (typeof data.item != 'undefined') {

                    //     $("#cart_add").attr("onclick", "Cart.add('"+ $("#cart_add").data("route") +"', "+ data.item.id +", " + $("[name='quantity']").val()  + ")");
                    //     /*вставим и в быстрый заказ*/
                    //     $("#shop-quich-order").find("[name='shop_item_id']").val(data.item.id);
                    //     $("#item-name").text(data.item.name);
                    //     $("#item-price").text(data.item.price);
                    //     $("#item-old-price").text(data.item.oldPrice);

                    //     history.pushState(null, null, data.item.url);

                    //     Spiner.hide();
                    // }
                },
            });
        }
    }
};