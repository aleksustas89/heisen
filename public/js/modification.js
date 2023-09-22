var Modification = {
    choose: function(item) {

        $(".buy-btn")
            .removeAttr("onclick")
            .attr("disabled", "disabled")
            .attr("uk-tooltip", $(".buy-btn")
            .attr("data-uk-tooltip"));

        Spiner.show();
        item.parents("ul").siblings("[type='hidden']").val(item.data("id"));

        let error = 0;
        $("#add_to_cart").find("[type='hidden']").each(function(){
            if (!$(this).val().length) {
                error++;
            }
        });

        if (error == 0) {
            $.ajax({
                url: "/get-modification",
                type: "POST",
                data: $("#add_to_cart").serialize(),
                dataType: "json",
                success: function (data) {
                    if (typeof data.item != 'undefined') {
                        item.parent("li").addClass("active");
                        item.parent("li").siblings("li").removeClass("active");
                        $(".buy-btn").removeAttr("disabled").removeAttr("uk-tooltip");
                        $("#cart_add").attr("onclick", "Cart.add('"+ $("#cart_add").data("route") +"', "+ data.item.id +", " + $("[name='quantity']").val()  + ")");
                        $("#item-name").text(data.item.name);
                        $("#item-price").text(data.item.price);
                        $("#item-old-price").text(data.item.oldPrice);

                        if ($("#uk-slide-" + data.item.image.shop_item_image_id).length) {
                            var slideshow = UIkit.slideshow("#uk-slideshow-items");
                            slideshow.show($("#uk-slide-" + data.item.image.shop_item_image_id).index());
                        }
                        Spiner.hide();
                    }
                },
            });
        }
    }
};