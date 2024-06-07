@if (count($aShopGroups) > 0)
    <div class="accordion accordion-flush">
        @foreach($aShopGroups as $ShopGroup)
            <div class="accordion-item border-0">
                <h5 class="accordion-header m-0 d-flex align-items-center">
                    <div class="form-switch form-switch-purple"> 
                        <input value="{{ $ShopGroup->id }}" @if (in_array($ShopGroup->id, $ShopItemAssociatedGroups)) checked @endif class="form-check-input" name="associated_groups[]" type="checkbox" id="associated-group-{{ $ShopGroup->id }}">
                    </div>
                    <button onclick="Associated.showTab({{ $ShopGroup->id }}, {{ $aShopItem->id }})" @class([
                        "accordion-button", 
                        "fw-semibold", 
                        "collapsed",
                        "hide-arrov" => $ShopGroup->subgroups_count > 0 || $ShopGroup->subitems_count > 0 ? false : true
                    ]) type="button" @if($ShopGroup->subgroups_count > 0 || $ShopGroup->subitems_count > 0) data-bs-toggle="collapse" data-bs-target="#associated-group-tab-{{ $ShopGroup->id }}" @endif aria-expanded="false" aria-controls="associated-group-tab-{{ $ShopGroup->id }}">                                                    
                        <i class="las la-folder" style="font-size:18px"></i> 
                        <span class="fs-6">{{ $ShopGroup->name }}</span>
                    </button>
                </h5>
                <div id="associated-group-tab-{{ $ShopGroup->id }}" class="accordion-collapse collapse" aria-labelledby="flush-headingOne">
                    <div class="accordion-body">
                        <div class="sub">
                            @if ($ShopGroup->subgroups_count > 0 || $ShopGroup->subitems_count > 0)
                                <div class="text-center">
                                    <div class="spinner-border thumb-md text-primary spinner-small" role="status"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@if (count($aShopItems) > 0)
    <div class="mt-4 table-responsive-sm">

        <table class="table table-bordered">
            <tbody>

                @foreach($aShopItems as $shopItem)
    
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

<div class="alert alert-success border-0 message" style="display:none;" role="alert">
    <strong>Успешно!</strong> Изменения были успешно сохранены.
</div>

