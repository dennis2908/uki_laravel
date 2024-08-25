<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <title>Tipster Admin IG Score</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700"/>

        <link href="{{ asset('assets/css/pages/login/classic/login-4.css?v=7.0.6') }}" rel="stylesheet" type="text/css"/>

        <link href="{{ asset('assets/plugins/global/plugins.bundle.css?v=7.0.6') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.css?v=7.0.6') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('assets/css/style.bundle.css?v=7.0.6') }}" rel="stylesheet" type="text/css"/>

        <link href="{{ asset('assets/css/themes/layout/header/base/light.css?v=7.0.6') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('assets/css/themes/layout/header/menu/light.css?v=7.0.6') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('assets/css/themes/layout/brand/dark.css?v=7.0.6') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('assets/css/themes/layout/aside/dark.css?v=7.0.6') }}" rel="stylesheet" type="text/css"/>
    </head>
    <body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading" >
        <div class="d-flex flex-column flex-root">
            <div class="login login-4 login-signin-on d-flex flex-row-fluid" id="kt_login">
                <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat" style="background: rgb(15, 128, 218)">
                    <div class="login-form text-center p-7 position-relative overflow-hidden">
                        <div class="d-flex flex-center mb-5">
                            <a href="{{ url('admin/login') }}">
                                <img src="{{ asset('assets/media/logo.png') }}" class="max-h-75px" alt=""/>
                            </a>
                        </div>

                        <div class="card card-custom">
                            <div class="card-body">
                                <div class="login-signin">
                                    <div class="mb-10">
                                        <h3>Sign In To Tipster Admin</h3>
                                        <div class="text-muted font-weight-bold">Enter your details to login to your account:</div>
                                    </div>
                                    <form class="form" id="kt_login_signin_form" method="POST" action="{{ url('admin/login') }}">
                                        @csrf
                                        <div class="form-group mb-5">
                                            <input class="form-control h-auto form-control-solid py-4 px-8" type="text" placeholder="Username" name="username" autocomplete="off" />
                                        </div>
                                        <div class="form-group mb-5">
                                            <input class="form-control h-auto form-control-solid py-4 px-8" type="password" placeholder="Password" name="password" />
                                        </div>
                                        <div class="form-group d-flex flex-wrap justify-content-between align-items-center">
                                            <div class="checkbox-inline">
                                                <label class="checkbox m-0 text-muted">
                                                    <input type="checkbox" name="remember" />
                                                    <span></span>
                                                    Remember me
                                                </label>
                                                <label class="checkbox m-0 text-muted ml-27">
                                                   <a href="/admin/password/reset"> Forgot password? </a>
                                                </label>
                                            </div>
                                        </div>
                                        <button id="kt_login_signin_submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-4">Sign In</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            var KTAppSettings = {
                "breakpoints": {
                    "sm": 576,
                    "md": 768,
                    "lg": 992,
                    "xl": 1200,
                    "xxl": 1400
                },
                "colors": {
                    "theme": {
                        "base": {
                            "white": "#ffffff",
                            "primary": "#3699FF",
                            "secondary": "#E5EAEE",
                            "success": "#1BC5BD",
                            "info": "#8950FC",
                            "warning": "#FFA800",
                            "danger": "#F64E60",
                            "light": "#E4E6EF",
                            "dark": "#181C32"
                        },
                        "light": {
                            "white": "#ffffff",
                            "primary": "#E1F0FF",
                            "secondary": "#EBEDF3",
                            "success": "#C9F7F5",
                            "info": "#EEE5FF",
                            "warning": "#FFF4DE",
                            "danger": "#FFE2E5",
                            "light": "#F3F6F9",
                            "dark": "#D6D6E0"
                        },
                        "inverse": {
                            "white": "#ffffff",
                            "primary": "#ffffff",
                            "secondary": "#3F4254",
                            "success": "#ffffff",
                            "info": "#ffffff",
                            "warning": "#ffffff",
                            "danger": "#ffffff",
                            "light": "#464E5F",
                            "dark": "#ffffff"
                        }
                    },
                    "gray": {
                        "gray-100": "#F3F6F9",
                        "gray-200": "#EBEDF3",
                        "gray-300": "#E4E6EF",
                        "gray-400": "#D1D3E0",
                        "gray-500": "#B5B5C3",
                        "gray-600": "#7E8299",
                        "gray-700": "#5E6278",
                        "gray-800": "#3F4254",
                        "gray-900": "#181C32"
                    }
                },
                "font-family": "Poppins"
            };
        </script>
        <script src="{{ asset('assets/plugins/global/plugins.bundle.js?v=7.0.6') }}"></script>
        <script src="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.js?v=7.0.6') }}"></script>
        <script src="{{ asset('assets/js/scripts.bundle.js?v=7.0.6') }}"></script>
        <script>
            $('form').submit(function(e){
                e.preventDefault();

                var action = $(this).attr('action');

                var button = $(this).find('button[type="submit"]');

                button.attr('disabled', true);

                var formData = new FormData(this);

                $.ajax({
                    url: action,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data){
                        if(data.redirect != null){
                            window.location.replace(data.redirect);
                        }
                    },
                    error: function(data){
                        var result = data.responseJSON;

                        if(result.code == 422){
                            $.each(result.data, function(key, value){
                                toastr.error(value[0]);
                            })
                        }else{
                            toastr.error(result.message);
                        }

                        button.attr('disabled', false);
                    }
                })
            });

            @if($message = Session::get('success'))
                $(document).ready(function(){
                    toastr.options = {
                        "closeButton": false,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showDuration": "500",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };

                    toastr.success("{{ $message }}");
                });
            @endif

            @if($message = Session::get('error'))
                $(document).ready(function(){
                    toastr.options = {
                        "closeButton": false,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showDuration": "500",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };

                    toastr.error("{{ $message }}");
                });
            @endif
        </script>
    </body>
</html>
