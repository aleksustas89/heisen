@extends('admin.auth.main')

@section('content')

    <form method="POST" class="my-4" action="{{ route('admin_login_action') }}">
        @csrf            
        <div class="form-group mb-2">
            <label class="form-label" for="username">Username</label>
            <input type="text" required class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ __('Email Address') }}" autofocus>                               
        </div><!--end form-group--> 

        <div class="form-group">
            <label class="form-label" for="userpassword">Password</label>                                            
            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Password') }}">                            
        </div><!--end form-group--> 

        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <div class="form-group mb-0 row">
            <div class="col-12">
                <div class="d-grid mt-3">
                    <input type="hidden" name="auth_type" value="1" />
                    <button class="btn btn-primary" type="submit">{{ __('Login') }} <i class="fas fa-sign-in-alt ms-1"></i></button>
                </div>
            </div><!--end col--> 
        </div> <!--end form-group-->                           
    </form><!--end form-->


@endsection
