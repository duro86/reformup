@extends('layouts.main')

@section('title', 'Editar oficio - Admin - ReformUp')

@section('content')
    {{-- Navbar principal --}}
    <x-navbar />

    {{-- Sidebar admin (escritorio) --}}
    <x-admin.admin_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        {{-- NAV MÓVIL ADMIN --}}
        <x-admin.nav_movil active="oficios" />

        <div class="container py-4">
            {{-- Migas de pan / encabezado --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1">Editar oficio</h1>
                    <p class="text-muted mb-0">
                        Modifica el nombre y la descripción del oficio seleccionado.
                    </p>
                </div>

                <a href="{{ route('admin.oficios') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver al listado
                </a>
            </div>

            {{-- Tarjeta de formulario --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('admin.oficios.actualizar', $oficio) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Nombre --}}
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del oficio</label>
                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                class="form-control @error('nombre') is-invalid @enderror"
                                value="{{ old('nombre', $oficio->nombre) }}"
                                maxlength="100"
                                required
                            >
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Ejemplo: Fontanero, Electricista, Albañil…
                            </div>
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción (opcional)</label>
                            <textarea
                                id="descripcion"
                                name="descripcion"
                                rows="4"
                                class="form-control @error('descripcion') is-invalid @enderror"
                                maxlength="500"
                            >{{ old('descripcion', $oficio->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Breve resumen del tipo de trabajos que incluye este oficio.
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save"></i> Guardar cambios
                            </button>

                            <a href="{{ route('admin.oficios') }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
