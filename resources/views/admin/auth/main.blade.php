
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Admin Panel - Authorization</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('/assets/images/favicon.ico') }}">

     <!-- App css -->
     <link href="{{ asset('/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
     <link href="{{ asset('/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
     <link href="/assets/css/admin.css" rel="stylesheet" type="text/css" />


     <link href="{{ asset('/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />


<link href="/assets/css/admin.css?id=4f3c33cf5f6498dc34ed6729ed490f1b" rel="stylesheet" />

</head>

<body id="body" class="auth-page" style="background-image: url('/assets/images/admin-bg.jpg'); background-size: cover; background-position: center center;">
   <!-- Log In page -->
    <div class="container-md">
        <div class="row vh-100 d-flex justify-content-center">
            <div class="col-12 align-self-center">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mx-auto">
                            <div class="card">
                                <div class="card-body p-0 auth-header-box">
                                    <div class="text-center p-3">
                                        <a href="index.html" class="logo logo-admin">
                                            <img src="/assets/images/logo-sm.png" height="50" alt="logo" class="auth-logo">
                                        </a>
                                        <h4 class="mt-3 mb-1 fw-semibold text-white font-18">Let's Get Started Powerful Panel</h4>   
                                        <p class="text-muted  mb-0">Sign in to continue to Powerful Panel.</p>  
                                    </div>
                                </div>
                                <div class="card-body pt-0">                                    
                                    @yield('content')
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-body-->
            </div><!--end col-->
        </div><!--end row-->
    </div><!--end container-->

    <!-- App js -->
    <script src="{{ asset('/assets/js/app.js') }}"></script>
    
</body>

</html>