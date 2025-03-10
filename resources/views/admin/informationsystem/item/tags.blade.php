@foreach ($informationsystemItem->informationsystemItemTags as $informationsystemItemTag)
    @php
        $Tag = $informationsystemItemTag->Tag;
    @endphp
    @if (!is_null($Tag))

        <span 
            id="tag_{{ $Tag->id }}" 
            class="saved badge badge-soft-{{ $BadgeClasses[rand(0, count($BadgeClasses) - 1)] }}">{{ $Tag->name }}
            <a href="javascript:void(0)" onclick="Tag.delete($(this), {{ $informationsystemItemTag->id }})" class="mdi mdi-close"></a>
        </span>
    @endif
@endforeach