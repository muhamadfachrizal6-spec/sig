<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/css/mainstyle.css') }}">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/logo.png">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome-pro.css') }}">
    <meta name="description" content>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIG Institute</title>
    @livewireStyles
</head>

<body>
    @include('components.navbarMobile')
    <div class="containerNav">
        @include('components.sidebar')
        <div class="navbarNav bg-light">
            @include('components.navbar', ['title' => $title ?? 'Dashboard'])
            <div class="content m-4">
                @yield('contents')
            </div>
        </div>
    </div>
    @livewireScripts
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="{{ asset('assets/js/addons.js') }}"></script>
</body>

</html>
