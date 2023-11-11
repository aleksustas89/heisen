<option value="">...</option>
@foreach ($options as $key => $value)
    <option data-id="{{ $key }}" value="@if(isset($valueWithCode))[{{ $key }}] @endif{{ $value }}">{{ $value }}</option>
@endforeach  