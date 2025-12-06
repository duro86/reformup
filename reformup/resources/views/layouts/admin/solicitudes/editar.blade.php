@extends('layouts.main')

@section('title', 'Editar solicitud - Admin - ReformUp')

@section('content')
    <x-navbar />
    <x-admin.admin_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        <x-admin.nav_movil active="solicitudes" />

        <div class="container py-4">

            <h1 class="h4 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-text"></i>
                Editar solicitud #{{ $solicitud->id }}
            </h1>

            <x-alertas.alertasFlash />

            @php
                $cliente = $solicitud->cliente;
                $perfilPro = $solicitud->profesional;
            @endphp

            {{-- Resumen cabecera --}}
            <div class="card mb-3">
                <div class="card-body small text-muted">
                    <div class="mb-1">
                        <strong>Estado actual:</strong>
                        {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                    </div>
                    <div class="mb-1">
                        <strong>Cliente:</strong>
                        @if ($cliente)
                            {{ $cliente->nombre ?? $cliente->name }}
                            {{ $cliente->apellidos ?? '' }}
                            ({{ $cliente->email }})
                        @else
                            <span class="text-muted">Sin cliente</span>
                        @endif
                    </div>
                    <div class="mb-1">
                        <strong>Profesional asignado:</strong>
                        @if ($perfilPro)
                            {{ $perfilPro->empresa }}
                            @if ($perfilPro->email_empresa)
                                ({{ $perfilPro->email_empresa }})
                            @endif
                        @else
                            <span class="text-muted">Sin profesional asignado</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Formulario edición --}}
            <form action="{{ route('admin.solicitudes.actualizar', $solicitud) }}" method="POST" class="card p-3">
                @csrf
                @method('PUT')

                {{-- Titulo --}}
                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <textarea style="resize: none;" name="titulo" rows="2" class="form-control @error('titulo') is-invalid @enderror"
                        required>{{ old('titulo', $solicitud->titulo) }}</textarea>
                    @error('titulo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Descripción --}}
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="4"
                        class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $solicitud->descripcion) }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ciudad --}}
                <div class="row">
                    {{-- Ciudad / Provincia --}}
                    <div class="row">
                        {{-- Provincia --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Provincia<span class="text-danger">*</span></label>
                            <select name="provincia" id="provincia"
                                class="form-control @error('provincia') is-invalid @enderror">
                                <option value="">Selecciona una provincia</option>
                                <option value="Huelva"
                                    {{ old('provincia', $solicitud->provincia) == 'Huelva' ? 'selected' : '' }}>Huelva
                                </option>
                                <option value="Sevilla"
                                    {{ old('provincia', $solicitud->provincia) == 'Sevilla' ? 'selected' : '' }}>Sevilla
                                </option>
                            </select>
                            @error('provincia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Ciudad / Municipio --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Municipio</label>
                            <select name="ciudad" id="ciudad"
                                class="form-control @error('ciudad') is-invalid @enderror">
                                <option value="">Selecciona primero una provincia</option>
                                {{-- Opciones por JS --}}
                            </select>
                            @error('ciudad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Presupuesto maximo --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Presupuesto máximo (opcional)</label>
                            <input type="number" step="0.01" name="presupuesto_max"
                                class="form-control @error('presupuesto_max') is-invalid @enderror"
                                value="{{ old('presupuesto_max', $solicitud->presupuesto_max) }}">
                            @error('presupuesto_max')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Estado --}}
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select @error('estado') is-invalid @enderror">
                            @foreach ($estados as $valor => $texto)
                                <option value="{{ $valor }}" @selected(old('estado', $solicitud->estado) === $valor)>
                                    {{ $texto }}
                                </option>
                            @endforeach
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Ten en cuenta que al pasar a <strong>cancelada</strong> también se actualizarán
                            el presupuesto y el trabajo asociados.
                        </div>
                        <div class="form-text">
                            Ten en cuenta que al pasar a <strong>cerrada</strong>, si existe, se actualizará
                            el presupuesto a aceptado.
                        </div>
                    </div>

                    {{-- BTN volver listado --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                        <a href="{{ route('admin.solicitudes') }}" class="btn btn-outline-secondary">
                            Volver al listado
                        </a>

                        <div class="d-flex flex-column flex-md-row gap-2">
                            <button type="submit" class="btn btn-primary">
                                Guardar cambios
                            </button>
                        </div>
                    </div>
            </form>
            {{-- Form cancelar, separado, sin anidamiento --}}
            {{-- Botón cancelar solicitud (separado, sin anidar formularios) --}}
            @if ($solicitud->estado !== 'cancelada')
                <x-admin.solicitudes.btn_cancelar :solicitud="$solicitud" />
            @endif
            {{-- CKEditor para este campo --}}
            <x-ckeditor.ckeditor_descripcion for="descripcion" />
        </div>
    </div>
<x-ciudadProvincia.ciudades_provincias :oldProvincia="old('provincia', $solicitud->provincia)" :oldCiudad="old('ciudad', $solicitud->ciudad)" />
@endsection

<x-alertas_sweet />
