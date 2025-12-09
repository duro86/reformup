@extends('layouts.main')
@section('title', 'Mi perfil - ReformUp')

@section('content')

    {{-- Navbar común --}}
    <x-navbar active="perfil"/>
    <x-usuario.user_bienvenido />
    {{-- NAV SUPERIOR SOLO MÓVIL/TABLET --}}
    <x-usuario.nav_movil active="perfil" />

    {{-- CONTENIDO PRINCIPAL (desplazado a la derecha del sidebar) --}}
    <div class="container my-1">
        <div class="d-flex flex-column flex-md-row align-items-center gap-1">
            {{-- Botón a la izquierda --}}
            <a href="{{ route('usuario.dashboard') }}"
                class="btn btn-secondary d-inline-flex align-items-center justify-content-center gap-2 me-md-auto">
                <i class="bi bi-arrow-left"></i>
                <span>Volver al panel principal</span>
            </a>
        </div>

        {{-- Formulario editar perfil usuario --}}
        <div class="row justify-content-center">
            <div class="col-lg-12 bg-white">
                <div class="p-4 p-lg-5">

                    {{-- Título --}}
                    <h4 class="mb-2 d-flex align-items-center gap-2 fw-semibold">
                        <i class="bi bi-person-bounding-box me-1"></i>
                        Mi perfil
                    </h4>

                    {{-- Roles sólo informativos --}}
                    <div class="mb-3">
                        @foreach ($roles as $role)
                            <span class="badge bg-secondary me-1">
                                {{ ucfirst($role) }}
                            </span>
                        @endforeach
                    </div>

                    {{-- Errores globales --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            Revisa los campos marcados en rojo.
                        </div>
                    @endif

                    {{-- Formulario actualizar perfil de USUARIO --}}
                    <form method="POST" action="{{ route('usuario.perfil.actualizar') }}" enctype="multipart/form-data"
                        novalidate>
                        @csrf
                        @method('PUT')

                        {{-- Bloque de datos de usuario --}}
                        <x-usuario.form_perfil_usuario :usuario="$usuario" />

                        {{-- Botones --}}
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                Guardar cambios
                            </button>
                            <a href="{{ route('usuario.dashboard') }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
    <x-footer />
@endsection
<x-alertas_sweet />