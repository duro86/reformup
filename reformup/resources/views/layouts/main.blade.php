<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ReformUp')</title>

    <!-- Favicon Reformup-->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon-reformup.ico') }}">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Vite: JS + SCSS --}}
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

</head>
<body class="bg-white">
    <main>@yield('content')</main>

    <x-alertas_sweet />

    @stack('scripts')
</body>
</html>
