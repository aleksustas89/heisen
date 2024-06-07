var editable = {
    save: function(elem) {
        $.ajax({
            url: editable_route + "?data=" + elem.attr("id") + "&value=" + elem.text(),
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                elem.parents("form").removeAttr("data-form-has-been-changed-value")
            },
            error: function () {
                location.reload();
            }
        });
    }
}

$(document).bind('dblclick touchend', function(e) {

    $this = $(e.target);

    if ($this.hasClass("editable")) {

        $editor = $('<input style="width:80%">').prop('type', 'text').val($this.text());

        $this.css("display", "none");

        let value = $this.hasAttr("data-value") ? $this.attr("data-value") : $this.text();

        $editor.on('blur', function() {
            var $editor = $(this);

            $this.text($editor.val()).css('display', '');
            $editor.remove();
            editable.save($this);
            if ($this.hasAttr("data-value")) {
                $this.attr("data-value", $editor.val());
            }
        }).on('keydown', function(e) {
            if (e.keyCode == 13) { // Enter
                e.preventDefault();
                this.blur();
            }
            if (e.keyCode == 27) { // ESC
                e.preventDefault();
                var $editor = jQuery(this);
                $this.css('display', '');
                $editor.remove();
            }
        })
        .insertAfter($this).focus().val(value);
    }
});