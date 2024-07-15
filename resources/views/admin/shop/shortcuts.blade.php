@foreach ($shopItem->ShopItemShortcuts as $ShopItemShortcut)
    @php
        $routeDeleteShortcutGroup =  route("deleteShortcutGroup", ['shopItem' => $ShopItemShortcut->shop_item_id, 'shopGroup' => $ShopItemShortcut->shop_group_id]);
        $ShopItemShortcutGroup = $ShopItemShortcut->ShopGroup;
    @endphp
    @if (!is_null($ShopItemShortcutGroup))
        @php
        $ParentGroup = $ShopItemShortcutGroup->parent_id > 0 ? \App\Models\ShopGroup::find($ShopItemShortcutGroup->parent_id) : false; 
        @endphp
        <span 
            id="shortcut_group_{{ $ShopItemShortcut->shop_group_id }}" 
            class="saved badge badge-soft-{{ $BadgeClasses[rand(0, count($BadgeClasses) - 1)] }}">{{ $ParentGroup ? $ParentGroup->name ." / " : '' }}{{ $ShopItemShortcut->ShopGroup->name }} [{{ $ShopItemShortcutGroup->id }}]
            <a href="javascript:void(0)" onclick="Shortcut.delete($(this), '{{ $routeDeleteShortcutGroup }}')" class="mdi mdi-close"></a>
        </span>
    @endif
@endforeach