$(function () {
    $(".uk-active").parents("li").addClass("uk-open");
    $(".uk-active").parents("ul").removeAttr("hidden");

    if ($(".group-text-top").length) {
        $(".group-text").html($(".group-text-top").html());
    }
});

$(window).on("scroll", function(){
    if ($(".pagination-auto").length) {
        var pagination = $(".pagination-auto"),
            topPagination = pagination.offset().top,
            h = window.innerHeight
                || document.documentElement.clientHeight
                || document.body.clientHeight;

        if (window.pageYOffset > topPagination - 2*h && !pagination.hasClass("disabled")) {

            pagination.addClass("disabled");
            
            $.ajax({
                url: "/shop/ajax/group/" + pagination.data("group") + "?page=" + $(".js-pagination-more").data("n"),
                method: 'GET',
                data: $("#filter").serialize(),
                dataType: "html",
                success: function(data) {
                        
                    pagination.response = $("<div>" + data + "</div>");

                    if (pagination.response.find(".tm-tovar").length) {
                        $(".items").append(pagination.items = pagination.response.find(".tm-tovar"))
                    }

                    if (pagination.response.find("a.js-pagination-more").length) {
                        $(".pagination").replaceWith(pagination.response.find(".pagination"));
                        pagination.removeClass("disabled");
                    }
                    else $(".pagination").remove();
                }
            });
        }
    }
});