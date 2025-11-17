@extends('layouts.main')
@section('title', 'Mi perfil - ReformUp')

@section('content')

    <x-navbar />

    <div class="container my-5">
        <div class="d-grid d-md-inline-block">
            <a href="{{ route('admin.dashboard') }}"
                class="btn btn-secondary d-inline-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-arrow-left"></i>
                <span>Volver al panel principal</span>
            </a>
        </div>
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
                    <form method="POST" action="{{ route('admin.perfil.actualizar') }}" enctype="multipart/form-data"
                        novalidate>
                        @csrf
                        @method('PUT')

                        {{-- Bloque usuario --}}
                        <x-admin.perfil.usuario :usuario="$usuario" />

                        {{-- Bloque profesional (solo si tiene rol profesional) --}}
                        @if ($roles->contains('profesional'))
                            <hr class="my-4">
                            <x-admin.perfil.profesional :perfil-profesional="$perfilProfesional" :oficios="$oficios" :oficios-seleccionados="$oficiosSeleccionados" />
                        @endif

                        {{-- Sólo dejamos tocar roles si el usuario tiene rol admin --}}
                        @if ($roles->contains('admin'))
                            <hr class="my-4">
                            <h5>Roles del usuario</h5>

                            @php
                                // $allRoles y $currentRoles vienen del controlador
                            @endphp

                            @foreach ($allRoles as $roleName)
                                @php
                                    $isChecked = in_array($roleName, old('roles', $currentRoles));
                                @endphp

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input rol-input" type="checkbox" name="roles[]"
                                        id="role_{{ $roleName }}" value="{{ $roleName }}"
                                        {{ $isChecked ? 'checked' : '' }}
                                        @if ($roleName === 'profesional' && $perfilProfesional) data-tiene-perfil-profesional="1" @endif>
                                    <label class="form-check-label" for="role_{{ $roleName }}">
                                        {{ ucfirst($roleName) }}
                                    </label>
                                </div>
                            @endforeach

                            @error('roles')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
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

{{-- Alertas Roles JS personalizados --}}
<x-admin.roles.roles_alertas />

<x-alertas_sweet />
