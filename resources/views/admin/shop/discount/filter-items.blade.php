@if ($ShopItems && count($ShopItems) > 0)

    <div class="card bg-warning text-white">
        <div class="card-body">
            <blockquote class="card-bodyquote mb-0">
                Применить скидку к найденным товарам?
            </blockquote>
        </div>
    </div>
    <div class="items-applied">

        <table class="table table-bordered">

            <tbody>
                <tr>
                    <td>
                        <div class="d-flex">
                            <div class="form-check form-switch form-switch-warning">
                                <input onclick="SwitchAll.init($(this))" id="apply_all" class="form-check-input" type="checkbox">
                            </div>
                            <label for="apply_all"><b>Применить/Отменить для всех</b></label>
                        </div>
                    </td>
                </tr>
                @foreach ($ShopItems as $ShopItem)
                    <tr>
                        <td>
                            <div class="d-flex">
                                <div class="form-check form-switch form-switch-warning">
                                    <input id="apply_{{$ShopItem->id}}" class="form-check-input" type="checkbox">
                                </div>
                                <label for="apply_{{$ShopItem->id}}">/ {{ $ShopItem->name }}({{$ShopItem->id}})</label>
                            </div>
                        </td>
                    
                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>

@endif