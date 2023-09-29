@php
$client = Auth::guard('client')->user();
@endphp

@foreach ($SearchWords as $SearchWord)
    @include('shop.list-item', [
        'item' => $SearchWord->SearchPage->ShopItem,
        'client' => $client,
        'clientFavorites' => !is_null($client) ? $client->getClientFavorites() : [],
    ])
@endforeach   

<!--пагенация-->
@if (count($SearchWords) > 0)
    @php
    $links = [
        'q' => $q
    ];
    @endphp
    <div class="pagination-auto uk-hidden">
    {{ $SearchWords->appends($links)->links(('vendor.pagination.default')) }}
    </div>
@endif