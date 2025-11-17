@extends('layouts.main')

@section('title', 'Panel Admin - ReformUp')

@section('content')

    <x-navbar />

    <x-admin.admin_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        <div class="container">
            <x-user_bienvenido />

            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        Aquí irá tu panel admin: resumen de solicitudes, presupuestos, trabajos y comentarios.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection