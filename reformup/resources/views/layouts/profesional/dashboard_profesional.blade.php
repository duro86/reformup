@extends('layouts.main')

@section('title', 'Panel profesional - ReformUp')

@section('content')

    {{-- Navbar principal --}}
    <x-navbar />

    {{-- Sidebar profesional --}}
    <x-profesional.profesional_sidebar />
    {{-- Bienvenida --}}
    <x-user_bienvenido />
    {{-- NAV SUPERIOR SOLO MÓVIL/TABLET --}}
    <x-profesional.nav_movil active="panel" />

    {{-- Contenido principal respetando el sidebar --}}
    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">
            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        Bienvenido panel <strong>profesional</strong> : resumen de solicitudes, presupuestos, trabajos y
                        comentarios.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
