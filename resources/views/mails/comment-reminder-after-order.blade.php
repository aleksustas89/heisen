<html>
    <head>
    </head>
    <body>

        <div><img src="https://{{ env('APP_NAME', false) }}/images/logo.png" /></div>

        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;">Здравствуйте.</p>
        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;">Недавно Вы делали заказ на сайте {{ env('APP_NAME', false) }}</p>
        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;">Будем очень благодарны, если Вы оставите отзыв о купленных товарах:</p>

        <table style='width:100%' border="0">

            <tbody>

                @foreach ($ShopOrder->ShopOrderItems as $orderItem)
                    @php
                    $ShopItem = $orderItem->ShopItem->modification_id > 0 ? $orderItem->ShopItem->parentItemIfModification() : $orderItem->ShopItem;
                    @endphp
                    <tr>
                        <td style="padding: 30px; background-color: #f8f8f8;">
                            
                            <table>
                                <tr>
                                    <td style="padding:10px;">
                                        @foreach ($ShopItem->getImages(false) as $image)
                                            <img class="uk-comment-avatar" src="https://{{ env('APP_NAME', false) }}{{ $image['image_small'] }}" width="80" height="80" alt="">
                                        @endforeach  
                                    </td>
                                    <td style="padding:10px;">
                                        <div><a style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;" href="https://{{ env('APP_NAME', false) }}{{ $ShopItem->url() }}">{{ $orderItem->name }}</a></div> 
                                    </td>
                                </tr>
                            </table>
                        
                        </td>
                    </tr>
                @endforeach



            </tbody>
        </table>

        <p style="font-size: 16px; line-height: 18px; font-family: 'Open Sans', sans-serif;">С уважением, {{ env('APP_NAME', false) }}</p>

    </body>
</html>