@foreach ($images as $k => $image)
    <div class="file-box d-flex align-items-center justify-content-center sortable" id="{{ $k }}">

        <a href="javascript:void(0)" onclick="if(confirm('Вы действительно хотите удалить изображение?')) {adminImage.remove('{{ route('deleteShopItemImage', [$shopItem->id, $k]) }}', $(this).parent())}">
            <i class="las la-times file-close-icon"></i>
        </a>

        <div class="text-center">
            <div class="file-box-image">

                @if (!empty($image['image_small']))
                    <a @if (!empty($image['image_large'])) href="{{ $image['image_large'] }}" @endif class="lightbox"><img src="{{ $image['image_small'] }}"></a>
                @endif
            </div>
        </div>                                                        
    </div>
@endforeach