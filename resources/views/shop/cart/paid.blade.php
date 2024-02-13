@extends('skeleton')

@section('seo_title', "Завершение заказа")
@section('seo_description', "Завершение заказа")

@section('skeleton_content')

    <div class="uk-container uk-container-xlarge">
        <div class="uk-section-small uk-padding-remove-bottom">
            <nav aria-label="Breadcrumb">
                <ul class="uk-breadcrumb">
                    <li><a href="{{ route('home') }}">Главная</a></li>                                                                         
                    <li><span>Корзина</span></li>
                </ul>
            </nav>
        </div>

        <h1 id="item-name" class="uk-h2 uk-margin-remove-vertical uk-section-small uk-padding-remove-top">Оформление заказа</h1>

        <div class="uk-flex uk-flex uk-align-center uk-flex-center uk-text-center uk-flex-middle uk-flex-column empty-cart">
            <div>
                <a class="uk-navbar-item uk-logo" href="/" aria-label="Back to Home" tabindex="0" role="menuitem">HEISEN</a>
            </div>

            @if($status == 1)
                <h1>Ваш заказ оплачен</h1>
            @else
                <h1>Ваш заказ оформлен.</h1>
                @if ($step < 5)
                    <p>Не закрывайте страницу, идет обновление статуса заказа!</p>
                @endif
                <script>
                    $(function() {
                        OrderStatus.update({{ $guid }});
                    });
                </script>
            @endif

            <div>Мы уже работаем, чтобы отправить Ваш заказ как можно скорее.</div>
        </div>

    </div>

@endsection


@section("js")
    <script>
        var OrderStatus = {
            update: function(guid, step = 0) {
                if (step < 5) {
                    setTimeout(function() {
                        step++;
                        window.location.href = "https://heisen.ru/cart/payment/result?guid=" + guid + "&step=" + step;
                    }, 2000);
                }

            }
        }
    </script>
@endsection