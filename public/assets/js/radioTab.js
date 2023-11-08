var radioTab = {
    click: function(elem) {
        let id = elem.attr("id");
        if (typeof elem.data("hidden") != 'undefined') {
            $("[name='"+ elem.data("hidden") +"']").val(elem.data("id"));
        }
        elem.siblings(".tab-content").children(".tab-pane").each(function () {
            if ($(this).attr("id") == "tab-" + id) {
                $(this).addClass("active");
            } else {
                $(this).removeClass("active");
            }
        });
    }
}