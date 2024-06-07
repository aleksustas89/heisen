@if (count($ShopItems) > 0)
    <div class="mt-4 table-responsive-sm">

        <table class="table table-bordered">
            <tbody>

                @foreach($ShopItems as $shopItem)
    
                    @include("admin.shop.item.associated.window_item", [
                        "shopItem" => $shopItem, 
                        "ShopItemAssociatedItems" => $ShopItemAssociatedItems,
                        "aShopItem" => $aShopItem
                    ])
                @endforeach
                
            </tbody>
        </table>


    </div>
@endif