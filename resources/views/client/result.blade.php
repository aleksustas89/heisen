@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @php
                        $client = Auth::guard('client')->user();
                    @endphp

                    @if (!is_null($client))
                        
                        <h1>Вы авторизовались как {{ $client->name }}</h1>
                    @else
                        <p>Вы не авторизованны  </p>   
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection