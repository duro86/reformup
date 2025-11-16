@extends('layouts.main')
@section('title', 'Mi perfil - ReformUp')

@section('content')

    {{-- Navbar común --}}
    <x-navbar />

    {{-- CONTENIDO PRINCIPAL (desplazado a la derecha del sidebar) --}}
    <div class="container my-2">
        <a href="{{ route('usuario.dashboard') }}" class="w-25 btn btn-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Volver al panel principal
        </a>

        <div class="row justify-content-center">
            <div class="col-lg-12 bg-white">
                <div class="p-4 p-lg-5">

                    {{-- Título --}}
                    <h1 class="h4 mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-person-bounding-box me-1"></i>
                        Mi perfil
                    </h1>

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
