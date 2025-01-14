var Shortcut = {

    delete: function(item, route = '') {

        if (route.length) {
            $.ajax({
                url: route,
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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
    },

    addFromGroup: function() {

        $.ajax({
            url: routeAddShortcutFromGroup,
            data: $(".admin-table").serialize(),
            type: "GET",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "html",
            success: function (data) {
                if($(".modal").length) {
                    $(".modal").remove();
                }
                $("body").append(data);
                var modal = new bootstrap.Modal(document.querySelector('#add-to-groups'));
                modal.show();

                $('[name="shortcut_group_id"]').autocomplete({
                    source: routeGetShortcutGroup,
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
    }
}