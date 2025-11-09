<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','ReformUp')</title>

  <!-- Favicon Reformup-->
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon-reformup.ico') }}">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

  <!-- Bootstrap Icons (para los iconos, <i class="bi...>) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <!-- Bootstrap JS Bundle (incluye Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  {{-- Vite --}}
  @vite(['resources/js/app.js'])
  
</head>

<!-- Body -->
<body class="bg-white">
 {{-- @include('layouts.partials.navbar')    placeholder vacío por ahora --}}
  <main>@yield('content')</main>
 {{-- @include('layouts.partials.footer')   {{-- placeholder vacío por ahora --}}
 {{-- Alerta SweetAlert2 --}}
<x-alertas_sweet />
</body>
</html>
