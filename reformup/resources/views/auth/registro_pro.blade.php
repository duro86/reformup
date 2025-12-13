@extends('layouts.main')
@section('title', 'Registro Profesional - ReformUp')

@section('content')

    <x-navbar />

    {{-- Registro de Profesionales --}}
    <div class="container my-5">

        <div class="row g-0 shadow rounded overflow-hidden align-items-stretch">
            {{-- Panel Izquierdo --}}
            <div class="col-lg-5 d-none d-lg-block" style="background:#E9F5DB;">
                <div class="h-100 p-3 d-flex flex-column">
                    <div>
                        <h2 class="mb-2 text-success">Únete como Profesional</h2>
                        <p class="text-muted mb-2">Promociona tu empresa y conecta con clientes fácilmente.</p>
                        <ul class="text-muted small mb-3">
                            <li>Registra tu empresa en minutos</li>
                            <li>Gestiona presupuestos y trabajos</li>
                            <li>Amplía tu visibilidad y alcance</li>
                        </ul>
                    </div>

                    <div class="mt-auto text-center">
                        <img src="{{ asset('img/Profesional/panel_registro/profesional_panel.jpg') }}" alt="Profesionales"
                            class="img-fluid rounded mx-auto d-block"
                            style="max-width:85%; max-height:260px; object-fit:cover;">
                    </div>
                </div>
            </div>

            {{-- Panel Derecho --}}
            <div class="col-lg-7 bg-white">
                <div class="p-3 p-lg-4">

                    <h1 class="h4 mb-3">
                        <i class="bi-building me-2"></i>
                        Crear cuenta <span class="text-primary">(Profesional)</span>
                    </h1>

                    {{-- Registro de Profesionales Opciones --}}
                    <div class="row row-cols-1 row-cols-md-2 g-4 mt-2">

                        {{-- Soy Usuario --}}
                        <div class="col text-center">
                            <a href="{{ route('validar.usuario') }}" class="btn btn-outline-primary mb-2">
                                <i class="bi bi-person-circle me-2"></i> Soy Usuario
                            </a>

                            <div>
                                <img src="{{ asset('img/Profesional/user_pro/soy_user_pro.jpg') }}" alt="Soy Usuario"
                                    class="img-fluid rounded mb-2"
                                    style="max-width: 260px; max-height:200px; object-fit:cover;">
                            </div>

                            <div class="hint-bubble mx-auto mt-1">
                                <div class="arrow-up-animated mb-1">
                                    <i class="bi bi-arrow-up-circle-fill"></i>
                                </div>
                                <p class="small mb-0 bg-light border rounded-pill d-inline-block px-3 py-2 shadow-sm">
                                    Ya estoy registrado en la web y quiero dar de alta mi empresa como profesional.
                                </p>
                            </div>
                        </div>

                        {{-- Soy Nuevo --}}
                        <div class="col text-center">
                            <a href="{{ route('registro.pro.form') }}" class="btn btn-outline-primary mb-2">
                                <i class="bi bi-person-plus me-2"></i> Soy Nuevo
                            </a>

                            <div>
                                <img src="{{ asset('img/Profesional/user_nuevo/soy_nuevo_pro.jpg') }}" alt="Soy Nuevo"
                                    class="img-fluid rounded mb-2"
                                    style="max-width: 260px; max-height:200px; object-fit:cover;">
                            </div>

                            <div class="hint-bubble mx-auto mt-1 hint-bubble-secondary">
                                <div class="arrow-up-animated mb-1">
                                    <i class="bi bi-arrow-up-circle-fill"></i>
                                </div>
                                <p class="small mb-0 bg-light border rounded-pill d-inline-block px-3 py-2 shadow-sm">
                                    Soy nuevo aquí: todavía no estoy dado de alta en la web ni como usuario ni como empresa.
                                </p>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

    <x-footer />

@endsection
