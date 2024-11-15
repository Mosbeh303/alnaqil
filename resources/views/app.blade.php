<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{--
    <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <!-- Scripts -->
    @routes
    <script src="{{ mix('js/app.js') }}" defer></script>
    <script src="{{ mix('js/vendor.js') }}" defer></script>
    <script src="{{ mix('js/manifest.js') }}" defer></script>

    <!-- PWA  -->
    {{--
    <meta name="theme-color" content="#465458" />
    <link rel="apple-touch-icon" href="https://alnaqil.sa/images/logo1.png">
    <link rel="manifest" href="{{ asset('/manifest.json') }}"> --}}

    <style>
        .aside {
            scrollbar-color: var(--scroll-thumb-color, grey) var(--scroll-track, transparent);
            scrollbar-width: thin;
        }

        .aside::-webkit-scrollbar {
            width: 5px;
            height: 10px;
        }

        .aside::-webkit-scrollbar-track {
            background-color: var(--scroll-track, transparent);
            border-radius: 5px;
        }

        .aside::-webkit-scrollbar-thumb {
            background-color: #B9BDC1;
            background-image: var(--scroll-thumb, none);
            border-radius: var(--scroll-thumb-radius, var(--scroll-radius));
        }

        .aside {
            --scroll-size: 2px;
            --scroll-radius: 10px;
            --scroll-track: rgb(255 255 255 / 10%);
            --scroll-thumb-color: #fff;
        }
    </style>




</head>

<body class="font-sans antialiased overflow-x-hidden">
    @inertia

    {{--@env ('local')
    <script src="http://localhost:8080/js/bundle.js"></script>
    @endenv--}}
</body>

</html>

{{-- <script src="{{ asset('/sw.js') }}"></script>
<script>
    if (!navigator.serviceWorker.controller) {
        navigator.serviceWorker.register("/sw.js").then(function (reg) {
            console.log("Service worker has been registered for scope: " + reg.scope);
        });
    }
</script> --}}

<script>
    navigator.serviceWorker.getRegistrations().then(function(registrations) {
    for(let registration of registrations) {
     registration.unregister()
   } })

</script>