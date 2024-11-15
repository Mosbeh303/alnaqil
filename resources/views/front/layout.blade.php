<!DOCTYPE html>
<html lang="ar">

<head>
    <!-- Charset meta -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <!-- Responsive meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <!-- App Name meta -->
    <meta name="application-name" content="{{__('title')}}" />

    <!-- Description meta -->
    <meta name="description"
        content="انطلقت شركة الناقل المحلي لتكون الشريك الاستراتيجي الأسرع في قطاع التجارة الالكترونية للمتاجر , ولتلبية رغبات التجار في حلول لوجستية سريعة لتوصيل طلباتهم وشحناتهم في وقت قياسي وزيادة رضى عملائهم ." />

    <!-- Keywords meta -->
    <meta name="keywords" content="" />

    <link rel="shortcut icon" href="imgs/favicon.ico" type="image/x-icon">

    <!-- Title -->
    <title>{{__('title')}} - @yield('title')</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">

    @if (app()->getLocale() == 'ar')
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="{{asset('front/css/bootstrap-rtl.css')}}" />
    @endif


    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome File -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />

    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- Toaster CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <!-- General Style -->
    <link rel="stylesheet" href="{{asset('front/css/general.css')}}" />

    <!-- datatables buttoons -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css" />

    <!-- Custom Style -->
    <link rel="stylesheet" href="{{asset('front/css/style.css')}}" />

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-185443585-1"></script>

    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-185443585-1');
    </script>

    <!-- PWA  -->
    {{--
    <meta name="theme-color" content="#465458" />
    <link rel="apple-touch-icon" href="https://alnaqil.sa/images/logo1.png">
    <link rel="manifest" href="{{ asset('/manifest.json') }}"> --}}

    @stack('styles')

</head>

<body>


    <header style="height:auto" class="pt-2">
        <div class="container">
            <div class="all">



                <div class="logo row">
                    <div class="col-md-2 col-sm-3">
                        <h1>
                            <img alt="logo" src="https://alnaqil.sa/images/logo1.png" width="180px" />
                        </h1>
                    </div>
                    <div class="navigation col-md-10 col-sm-9">
                        <ul>
                            <li>
                                <a href="/" class="active">
                                    <i class="fal fa-fw fa-3x fa-home"></i>
                                    <span>{{__('home')}}</span>

                                </a>
                            </li>

                            <li>
                                <a href="/login">
                                    <i class="fal fa-fw fa-3x fa-user"></i>
                                    <span> {{__('my_account')}}</span>
                                </a>
                            </li>

                            <li>
                                <a href="/terms">

                                    <i class="fal fa-fw fa-3x fa-info-circle"></i>
                                    <span>{{__('terms')}}</span>
                                </a>
                            </li>

                            <li>
                                <a href="/contact">

                                    <i class="fal fa-fw fa-3x fa-phone"></i>
                                    <span>{{__('contact')}}</span>
                                </a>
                            </li>

                            <li>
                                @if (app()->getLocale() == 'ar')
                                <a href="{{ route('set_locale', 'en') }}">
                                    <img alt="English" src="https://unpkg.com/language-icons/icons/en.svg"
                                        style="width: 40px; position: relative; top:0">
                                </a>
                                @else
                                <a href="{{ route('set_locale', 'ar') }}">
                                    <img alt="Arabic" src="https://unpkg.com/language-icons/icons/ar.svg"
                                        style="width: 40px; position: relative; top:0">
                                </a>
                                @endif

                            </li>


                        </ul>
                    </div>
                </div>






                @yield('hero')


            </div>
        </div>
    </header>

    @yield('content')

    <footer class="py-2">
        <div class="container pt-5 pb-3">
            <div class="row">
                <div class="col-md-3 mb-3">
                    {{-- <h4 class="mb-3">{{__('title')}}</h4> --}}
                    <img src="{{asset('images/logo_white.png')}}" class="w-50"
                        style="position: relative; bottom:30px" />
                    <p>{{__('footer_desc')}}</p>
                </div>
                <div class="col-md-3 mb-3">
                    <h4 class="mb-3">{{__('quick_links')}}</h4>
                    <ul class="list-unstyled">
                        <li><a href="/register">{{__('register')}}</a></li>
                        <li><a href="{{url(app()->getLocale(). '/login')}}">{{__('login')}}</a></li>
                        <li><a href="'/contact'">{{__('contact')}}</a></li>
                        <li><a href="/terms">{{__('terms')}}</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-3">
                    <h4 class="mb-3">{{__('contact_us')}}</h4>
                    <ul class="list-unstyled">
                        <li><i class='fab fa-fw fa-whatsapp'></i> <a
                                href="https://api.whatsapp.com/send?phone=966590700745">{{__('whatsapp')}}</a></li>
                        <li><i class='fab fa-fw fa-twitter'></i> <a
                                href="https://mobile.twitter.com/alnaqilsa">{{__('twitter')}}</a></li>
                        <li><i class='fab fa-fw fa-instagram'></i><a href="https://www.instagram.com/alnaqilsa/">
                                {{__('instagram')}}</a></li>
                        <li><i class='fab fa-fw fa-snapchat-ghost'></i><a href="https://www.snapchat.com/add/alnaqilsa">
                                {{__('snapchat')}}</a></li>
                        <li><i class='fas fa-phone'></i><a href="tel:8001240316">
                                8001240316</a></li>
                        <li><i class='fas fa-phone'></i><a href="tel:0590700745">
                                0590700745</a></li>
                        <li><i class='fas fa-envelope'></i><a href="mailto:info@alnaqil.sa">
                                info@alnaqil.sa</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-3">
                    <h4 class="mb-3">{{__('users_rights_postal')}}</h4>
                    <ul class="list-unstyled">
                        <li><i class='fas fa-file-pdf ml-1'></i><a target="_blank"
                                href="{{asset('users-rights-postal.pdf')}}">{{__('download')}}</a></li>

                    </ul>
                </div>
                <div class="col-12 text-center mt-5">

                    <p class="mb-3">{{__('copyright')}} &copy; {{date('Y')}} </p>

                </div>
            </div>
        </div>

    </footer>





    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://cdn.rtlcss.com/bootstrap/v4.2.1/js/bootstrap.min.js"></script>

</body>

@stack('scripts')

</html>