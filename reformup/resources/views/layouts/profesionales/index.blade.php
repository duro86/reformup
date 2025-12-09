@extends('layouts.main')

@section('title', 'Buscar profesionales - ReformUp')

@section('content')
    <x-navbar active="perfil"/>

    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">
            <h1 class="h2 fw-bold mb-3 pb-2 border-bottom text-center text-md-start">
                <i class="bi bi-search me-2 text-primary"></i>
                Buscar profesionales
            </h1>
            <p class="text-muted mb-4 text-center text-md-start">
                Filtra por empresa, ciudad, provincia o valoración mínima y encuentra el profesional que encaja contigo.
            </p>

            <div id="app">
                <profesionales-grid></profesionales-grid>
            </div>
        </div>
    </div>

    <x-footer />
@endsection

<x-alertas_sweet />
