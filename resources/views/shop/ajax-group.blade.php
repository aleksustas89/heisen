@php
$client = Auth::guard('client')->user();

@endphp
@foreach ($items as $item)
@include('shop.list-item', [
    'item' => $item,
    'client' => $client,
    'clientFavorites' => !is_null($client) ? $client->getClientFavorites() : [],
])
@endforeach  

{{ $items->links(('vendor.pagination.default')) }}
