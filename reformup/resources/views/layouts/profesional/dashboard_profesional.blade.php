@extends('layouts.main')

@section('title', 'Panel profesional - ReformUp')

@section('content')

    <x-navbar />

    <x-profesional.profesional_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        <div class="container">
            <x-user_bienvenido />

            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        Aquí irá tu panel profesional: resumen de solicitudes, presupuestos, trabajos y comentarios.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

