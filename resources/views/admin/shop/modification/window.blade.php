@php
    $success = count($aResult) > 0 && $ShopItem ? true : false;
@endphp

<div class="modal fade" id="Modal" tabindex="-1" aria-labelledby="Modal" aria-hidden="true">
    <form action="{{ route('modification.store') }}" method="POST" id="formEdit" enctype="multipart/form-data">
        @csrf
        @method('POST')
        <div class="modal-dialog modal-fullscreen-lg-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="Modal">Создать модификации</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <table class="table mb-0 table-centered" id="create_modifications_table">
                        <tbody>
                            @if ($success)
                                @foreach ($aResult as $key => $Result) 
                                    <tr>
                                        <td class="text-center" style="width:150px;">
                                            <i class="la la-image font-40" title=""></i> 
                                            <input name="item_{{ $key }}_image" type="hidden" value="" />
                                        </td>
                                        <td>
                                            <textarea name="item_{{ $key }}_name" class="form-control">{{ $Result['name'] }}</textarea>

                                            @foreach ($Result["properties"] as $property => $value) 
                                                <input type="hidden" name="item_{{ $key }}_property_{{ $property }}" value="{{ $value }}" />
                                            @endforeach
                                        </td>

                                        <td style="width:200px;">
                                            <input name="item_{{ $key }}_price" class="form-control" value="{{ $ShopItem->price }}" />
                                        </td>
                                        <td style="width:180px;">
                                            <button type="button" class="btn btn-dark btn-round" onclick="adminModification.showImages({{ $key }})">
                                                <i class="la la-image font-16" title=""></i> 
                                                Выбрать фото
                                            </button>
                                        </td>
                                        <td style="width:75px;">

                                            <button type="button" class="round-btn" onclick="adminModification.delete($(this))">
                                                <i class="las la-trash-alt font-28"></i>
                                            </button>
                                            
                                        </td>
                                    </tr>
                                @endforeach

                            @else 
                                <p>Вы не выбрали свойства, для создания модификаций</p>
                            @endif
                        </tbody>
                    </table>

                    @if ($success)

                        <div id="choose-mod-image-container" class="choose-mod-image-container">

                            <h5>Выберите фото для модификации <span class="choose_modification_name" id="choose_modification_name"></span></h5>

                            <div class="choose-mod-image-wrap">
                                <div class="d-flex flex-wrap">
                                    @foreach ($ShopItem->ShopItemImages as $ShopItemImage)
                                        @php
                                        $image_url = $ShopItem->path() . $ShopItemImage->image_small;
                                        @endphp
                                        <div onclick="adminModification.chooseImg($(this))" data-id="{{ $ShopItemImage->id }}" data-img="{{ $image_url }}" class="choose-mod-image" style="background-image: url('{{ $image_url }}');"></div>
                                    @endforeach
                                </div>
                            </div>

                        </div>

                    @endif

                    
                <div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="shop_item_id" value="{{ $ShopItem->id }}" />
            <button type="button" class="btn btn-de-secondary" data-bs-dismiss="modal">Закрыть</button>
            @if ($success)
                <button type="submit" class="btn btn-primary">Создать</button>
            @endif
        </div>
    </form>
</div>
