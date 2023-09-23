<html>
    <head></head>
    <body>

        <p>Здравствуйте.</p>
        <div>На сайте {{ env('APP_NAME', false) }} был сделан новыйбыстрый заказ № <b>{{ $ShopQuickOrder->id }}</b></div>
        <div><b>Дата:</b> {{ date("d.m.Y H:i", strtotime($ShopQuickOrder->created_at)) }}</div>
        <div><b>Фио:</b> {{ $ShopQuickOrder->name }}</div>
        <div><b>Телефон:</b> {{ $ShopQuickOrder->phone }} </div>
        <div><b>Товар:</b> <a href="{{ $ShopQuickOrder->ShopItem->url() }}">{{ $ShopQuickOrder->ShopItem->name }}</a> </div>

        <p><a href="{{ env('APP_NAME', false) }}/admin/shopQuickOrder/{{ $ShopQuickOrder->id }}/edit">Перейти к заказу</a></p>

    </body>
</html>