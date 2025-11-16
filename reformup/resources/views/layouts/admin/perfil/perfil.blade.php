@extends('layouts.main')
@section('title', 'Mi perfil - ReformUp')

@section('content')

    <x-navbar />

    <div class="container my-5">
        <div class="row g-0 shadow rounded overflow-hidden">
            <div class="col-lg-12 bg-white">
                <div class="p-4 p-lg-5">

                    <h1 class="h4 mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-person-gear me-1"></i>
                        Mi perfil
                    </h1>

                    {{-- Roles del usuario --}}
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
                    
                    {{-- Actualizar perfil --}}
                    <form method="POST"
                          action="{{ route('admin.perfil.actualizar') }}"
                          enctype="multipart/form-data"
                          novalidate>
                        @csrf
                        @method('PUT')

                        {{-- Bloque usuario --}}
                        <x-admin.perfil.usuario :usuario="$usuario" />

                        {{-- Bloque profesional (solo si tiene rol profesional) --}}
                        @if ($roles->contains('profesional'))
                            <hr class="my-4">
                            <x-admin.perfil.profesional
                                :perfil-profesional="$perfilProfesional"
                                :oficios="$oficios"
                                :oficios-seleccionados="$oficiosSeleccionados"
                            />
                        @endif

                        {{-- Botones --}}
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                Guardar cambios
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
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
