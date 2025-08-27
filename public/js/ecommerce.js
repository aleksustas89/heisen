window.dataLayer = window.dataLayer || [];

var Ecommerce = {
    click: function(data) {
        if (data && typeof data === 'object') {
            window.dataLayer = window.dataLayer || [];
            dataLayer.push({
                "ecommerce": {
                    "currencyCode": "RUB",
                    "click": {
                        "products": [data]
                    }
                }
            });
            //console.log('Ecommerce click:', data);
        }
    },

    detail: function(data) {
        // Проверяем, существует ли sessionStorage и не было ли события для этого товара
        if (typeof sessionStorage !== 'undefined') {
            var viewedItems = sessionStorage.getItem('ecommerce_viewed_items');
            viewedItems = viewedItems ? JSON.parse(viewedItems) : [];

            // Если товар уже просмотрен в этой сессии, выходим
            if (viewedItems.includes(data.id)) {
                //console.log('Товар ' + data.id + ' уже просмотрен, событие detail не отправлено');
                return;
            }

            // Добавляем ID товара в список просмотренных
            viewedItems.push(data.id);
            sessionStorage.setItem('ecommerce_viewed_items', JSON.stringify(viewedItems));
        }

        // Отправляем событие в dataLayer
        window.dataLayer = window.dataLayer || [];
        dataLayer.push({
            "ecommerce": {
                "currencyCode": "RUB",
                "detail": {
                    "products": [data]
                }
            }
        });
        console.log('Ecommerce detail:', data); // Для отладки
    },

    // Событие добавления в корзину
    add: function(data) {
        window.dataLayer = window.dataLayer || [];
        dataLayer.push({
            "ecommerce": {
                "currencyCode": "RUB",
                "add": {
                    "products": [data]
                }
            }
        });
        //console.log('Ecommerce add:', data); // Для отладки
    },

    // Событие удаления из корзины
    remove: function(data) {
        window.dataLayer = window.dataLayer || [];
        dataLayer.push({
            "ecommerce": {
                "currencyCode": "RUB",
                "remove": {
                    "products": [data]
                }
            }
        });
        //console.log('Ecommerce remove:', data); // Для отладки
    },

    purchase: function(data) {
        if (typeof sessionStorage !== 'undefined') {
            var purchasedOrders = sessionStorage.getItem('ecommerce_purchased_orders');
            purchasedOrders = purchasedOrders ? JSON.parse(purchasedOrders) : [];
            if (purchasedOrders.includes(data.actionField.id)) {
                //console.log('Событие purchase для заказа ' + data.actionField.id + ' уже отправлено');
                return;
            }
            purchasedOrders.push(data.actionField.id);
            sessionStorage.setItem('ecommerce_purchased_orders', JSON.stringify(purchasedOrders));
        }

        window.dataLayer = window.dataLayer || [];
        dataLayer.push({
            "ecommerce": {
                "currencyCode": "RUB",
                "purchase": data
            }
        });
        ///console.log('Ecommerce purchase:', data);
    },

    impressions: function(items) {
        if (!items || !items.length) return;

        if (typeof sessionStorage !== 'undefined') {
            var viewedItems = sessionStorage.getItem('ecommerce_viewed_items');
            viewedItems = viewedItems ? JSON.parse(viewedItems) : [];
            // Фильтруем товары, которые еще не были просмотрены
            items = items.filter(item => !viewedItems.includes(item.id));
            if (!items.length) {
                console.log('Все товары уже просмотрены');
                return;
            }
            // Сохраняем ID просмотренных товаров
            items.forEach(item => viewedItems.push(item.id));
            sessionStorage.setItem('ecommerce_viewed_items', JSON.stringify(viewedItems));
        }

        window.dataLayer = window.dataLayer || [];
        dataLayer.push({
            "ecommerce": {
                "currencyCode": "RUB",
                "impressions": items
            }
        });
        //console.log('Ecommerce impressions:', items);
    }
};

document.addEventListener('DOMContentLoaded', function() {
    var items = [];
    document.querySelectorAll('.tm-tovar').forEach(function(element) {
        var ecommerceData = element.getAttribute('data-ecommerce');
        if (ecommerceData) {
            items.push(JSON.parse(ecommerceData));
        }
    });
    Ecommerce.impressions(items);
});