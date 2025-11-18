@extends('layouts.main')

@section('title', 'Panel Admin - ReformUp')

@section('content')

    {{-- Navbar principal --}}
    <x-navbar />

    {{-- Sidebar admin --}}
    <x-admin.admin_sidebar />
    <x-admin.admin_bienvenido />
    {{-- Dashboard admin --}}
    <x-admin.nav_movil active="panel" />

    {{-- Contenido principal respetando el sidebar --}}
    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">

            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        Bienvenido al panel de <strong>administrador</strong>. Desde aquí puedes gestionar usuarios,
                        profesionales,
                        solicitudes, presupuestos, trabajos y comnetarios.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
