$(function() {
        $("body").on("click", ".cdek-tariff", function(){
            let $this = $(this);

            let html = '';

            $("[name='delivery_7_tariff_name']").val($this.attr("data-name"));
            $("[name='delivery_7_tariff_code']").val($this.attr("data-tariff"));
            $("[name='delivery_7_price']").val($this.attr("data-price"));
            $("[name='delivery_7_office']").val($this.attr("data-office-name"));
            $("[name='delivery_7_office_id']").val($this.attr("data-office-id"));
            $("[name='delivery_7_city']").val($this.attr("data-city"));

            if ($this.attr("data-city").length) {
                html += '<p>Город: '+ $this.attr("data-city") +'</p>';
            }

            html += '<p>Отделение: '+ $this.attr("data-office-name") +'</p>';
            html += '<p>Ориентировочная цена: '+ $this.attr("data-price") +' ₽</p>';
            html += '<p>Тариф: '+ $this.attr("data-name") +'</p>';

            $("#cdekResult").html(html);

            $(".cdek-tariff").each(function() {
                if ($(this).attr("id") == $this.attr("id")) {
                    $(this).addClass("active");

                    setTimeout(() => {
                        UIkit.modal("#cdek-window").hide();
                    }, 1000);
                } else {
                    $(this).removeClass("active");
                }
            });
        });
    });

    var aPoints = [
        @foreach ($CdekOffices as $CdekOffice)
            [{{ $CdekOffice->latitude }}, {{ $CdekOffice->longitude }}],
        @endforeach
    ];

    var aPointsData = [
        @foreach ($CdekOffices as $CdekOffice)
            ['{{ $CdekOffice->code }}', "{{ $CdekOffice->name }}", "{{ \App\Services\Helpers\Str::clean($CdekOffice->address_comment) }}", "{{ $CdekOffice->work_time }}"],
        @endforeach
    ];

    var Cdek = {

        window: function() {

            UIkit.modal("#cdek-window").show();
        },

        showTariff: function(code) {

            $("#cdek-tariffs").html("");

            $.ajax({
                url: "/cdek/tariffs",
                type: "GET",
                data: {"code": code},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "html",
                success: function (data) {

                    $("#cdek-tariffs").html(data);        
                },
            });

        }
    }



    function init () {
        myMap = new ymaps.Map('map', {
            center: [55.751574, 37.573856],
            zoom: 9,
            controls: ['zoomControl', 'searchControl']
        }, {
            searchControlProvider: 'yandex#map'
        }),

        /**
         * Создадим кластеризатор, вызвав функцию-конструктор.
         * Список всех опций доступен в документации.
         * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/Clusterer.xml#constructor-summary
         */
            clusterer = new ymaps.Clusterer({
            /**
             * Через кластеризатор можно указать только стили кластеров,
             * стили для меток нужно назначать каждой метке отдельно.
             * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/option.presetStorage.xml
             */
            //preset: 'islands#greenIcon',
            //clusterIconLayout: 'default#pieChart',

            clusterIconColor: "#1ab248",

            clusterIconPieChartRadius: 15,

            clusterIconPieChartCoreRadius: 10,
            // Ширина линий-разделителей секторов и внешней обводки диаграммы.
            clusterIconPieChartStrokeWidth: 1,
            /**
             * Ставим true, если хотим кластеризовать только точки с одинаковыми координатами.
             */
            groupByCoordinates: false,
            /**
             * Опции кластеров указываем в кластеризаторе с префиксом "cluster".
             * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/ClusterPlacemark.xml
             */
         
        }),
        /**
         * Функция возвращает объект, содержащий данные метки.
         * Поле данных clusterCaption будет отображено в списке геообъектов в балуне кластера.
         * Поле balloonContentBody - источник данных для контента балуна.
         * Оба поля поддерживают HTML-разметку.
         * Список полей данных, которые используют стандартные макеты содержимого иконки метки
         * и балуна геообъектов, можно посмотреть в документации.
         * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/GeoObject.xml
         */
        getPointData = function (index) {
            return {
                balloonContentHeader: '<b>' + aPointsData[index][0] + ", " +  aPointsData[index][1] + '</b>',
                balloonContentBody: '<p>' + aPointsData[index][2] + '</p><p>Время работы: ' + aPointsData[index][3] + '</p><p><a href="javascript:void(0)" onclick="Cdek.showTariff(\''+ aPointsData[index][0] +'\')">Выбрать тариф</a></p><div id="ya-tariff"></div>',
            };
        },
        /**
         * Функция возвращает объект, содержащий опции метки.
         * Все опции, которые поддерживают геообъекты, можно посмотреть в документации.
         * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/GeoObject.xml
         */
            getPointOptions = function () {
            return {
                //preset: 'islands#pinkIcon',
                preset: 'islands#icon',
                iconColor: '#1ab248'
            };
        },
        points = aPoints,
        geoObjects = [];

    /**
     * Данные передаются вторым параметром в конструктор метки, опции - третьим.
     * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/Placemark.xml#constructor-summary
     */
    for(var i = 0, len = points.length; i < len; i++) {
        geoObjects[i] = new ymaps.Placemark(points[i], getPointData(i), getPointOptions());
    }

    /**
     * Можно менять опции кластеризатора после создания.
     */
    clusterer.options.set({
        gridSize: 60,
        //clusterDisableClickZoom: true
    });

    /**
     * В кластеризатор можно добавить javascript-массив меток (не геоколлекцию) или одну метку.
     * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/Clusterer.xml#add
     */
    clusterer.add(geoObjects);
    myMap.geoObjects.add(clusterer);

    /**
     * Спозиционируем карту так, чтобы на ней были видны все объекты.
     */

    // myMap.setBounds(clusterer.getBounds(), {
    //     checkZoomRange: false
    // });
    }

    ymaps.ready(init);

    var myMap;