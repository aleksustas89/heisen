<html>
    <head></head>
    <body>

        @php
            $fio = implode(" ", [$ShopOrder->surname, $ShopOrder->name]);
            $sOrderSum = $ShopOrder->getSum();
            $sOrderCurrency = $ShopOrder->ShopCurrency->name;
        @endphp

        <p>Здравствуйте.</p>
        <div>На сайте {{ env('APP_NAME', false) }} был сделан новый заказ № <b>{{ $ShopOrder->id }}</b></div>
        <div><b>Дата:</b> {{ date("d.m.Y H:i", strtotime($ShopOrder->created_at)) }}</div>
        <div><b>Фио:</b> {{ $fio }}</div>
        <div><b>Телефон:</b> {{ $ShopOrder->phone }} </div>
        <div><b>E-mail:</b> {{ $ShopOrder->email }} </div>
        <div><b>Способ доставки:</b> {{ $ShopOrder->ShopDelivery->name }} </div>
        <div><b>Способ оплаты:</b> {{ $ShopOrder->ShopPaymentSystem->name }} </div>
        <div><b>Описание заказа:</b> {{ $ShopOrder->description }} </div>

        <br/>

        <table style='min-width:700px' border="1">
            <thead>
                <tr>
                    <th width="40px">№</th>
                    <th>Наименование</th>
                    <th width="100px">Цена, {{ $sOrderCurrency }}</th>
                    <th width="100px">Кол-во</th>
                    <th width="100px">Сумма, {{ $sOrderCurrency }}</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($ShopOrder->ShopOrderItems as $orderItem)
                    <tr>
                        <td style="text-align:center" width="40px">{{ $orderItem->id }}</td>
                        <td>{{ $orderItem->name }}</td>
                        <td style="text-align:center" width="100px">{{ App\Models\Str::price($orderItem->price) }}</td>
                        <td style="text-align:center" width="100px">{{ $orderItem->quantity }}</td>
                        <td style="text-align:center" width="100px">{{ App\Models\Str::price($orderItem->price * $orderItem->quantity) }}</td>
                    </tr>
                @endforeach

                <tr> 
                    <td width="40px">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td width="100px">&nbsp;</td>
                    <td width="100px">Всего к оплате:</td>
                    <td width="100px" style="text-align:center"><b>{{ App\Models\Str::price($sOrderSum) ."  ". $sOrderCurrency }}</b></td>
                </tr>

            </tbody>
        </table>

        <p><a href="{{ env('APP_NAME', false) }}/admin/shopOrder/{{ $ShopOrder->id }}/edit">Перейти к заказу</a></p>

    </body>
</html>