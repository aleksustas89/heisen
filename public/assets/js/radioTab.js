var radioTab = {
    click: function(elem) {
        let id = elem.attr("id");
        elem.siblings(".tab-content").find(".tab-pane").each(function () {
            if ($(this).attr("id") == "tab-" + id) {
                $(this).addClass("active");
            } else {
                $(this).removeClass("active");
            }
        });
    }
}