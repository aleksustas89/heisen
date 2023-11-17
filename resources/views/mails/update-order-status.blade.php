<html>
    <head></head>
    <body>

        @php
            $fio = implode(" ", [$ShopOrder->surname, $ShopOrder->name]);
            $sOrderSum = $ShopOrder->getSum();
            $sOrderCurrency = $ShopOrder->ShopCurrency->name;
        @endphp

        
        <div><img src="https://{{ env('APP_NAME', false) }}/images/logo.png" /></div>

        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;">Здравствуйте.</p>
        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;">Статус заказа {{ $ShopOrder->id }} был изменен на оплачено.</p>

        <p style="font-size:20px">С ❤️ Ваш HEISEN.RU</p>

    </body>
</html>