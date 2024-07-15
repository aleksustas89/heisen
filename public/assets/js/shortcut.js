var Shortcut = {

    delete: function(item, route = '') {

        if (route.length) {
            $.ajax({
                url: route,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if (data.responce == true) {
                        item.parent().remove();
                    }
                }
            });
        } else {
            item.parent().remove();
        }
    }
}