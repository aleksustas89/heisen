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
        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;">На сайте {{ env('APP_NAME', false) }} был сделан новый заказ № <b>{{ $ShopOrder->id }}</b></p>
        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;">Данные заказа:</p>

        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;"><b>Дата:</b> {{ date("d.m.Y H:i", strtotime($ShopOrder->created_at)) }}</p>
        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;"><b>Фио:</b> {{ $fio }}</p>
        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;"><b>Телефон:</b> {{ $ShopOrder->phone }}</p>
        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;"><b>E-mail:</b> {{ $ShopOrder->email }}</p>
        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;"><b>Способ доставки:</b> {{ $ShopOrder->ShopDelivery->name }}</p>
        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;"><b>Способ оплаты:</b> {{ $ShopOrder->ShopPaymentSystem->name }}</p>
        @if (!empty($ShopOrder->description))
            <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;"><b>Описание заказа:</b> {{ $ShopOrder->description }}</p>
        @endif


        <table style='width:100%' border="0">
            <thead>
                <tr>
                    <th style="padding: 30px; background-color: #f8f8f8; text-align:left">Наименование</th>
                    <th style="padding: 30px; background-color: #f8f8f8;" width="100px">Цена, {{ $sOrderCurrency }}</th>
                    <th style="padding: 30px; background-color: #f8f8f8;" width="100px">Кол-во</th>
                    <th style="padding: 30px; background-color: #f8f8f8;" width="100px">Сумма, {{ $sOrderCurrency }}</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($ShopOrder->ShopOrderItems as $orderItem)
                    <tr>
                        <td style="padding: 30px; background-color: #f8f8f8; font-size:18px">{{ $orderItem->name }}</td>
                        <td style="text-align:center; padding: 30px; background-color: #f8f8f8; font-size:18px" width="100px">{{ App\Models\Str::price($orderItem->price) }}</td>
                        <td style="text-align:center; padding: 30px; background-color: #f8f8f8; font-size:18px" width="100px">{{ $orderItem->quantity }}</td>
                        <td style="text-align:center; padding: 30px; background-color: #f8f8f8; font-size:18px" width="100px">{{ App\Models\Str::price($orderItem->price * $orderItem->quantity) }}</td>
                    </tr>
                @endforeach

                <tr> 
                    <td style="padding: 30px; background-color: #f8f8f8;">&nbsp;</td>
                    <td style="padding: 30px; background-color: #f8f8f8;" width="100px">&nbsp;</td>
                    <td style="padding: 30px; background-color: #f8f8f8; text-align:center; font-size:18px" width="100px">Всего:</td>
                    <td style="padding: 30px; background-color: #f8f8f8; text-align:center; font-size:18px" width="100px"><b>{{ App\Models\Str::price($sOrderSum) ."  ". $sOrderCurrency }}</b></td>
                </tr>

            </tbody>
        </table>

        <p style="font-size:20px"><a href="{{ env('APP_NAME', false) }}/admin/shopOrder/{{ $ShopOrder->id }}/edit">Перейти к заказу</a></p>

    </body>
</html>