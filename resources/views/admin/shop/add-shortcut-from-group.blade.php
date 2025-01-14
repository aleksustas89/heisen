<div class="modal fade" id="add-to-groups" tabindex="-1" aria-labelledby="Modal" aria-hidden="true">
    <form action="{{ route('modification.store') }}" method="POST" enctype="multipart/form-data" id="addShopItemsToGroups">
        @csrf
        @method('POST')

        <div class="modal-dialog modal-fullscreen-lg-down">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title" id="Modal">Добавить в группы</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>

                <div class="modal-body">

                    @if (!is_null($shop_items))
                        
                        <div class="mb-5">
                            <label class="mb-1">Дополнительные группы</label>
                            <input type="text" name="shortcut_group_id" class="form-control" placeholder="Пожалуйста, введите еще хотя бы 2 символа">
                            <div class="shortcut_groups position-absolute">

                            </div>
                        </div>
                            
                        @foreach ($shop_items as $k => $item)
                            <input type="hidden" name="shop_items[]" value="{{ $k }}" />
                        @endforeach

                    @else
                        <p>Выберите товары</p>
                    @endif
                <div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" onclick="">Добавить</button>
        </div>
    </form>
</div>