<option value="">...</option>
@foreach ($options as $key => $value)
    <option data-id="{{ $key }}" value="{{ $value }}">{{ $value }}</option>
@endforeach  