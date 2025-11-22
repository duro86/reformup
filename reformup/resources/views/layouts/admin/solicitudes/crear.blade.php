@extends('layouts.main')

@section('title', 'Nueva solicitud (admin) - ReformUp')

@section('content')

    <x-navbar />

    {{-- Sidebar admin si lo tienes --}}
    <x-admin.admin_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-plus"></i> Nueva solicitud (admin)
                </h1>

                <a href="{{ route('admin.solicitudes') }}"
                    class="btn btn-secondary d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-arrow-left"></i> Volver al listado
                </a>
                
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    Revisa los campos marcados en rojo.
                </div>
            @endif

            <div class="row g-0 shadow rounded bg-white">
                <div class="col-12 p-4 p-lg-5">

                    <form method="POST" action="{{ route('admin.solicitudes.guardar') }}">
                        @csrf

                        {{-- CLIENTE --}}
                        <div class="mb-3">
                            <label class="form-label">Cliente<span class="text-danger">*</span></label>
                            <select name="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror">
                                <option value="">Selecciona un cliente...</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}"
                                        {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nombre ?? $cliente->name }}
                                        {{ $cliente->apellidos ?? '' }}
                                        ({{ $cliente->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PROFESIONAL --}}
                        <div class="mb-3">
                            <label class="form-label">Profesional<span class="text-danger">*</span></label>
                            <select name="pro_id" class="form-select @error('pro_id') is-invalid @enderror">
                                <option value="">Selecciona un profesional...</option>
                                @foreach ($profesionales as $pro)
                                    <option value="{{ $pro->id }}" {{ old('pro_id') == $pro->id ? 'selected' : '' }}>
                                        {{ $pro->empresa }}
                                        @if ($pro->user)
                                            - {{ $pro->user->nombre ?? $pro->user->name }}
                                            {{ $pro->user->apellidos ?? '' }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('pro_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Título --}}
                        <div class="mb-3">
                            <label class="form-label">Título de la solicitud<span class="text-danger">*</span></label>
                            <input type="text" name="titulo" value="{{ old('titulo') }}"
                                class="form-control @error('titulo') is-invalid @enderror"
                                placeholder="Reforma de baño, cambio de suelo, pintura del piso...">
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-3">
                            <label class="form-label">Descripción detallada<span class="text-danger">*</span></label>
                            <textarea name="descripcion" rows="4" style="resize:none;"
                                class="form-control @error('descripcion') is-invalid @enderror"
                                placeholder="Describe qué necesita el cliente, metros aproximados, estado actual, etc.">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Ciudad / Provincia / Dirección --}}
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ciudad<span class="text-danger">*</span></label>
                                <input type="text" name="ciudad" value="{{ old('ciudad') }}"
                                    class="form-control @error('ciudad') is-invalid @enderror">
                                @error('ciudad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Provincia</label>
                                <input type="text" name="provincia" value="{{ old('provincia') }}"
                                    class="form-control @error('provincia') is-invalid @enderror">
                                @error('provincia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Dirección (opcional)</label>
                                <input type="text" name="dir_cliente" value="{{ old('dir_cliente') }}"
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
                                <input type="number" step="0.01" min="0" name="presupuesto_max"
                                    value="{{ old('presupuesto_max') }}"
                                    class="form-control @error('presupuesto_max') is-invalid @enderror">
                                <span class="input-group-text">€</span>
                                @error('presupuesto_max')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">
                                Si no se indica, el profesional propondrá un rango según el trabajo.
                            </small>
                        </div>

                        <div class="mt-4 d-flex flex-column flex-md-row gap-2">
                            <button type="submit" class="btn btn-primary">
                                Crear solicitud
                            </button>
                            <a href="{{ route('admin.solicitudes') }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
