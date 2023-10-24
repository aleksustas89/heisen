@if ($ShopItems && count($ShopItems) > 0)

    <div class="card bg-warning text-white">
        <div class="card-body">
            <blockquote class="card-bodyquote mb-0">
                Применить скидку к найденным товарам?
            </blockquote>
        </div>
    </div>
    <div class="items-applied">


        <div class="d-flex my-3">
            <div class="form-check form-switch form-switch-warning">
                <input onclick="SwitchAllWithAccordion.init($(this))" id="apply_all" class="form-check-input" type="checkbox">
            </div>
            <label for="apply_all"><b>Применить/Отменить для всех</b></label>
        </div>
  
        <div class="accordion" id="accordionExample">
            
            @foreach ($ShopItems as $ShopItem)

                @php
                    $Modifications = \App\Models\ShopItem::where("modification_id", $ShopItem->id)->whereIn("id", $aModifications)->get();
                @endphp

                @if (count($Modifications) > 0)
                    <div class="accordion-item">
                        <h5 class="accordion-header m-0" id="headingOne">
                            <button class="accordion-button fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#shopitem{{ $ShopItem->id }}" aria-expanded="false" aria-controls="collapseOne">
                                
                                @foreach ($ShopItem->getImages(false) as $image)
                                    <div class="mx-2"><img src="{{ $image['image_small'] }}" alt="" height="80"></div>
                                @endforeach 
                                
                                {{ $ShopItem->name }} ({{ $ShopItem->id }})
                            </button>
                        </h5>
                        <div id="shopitem{{ $ShopItem->id }}" class="accordion-collapse collapse" aria-labelledby="headingOne" style="">
                            <div class="accordion-body">

                                <table class="table table-bordered">

                                    <tbody>
                                        @foreach ($Modifications as $Modification)
                                            <tr>
                                                <td>
                                                    <div class="d-flex">
                                                        <div class="form-check form-switch form-switch-warning">
                                                            <input name="apply_discount[]" value="{{$Modification->id}}" id="apply_{{$Modification->id}}" class="form-check-input" type="checkbox">
                                                        </div>

                                                        <label for="apply_{{$Modification->id}}">{{ $Modification->parentItemIfModification()->ShopGroup->name }} / {{ $Modification->name }}({{$Modification->id}})</label>
                                                    </div>
                                                </td>
                                            
                                            </tr>
                                        @endforeach
                                    </tbody>
                        
                                </table>

                            </div>
                        </div>
                    </div>
                @endif
            @endforeach



        </div>

    </div>

@else

    <div class="card bg-danger text-white">
        <div class="card-body">
            <blockquote class="card-bodyquote mb-0">
                Не найдено товаров по Вашему запросу. Попробуйте изменить критерии поиска.
            </blockquote>
        </div>
    </div>

@endif