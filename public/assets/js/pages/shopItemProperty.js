$(function(){

    $("#shop_item_property_type").on("change", function(){
        switch ($(this).val()) {
            case '4':
                $(".shop-property-lists").removeClass("hidden");
                $("#row_multiple").removeClass("hidden");
            break;
            case '3':
                $("#row_multiple").addClass("hidden");
            break;
            default: 
                $(".shop-property-lists").addClass("hidden");
                $("#row_multiple").removeClass("hidden");
        }
    });

});