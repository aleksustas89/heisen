var Modification = {
    choose: function(item) {

        $(".buy-btn")
            .removeAttr("onclick")
            .attr("disabled", "disabled")
            .attr("uk-tooltip", $(".buy-btn")
            .attr("data-uk-tooltip"));

        
        item.parents("ul").siblings("[type='hidden']").val(item.data("id"));

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
                dataType: "json",
                success: function (data) {
                                 
                    if (typeof data.item != 'undefined') {

                        window.location = data.item.url;
                    }
                },
            });
        }
    }
};