<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- AdminLTE CSS -->
    <link href="{{ asset('adminlte/dist/css/adminlte.min.css') }}" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="{{ asset('adminlte/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <!-- Styles -->
    @livewireStyles
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        @include('layouts.navbar')

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Content -->
        <div class="content-wrapper">
            {{ $slot }}
        </div>

        <!-- Footer -->
        {{-- @include('layouts.footer') --}}

    </div>

    <!-- Scripts -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

    @livewireScripts
</body>

</html>
