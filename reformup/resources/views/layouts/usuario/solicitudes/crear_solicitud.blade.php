@extends('layouts.main')

@section('title', 'Nueva solicitud - ReformUp')

@section('content')

    <x-navbar />

    <x-usuario.usuario_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-plus"></i> Nueva solicitud
                </h1>

                <a href="{{ route('usuario.solicitudes.index') }}"
                   class="btn btn-secondary d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-arrow-left"></i> Volver a mis solicitudes
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    Revisa los campos marcados en rojo.
                </div>
            @endif

            <div class="row g-0 shadow rounded bg-white">
                <div class="col-12 p-4 p-lg-5">

                    <form method="POST" action="{{ route('usuario.solicitudes.guardar') }}">
                        @csrf

                        {{-- Profesional elegido --}}
                        <input type="hidden" name="pro_id" value="{{ $profesional->id }}">

                        {{-- Título --}}
                        <div class="mb-3">
                            <label class="form-label">Título de la solicitud<span class="text-danger">*</span></label>
                            <input type="text"
                                   name="titulo"
                                   value="{{ old('titulo') }}"
                                   class="form-control @error('titulo') is-invalid @enderror"
                                   placeholder="Reforma de baño, cambio de suelo, pintura del piso...">
                            @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-3">
                            <label class="form-label">Descripción detallada<span class="text-danger">*</span></label>
                            <textarea name="descripcion"
                                      rows="4"
                                      style="resize:none;"
                                      class="form-control @error('descripcion') is-invalid @enderror"
                                      placeholder="Describe qué necesitas, metros aproximados, estado actual, etc.">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Ciudad / Provincia --}}
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ciudad<span class="text-danger">*</span></label>
                                <input type="text"
                                       name="ciudad"
                                       value="{{ old('ciudad') }}"
                                       class="form-control @error('ciudad') is-invalid @enderror">
                                @error('ciudad')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Provincia</label>
                                <input type="text"
                                       name="provincia"
                                       value="{{ old('provincia') }}"
                                       class="form-control @error('provincia') is-invalid @enderror">
                                @error('provincia')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Dirección (opcional)</label>
                                <input type="text"
                                       name="dir_cliente"
                                       value="{{ old('dir_cliente') }}"
                                       class="form-control @error('dir_cliente') is-invalid @enderror">
                                @error('dir_cliente')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Presupuesto máximo --}}
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Presupuesto máximo (opcional)</label>
                            <div class="input-group">
                                <input type="number"
                                       step="0.01"
                                       min="0"
                                       name="presupuesto_max"
                                       value="{{ old('presupuesto_max') }}"
                                       class="form-control @error('presupuesto_max') is-invalid @enderror">
                                <span class="input-group-text">€</span>
                                @error('presupuesto_max')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">
                                Si no lo indicas, el profesional te propondrá un rango según el trabajo.
                            </small>
                        </div>

                        <div class="mt-4 d-flex flex-column flex-md-row gap-2">
                            <button type="submit" class="btn btn-primary">
                                Enviar solicitud
                            </button>
                            <a href="{{ route('usuario.solicitudes.index') }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
<x-alertas_sweet />
