<form id="choose_properties">

    @if (count($properties) > 0)

        <div class="tab-pane properties-block" id="properties">
            @foreach ($properties as $property)

                @switch($property->type)
                    
                    @case(4)

                        @if (isset($lists[$property->shop_item_list_id]))

                            <div class="list-group-item">
                                <div class="row mb-3 admin-item-property">
        
                                    <div class="col-10">
                                        <label class="mb-1">{{ $property->name }}</label>

                                        @foreach ($lists[$property->shop_item_list_id] as $key => $listItem)

                                            <div class="form-check form-switch form-switch-success">
                                                <input class="form-check-input" name="property_{{ $property->id }}[]" value="{{ $key }}" type="checkbox" id="{{ $property->id }}{{ $key }}">
                                                <label class="form-check-label" for="{{ $property->id }}{{ $key }}">{{ $listItem }}</label>
                                            </div>
                                    
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        @endif

                    @break
                    @default
                        
                @endswitch

            @endforeach
        </div>
    @endif

    <input type="hidden" name="shop_item_id" value="{{ $shop_item_id }}" />

</form>



