var Search = {
    init: function(type = 0, offset = 0, indexed = 0) {

        let btn = $("[type='submit']");

        btn
            .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="mx-2">Идет индексация...</span>')
            .attr("disabled", "disabled");

        $.ajax({
            url: "/admin/search/indexing?type=" + type + "&offset=" + offset + "&indexed=" + indexed,
            type: "GET",
            dataType: "json",
            success: function (data) {

                console.log(data)

                let alert = '';

                if (data.finished == false) {
                    Search.init(data.type, data.offset, data.indexed);
                    alert = '<div class="alert alert-outline-success mb-3" role="alert"><strong>Проиндексированно ' + data.indexed + ' элементов  🎉</strong> Не обновляйте страницу!</div>';
                } else {
                    alert = '<div class="alert alert-outline-success mb-3" role="alert"><strong>Проиндексированно ' + data.indexed + ' элементов 🎉</strong> Индексирование успешно завершенно. Страница будет обновлена.</div>';
                    btn
                        .removeAttr("disabled")
                        .text(btn.data("title"));

                    setTimeout(function(){
                        location.reload();
                    }, 2000);
                }

                $("#result").html(alert);
            }
        });

    },
}