<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Прием платежа с помощью виджета ЮKassa</title>

  
  <script src="https://yookassa.ru/checkout-widget/v1/checkout-widget.js"></script>
 </head>
 <body>
  <p>Ниже отобразится платежная форма. Если вы еще не создавали платеж и не передавали токен для инициализации виджета, появится сообщение об ошибке.</p>

  
  <div id="payment-form"></div>

  <p>Данные банковской карты для оплаты в <b>тестовом магазине</b>:</p>
  <ul>
   <li>номер — <b>5555 5555 5555 4477</b></li>
   <li>срок действия — <b>01/30</b> (или другая дата, больше текущей)</li>
   <li>CVC — <b>123</b> (или три любые цифры)</li>
   <li>код для прохождения 3-D Secure — <b>123</b> (или три любые цифры)</li>
  </ul>
  <p><a href=https://yookassa.ru/developers/payment-acceptance/testing-and-going-live/testing#test-bank-card>Другие тестовые банковские карты</a></p>

  <script>
  //Инициализация виджета. Все параметры обязательные.
  const checkout = new window.YooMoneyCheckoutWidget({
      confirmation_token: 'ct-287e0c37-000f-5000-8000-16961d35b0fd', //Токен, который перед проведением оплаты нужно получить от ЮKassa
      return_url: 'https://example.com/', //Ссылка на страницу завершения оплаты, это может быть любая ваша страница

      //При необходимости можно изменить цвета виджета, подробные настройки см. в документации
       //customization: {
        //Настройка цветовой схемы, минимум один параметр, значения цветов в HEX
        //colors: {
            //Цвет акцентных элементов: кнопка Заплатить, выбранные переключатели, опции и текстовые поля
            //control_primary: '#00BF96', //Значение цвета в HEX

            //Цвет платежной формы и ее элементов
            //background: '#F2F3F5' //Значение цвета в HEX
        //}
      //},
      error_callback: function(error) {
          console.log(error)
      }
  });

  //Отображение платежной формы в контейнере
  checkout.render('payment-form');
  </script>
 </body>
</html>