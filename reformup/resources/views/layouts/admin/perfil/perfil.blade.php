@extends('layouts.main')
@section('title', 'Mi perfil - ReformUp')

@section('content')

    <x-navbar active="perfil" />
    {{-- Contenedor Principal --}}
    <div class="container my-1">
        <div class="d-grid d-md-inline-block">
            <a href="{{ route('admin.dashboard') }}"
                class="btn btn-secondary d-inline-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-arrow-left"></i>
                <span>Volver al panel principal</span>
            </a>
        </div>
        <div class="row g-0 shadow rounded overflow-hidden">
            <div class="col-lg-12 bg-white">
                <div class="p-1 p-lg-3">

                    <h1 class="h4 mb-1 d-flex align-items-center gap-2">
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

                        {{-- NAV TABS --}}
                        <ul class="nav nav-tabs" id="perfilTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab-usuario-tab" data-bs-toggle="tab"
                                    data-bs-target="#tab-usuario" type="button" role="tab" aria-controls="tab-usuario"
                                    aria-selected="true">
                                    Datos de usuario
                                </button>
                            </li>

                            @if ($roles->contains('profesional'))
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-profesional-tab" data-bs-toggle="tab"
                                        data-bs-target="#tab-profesional" type="button" role="tab"
                                        aria-controls="tab-profesional" aria-selected="false">
                                        Datos de profesional
                                    </button>
                                </li>
                            @endif
                        </ul>

                        {{-- CONTENIDO TABS --}}
                        <div class="tab-content mt-3" id="perfilTabsContent">
                            {{-- TAB USUARIO --}}
                            <div class="tab-pane fade show active" id="tab-usuario" role="tabpanel"
                                aria-labelledby="tab-usuario-tab">
                                <x-admin.perfil.usuario :usuario="$usuario" />
                            </div>

                            {{-- TAB PROFESIONAL --}}
                            @if ($roles->contains('profesional'))
                                <div class="tab-pane fade" id="tab-profesional" role="tabpanel"
                                    aria-labelledby="tab-profesional-tab">
                                    <x-admin.perfil.profesional :perfil-profesional="$perfilProfesional" :oficios="$oficios" :oficios-seleccionados="$oficiosSeleccionados" />
                                </div>
                            @endif
                        </div>

                        {{-- Sólo dejamos tocar roles si el usuario tiene rol admin --}}
                        @if ($roles->contains('admin'))
                            <hr class="my-4">
                            <h5>Roles del usuario</h5>

                            @foreach ($allRoles as $roleName)
                                @continue($roleName === 'usuario')

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
