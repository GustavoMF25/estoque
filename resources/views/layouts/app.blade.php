<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $empresa->nome }} - {{ config('app.name', 'Laravel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="imagex/png" href="{{ asset('storage/' . $empresa->logo) }}">

    <!-- AdminLTE CSS -->
    <link href="{{ asset('adminlte/dist/css/adminlte.min.css') }}" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="{{ asset('adminlte/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="{{ asset('css/geral.css') }}" rel="stylesheet">
    @stack('styles')

    <!-- Styles -->
    @livewireStyles
    @rappasoftTableStyles
    @rappasoftTableThirdPartyStyles
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
    @livewire('modal-dinamico')

    <!-- Scripts -->

    @yield('css')
    @stack('scripts')
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('js/geral.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/toastr/toastr.min.js') }}"></script>
    <!-- Livewire e Rappasoft -->
    @livewireScripts
    @rappasoftTableScripts
    @rappasoftTableThirdPartyScripts



    <livewire:components.toast />
    <script>
        document.addEventListener("DOMContentLoaded", function(e) {
            @if (session('success') || session()->has('success'))
                toastr.success(@json(session('success')))
            @endif

            @if (session('error') || session()->has('error'))
                toastr.error(@json(session('error')))
            @endif

            @if (session('warning') || session()->has('warning'))
                toastr.warning(@json(session('warning')))
            @endif

            Livewire.on('msgtSuccess', message => {
                toastr.success(message);
            });
        })

        window.addEventListener('toastr:success', event => {
            const detail = event.detail?.[0] ?? {};
            toastr.success(detail.success || 'Sucesso!');
        });
        window.addEventListener('toastr:error', event => {
            const detail = event.detail?.[0] ?? {};
            toastr.error(detail.error || 'Error!');
        });

        window.Dropzone = window.Dropzone || {};
        window.Dropzone.autoDiscover = false;
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>




</body>

</html>
