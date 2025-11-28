@extends('layouts.main')
@section('title', 'Registro Profesional - ReformUp')

@section('content')

    <x-navbar />

    {{-- Registro de Profesionales --}}
    <div class="container my-5">

        {{-- Panel Izquierdo --}}
        <div class="row g-0 shadow rounded overflow-hidden">
            <div class="col-lg-5 d-none d-lg-block" style="background:#E9F5DB;">
                <div class="h-60 p-5">
                    <h2 class="mb-3 text-success">Únete como Profesional</h2>
                    <p class="text-muted">Promociona tu empresa y conecta con clientes fácilmente.</p>
                    <ul class="text-muted small">
                        <li>Registra tu empresa en minutos</li>
                        <li>Gestiona presupuestos y trabajos</li>
                        <li>Amplía tu visibilidad y alcance</li>
                    </ul>

                    <div class="text-center mt-3">
                        <img src="{{ asset('img/Profesional/panel_registro/profesional_panel.jpg') }}" alt="Profesionales"
                            class="img-fluid rounded mx-auto d-block" style="max-width:85%; height:auto;">
                    </div>
                </div>
            </div>

            {{-- Panel Derecho --}}
            <div class="col-lg-7 bg-white">
                <div class="p-3 p-lg-5">

                    <h1 class="h4 mb-1"><i class="bi-building me-2"></i> Crear cuenta <span
                            class="text-primary">(Profesional)</span></h1>

                    {{-- Registro de Profesionales Opciones --}}
                    <div class="row mb-5 mt-1 g-4">

                        {{-- Soy Usuario --}}
                        <div class="text-center">
                            <a href="{{ route('validar.usuario') }}" class="btn btn-outline-primary mb-3 mb-sm-3 mt-sm-3">
                                <i class="bi bi-person-circle me-2"></i> Soy Usuario
                            </a>

                            <div>
                                <img src="{{ asset('img/Profesional/user_pro/soy_user_pro.jpg') }}" alt="Soy Usuario"
                                    class="img-fluid rounded mb-sm-3" style="max-width: 300px;">
                            </div>

                            {{-- Bocadillo de ayuda --}}
                            <div class="hint-bubble mx-auto mt-2">
                                <div class="arrow-up-animated mb-1">
                                    <i class="bi bi-arrow-up-circle-fill"></i>
                                </div>
                                <p class="small mb-0 bg-light border rounded-pill d-inline-block px-3 py-2 shadow-sm">
                                    Ya estoy registrado en la web y quiero dar de alta mi empresa como profesional.
                                </p>
                            </div>
                        </div>

                        {{-- Soy Nuevo --}}
                        <div class="text-center">
                            <a href="{{ route('registro.pro.form') }}" class="btn btn-outline-primary mb-3 mb-sm-3 mt-sm-3">
                                <i class="bi bi-person-plus me-2"></i> Soy Nuevo
                            </a>

                            <div>
                                <img src="{{ asset('img/Profesional/user_nuevo/soy_nuevo_pro.jpg') }}" alt="Soy Nuevo"
                                    class="img-fluid rounded mb-sm-3" style="max-width: 300px;">
                            </div>

                            {{-- Bocadillo de ayuda --}}
                            <div class="hint-bubble mx-auto mt-2 hint-bubble-secondary">
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

    {{-- Footer  --}}
    <x-footer />

@endsection
