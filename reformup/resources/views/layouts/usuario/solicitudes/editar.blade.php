@extends('layouts.main')

@section('title', 'Editar mi solicitud - ReformUp')

@section('content')

    <x-navbar />
    <x-usuario.usuario_sidebar />
    <x-usuario.user_bienvenido />

    <div class="container-fluid main-content-with-sidebar">
        <x-usuario.nav_movil active="solicitudes" />

        <div class="container py-4">

            <h1 class="h4 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-text"></i>
                Editar solicitud #{{ $solicitud->id }}
            </h1>

            <x-alertas.alertasFlash />

            <form action="{{ route('usuario.solicitudes.actualizar', $solicitud) }}" method="POST" class="card p-3">
                @csrf
                @method('PUT')

                {{-- Título --}}
                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <textarea style="resize: none;" name="titulo" rows="2"
                              class="form-control @error('titulo') is-invalid @enderror"
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

                <div class="row">
                    {{-- Provincia --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Provincia<span class="text-danger">*</span></label>
                        <select name="provincia" id="provincia"
                                class="form-control @error('provincia') is-invalid @enderror">
                            <option value="">Selecciona una provincia</option>
                            <option value="Huelva"  {{ old('provincia', $solicitud->provincia) == 'Huelva' ? 'selected' : '' }}>Huelva</option>
                            <option value="Sevilla" {{ old('provincia', $solicitud->provincia) == 'Sevilla' ? 'selected' : '' }}>Sevilla</option>
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

                    {{-- Presupuesto máximo --}}
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

                <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                    <a href="{{ route('usuario.solicitudes.index') }}" class="btn btn-outline-secondary">
                        Volver a mis solicitudes
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Guardar cambios
                    </button>
                </div>
            </form>

            {{-- CKEditor para descripción --}}
            <x-ckeditor.ckeditor_descripcion for="descripcion" />

        </div>
    </div>

@endsection

<x-ciudadProvincia.ciudades_provincias :oldProvincia="old('provincia', $solicitud->provincia)" :oldCiudad="old('ciudad', $solicitud->ciudad)" />
<x-alertas_sweet />
