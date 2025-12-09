@extends('layouts.main')
@section('title', 'Mi perfil - ReformUp')

@section('content')

    {{-- Navbar común --}}
    <x-navbar active="perfil"/>
    <x-profesional.profesional_bienvenido />
    {{-- NAV SUPERIOR SOLO MÓVIL/TABLET --}}
    <x-usuario.nav_movil active="perfil" />

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="container my-1">
        <div class="d-flex flex-column flex-md-row align-items-center gap-1">
            {{-- Botón a la izquierda --}}
            <a href="{{ route('profesional.dashboard') }}"
                class="btn btn-secondary d-inline-flex align-items-center justify-content-center gap-2 me-md-auto">
                <i class="bi bi-arrow-left"></i>
                <span>Volver al panel principal</span>
            </a>

            {{-- Título centrado --}}
            <h3 class="mb-0 text-center flex-grow-1">
                Mi perfil Profesional
            </h3>
        </div>


        <div class="row justify-content-center">
            <div class="col-lg-12 bg-white">
                <div class="p-lg-5">

                    {{-- Errores globales --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            Revisa los campos marcados en rojo.
                        </div>
                    @endif

                    {{-- FORMULARIO actualizar perfil PROFESIONAL --}}
                    <form method="POST" action="{{ route('profesional.perfil.actualizar') }}" enctype="multipart/form-data"
                        novalidate>
                        @csrf
                        @method('PUT')

                        {{-- Bloque de datos de profesional --}}
                        <x-profesional.form_perfil_profesional :perfil_profesional="$perfil_profesional" :oficios="$oficios" :oficiosSeleccionados="$oficiosSeleccionados ?? []" />

                        {{-- Botones --}}
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                Guardar cambios
                            </button>
                            <a href="{{ route('profesional.dashboard') }}" class="btn btn-outline-secondary">
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
