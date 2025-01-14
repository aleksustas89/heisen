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
        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;">Вы только что оформили заказ на сайте {{ env('APP_NAME', false) }} № <b>{{ $ShopOrder->id }}</p>
        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;">Данные заказа:</p>

        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;"><b>Дата:</b> {{ date("d.m.Y H:i", strtotime($ShopOrder->created_at)) }}</p>
        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;"><b>Фио:</b> {{ $fio }}</p>
        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;"><b>Телефон:</b> {{ $ShopOrder->phone }}</p>
        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;"><b>E-mail:</b> {{ $ShopOrder->email }}</p>
        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;"><b>Способ доставки:</b> {{ $ShopOrder->ShopDelivery->name }}</p>
        
        @if ($ShopOrder->shop_delivery_id == 7)
            @php
            $value = \App\Models\ShopDeliveryFieldValue::where("shop_order_id", $ShopOrder->id)->where("shop_delivery_field_id", 10)->first();
            @endphp
            <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;"><b>Город:</b> {{ $value->value ?? '' }}</p>  
            @php
            $Type = \App\Models\ShopDeliveryFieldValue::where("shop_order_id", $ShopOrder->id)->where("shop_delivery_field_id", 14)->first();
            @endphp

            @if ($Type->value == 11) 
                @php
                $value = \App\Models\ShopDeliveryFieldValue::where("shop_order_id", $ShopOrder->id)->where("shop_delivery_field_id", 11)->first();
                @endphp
                <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;"><b>Отделение:</b> {{ $value->value ?? '' }}</p> 
            @elseif($Type->value == 15)
            @php
                $value = \App\Models\ShopDeliveryFieldValue::where("shop_order_id", $ShopOrder->id)->where("shop_delivery_field_id", 15)->first();
                @endphp
                <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;"><b>Адрес:</b> {{ $value->value ?? '' }}</p> 
            @endif

        @elseif($ShopOrder->shop_delivery_id == 1)
            @php
            $value = \App\Models\ShopDeliveryFieldValue::where("shop_order_id", $ShopOrder->id)->where("shop_delivery_field_id", 25)->first();
            @endphp
            <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;"><b>Адрес:</b> {{ $value->value ?? '' }}</p>  
        @endif
        
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
                    <td style="padding: 30px; background-color: #f8f8f8; font-size:18px; text-align:center" width="100px">Всего:</td>
                    <td style="padding: 30px; background-color: #f8f8f8; font-size:18px; text-align:center" width="100px"><b>{{ App\Models\Str::price($sOrderSum) ."  ". $sOrderCurrency }}</b></td>
                </tr>

            </tbody>
        </table>

        <p style="font-size:20px">Спасибо за Ваш заказ! С ❤️ Ваш HEISEN.RU</p>

    </body>
</html>