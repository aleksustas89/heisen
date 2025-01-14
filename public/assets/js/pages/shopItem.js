
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

    delete: function (elem, route = '') {

        let id = parseInt(elem.parents(".admin-item-property").attr("data-id"));

        if (id > 0) {
            if (confirm('Вы действительно хотите удалить значение?')) {

                $.ajax({
                    url: route,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        if (data == true) {
                            elem.parents(".admin-item-property").remove();
                        }
                    }
                });
            } 
        } else {
            elem.parents(".admin-item-property").remove();
        }

    },

}

var Canonical = {
    delete: function() {
        $(".canonical_value").remove();
        $('[name="canonical_name"]').val('').show();
        $('[name="canonical"]').val('');
    }
}

$(function() {

    if ($('[name="canonical_name"]').length) {
        $('[name="canonical_name"]').autocomplete({
            source: routeSearchCanonical,
            minLength: 2,
            select: function( event, ui ) {
                $('[name="canonical"]').val(ui.item.data);
                $('[name="canonical_name"]').after('<span class="canonical_value badge rounded-pill bg-primary">'+ ui.item.value +'<a href="javascript:void(0)" onclick="Canonical.delete()" class="mdi mdi-close"></a></span>');
                $('[name="canonical_name"]').hide();
            }, 
        });
    }

    if ($('[name="shortcut_group_id"]').length) {
        $('[name="shortcut_group_id"]').autocomplete({
            source: routeGroupShortcut,
            minLength: 2,
            select: function( event, ui ) {
    
                var getRandomBadgeClass = Math.floor( (Math.random() * BadgeClasses.length) + 0);
    
                if (!$("#shortcut_group_" + ui.item.data).length) {
                    $(".shortcut_groups")
                        .append('<span id="shortcut_group_' + ui.item.data + '" class="badge badge-soft-'+ BadgeClasses[getRandomBadgeClass] +'">'+ ui.item.label +'<a href="javascript:void(0)" onclick="Shortcut.delete($(this))" class="mdi mdi-close"></a><input type="hidden" name="shortcut_groups[]" value="'+ ui.item.data +'"></span>');
                }
            }, 
    
            close: function() {
                $(this).val("");
            }
        });
    }
});