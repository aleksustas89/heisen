
var adminProperty = {


    copy: function (elem) {

        let copyElem = elem.parents(".admin-item-property").clone();

        copyElem.find("[type='text']").val("").attr("name", copyElem.find("[type='text']").attr("data-name"));
        copyElem.find("select").val("").attr("name", copyElem.find("select").attr("data-name"));
        copyElem.removeAttr("id")
        copyElem.removeAttr("data-id")
        copyElem.find(".delete-property").attr("onclick", 'adminProperty.delete($(this))')
        
  
        elem.parents(".list-group-item").find(".admin-item-property:last").after(copyElem);

    },

    delete: function (elem) {

        let id = parseInt(elem.parents(".admin-item-property").attr("data-id"));

        if (id > 0) {
            if (confirm('Вы действительно хотите удалить значение?')) {
                let property = parseInt(elem.parents(".admin-item-property").attr("data-property"));
                adminProperty.remove(property, id);
            } 
        } else {
            elem.parents(".admin-item-property").remove();
        }

    },

    remove: function (property, id) {

        $.ajax({
            url: "/admin/deleteShopItemPropertyValue/" + property + "/" + id + "/",
            type: "GET",
            dataType: "json",
            success: function (data) {
                if (data == true) {
                    $("#admin-item-property-" + property + "-" + id).remove();
                }
            }
        });

    }

}