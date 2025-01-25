@if ($groupShopItems)
    <table class="table table-bordered">
        <tbody>
            <tr>
                <td>
                    <div class="d-flex">
                        <div class="form-check form-switch form-switch-success">
                            <input id="applied_all" onclick="SwitchAll.init($(this))" class="form-check-input" type="checkbox">
                        </div>
                        <label for="applied_all"><b>Применить/Отменить для всех</b></label>
                    </div>
                </td>
            </tr>
            @foreach ($groupShopItems as $groupShopItem)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">

                            <div class="form-check form-switch form-switch-success">
                                <input 
                                    @if (!isset(request()->shop_group_id))
                                        checked
                                    @elseif(count($aCheckedIds) > 0 && in_array($groupShopItem->id, $aCheckedIds)) 
                                        checked
                                    @endif
                                        name="shop_items[]" value="{{$groupShopItem->id}}" id="apply_{{$groupShopItem->id}}" class="form-check-input" type="checkbox">
                            </div>


                            @foreach ($groupShopItem->getImages(false) as $image)
                                <div class="mx-2"><img src="{{ $image['image_small'] }}" alt="" height="80"></div>
                            @endforeach 


                            <label for="apply_{{$groupShopItem->id}}">{{ $groupShopItem->ShopGroup->name }} / {{ $groupShopItem->name }}({{$groupShopItem->id}})</label>
                        </div>
                    </td>
                
                </tr>
            @endforeach
        </tbody>
    </table>
@endif