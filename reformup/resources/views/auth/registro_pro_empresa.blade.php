@extends('layouts.main')
@section('title', 'Registro Empresa - ReformUp')

@section('content')

    {{-- Nav importado  --}}
    <x-navbar />

    {{-- Contenedor principal --}}
    <div class="container my-1">
        {{-- Formulario registro Pro nuevo --}}
        <form method="POST" action="{{ route('registrar.empresa') }}" enctype="multipart/form-data" novalidate>
            @csrf
            <input type="hidden" name="user_id" value="{{ $userId ?? old('user_id') }}">

            <div class="row justify-content-center align-items-start">

                <!-- Columna izquierda: formulario campos normales -->
                <div class="col-lg-7 bg-white shadow rounded p-4">
                    <div class="text-center mb-1">
                        <i class="bi bi-briefcase text-primary" style="font-size: 3rem;"></i>
                        <h1 class="h4 mt-2">Registro de Empresa</h1>
                        <p class="text-muted">Paso 2 de 2: Información de tu empresa</p>
                    </div>

                    {{-- Errores globales --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            Revisa los campos marcados en rojo.
                        </div>
                    @endif

                    {{-- Campos normales --}}
                    {{-- Nombre --}}
                    <div class="mb-3">
                        <label class="form-label" for="empresa">Nombre de la empresa <span
                                class="text-danger">*</span></label>
                        <input type="text" id="empresa" name="empresa" value="{{ old('empresa') }}"
                            class="form-control @error('empresa') is-invalid @enderror" required>
                        @error('empresa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- CIF --}}
                    <div class="mb-3">
                        <label class="form-label" for="cif">CIF <span class="text-danger">*</span></label>
                        <input type="text" id="cif" name="cif" value="{{ old('cif') }}"
                            class="form-control @error('cif') is-invalid @enderror" required>
                        @error('cif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email de la empresa --}}
                    <div class="mb-3">
                        <label class="form-label" for="email_empresa">Email de la empresa <span
                                class="text-danger">*</span></label>
                        <input type="email" id="email_empresa" name="email_empresa" value="{{ old('email_empresa') }}"
                            class="form-control @error('email_empresa') is-invalid @enderror" required>
                        @error('email_empresa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Bio --}}
                    <div class="mb-3">
                        <label class="form-label" for="bio">Descripción / Bio</label>
                        <textarea id="bio" name="bio" class="form-control @error('bio') is-invalid @enderror" rows="3"
                            style="resize: none;">{{ old('bio') }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Sitio web --}}
                    <div class="mb-3">
                        <label class="form-label" for="web">Sitio web</label>
                        <input type="url" id="web" name="web" value="{{ old('web') }}"
                            class="form-control @error('web') is-invalid @enderror">
                        <small class="form-text text-muted">Ejemplo: https://www.ejemplo.com</small>
                        @error('web')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Teléfono de la empresa --}}
                    <div class="mb-3">
                        <label class="form-label" for="telefono_empresa">Teléfono de la empresa <span
                                class="text-danger">*</span></label>
                        <input type="text" id="telefono_empresa" name="telefono_empresa"
                            value="{{ old('telefono_empresa') }}"
                            class="form-control @error('telefono_empresa') is-invalid @enderror" required>
                        @error('telefono_empresa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Ciudad --}}
                    <div class="mb-3">
                        <label class="form-label" for="ciudad_empresa">Ciudad</label>
                        <input type="text" id="ciudad_empresa" name="ciudad_empresa" value="{{ old('ciudad_empresa') }}"
                            class="form-control @error('ciudad_empresa') is-invalid @enderror">
                        @error('ciudad_empresa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Provincia --}}
                    <div class="mb-3">
                        <label class="form-label" for="provincia_empresa">Provincia</label>
                        <input type="text" id="provincia_empresa" name="provincia_empresa"
                            value="{{ old('provincia_empresa') }}"
                            class="form-control @error('provincia_empresa') is-invalid @enderror">
                        @error('provincia_empresa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Dirección --}}
                    <div class="mb-3">
                        <label class="form-label" for="direccion_empresa">Dirección</label>
                        <input type="text" id="direccion_empresa" name="direccion_empresa"
                            value="{{ old('direccion_empresa') }}"
                            class="form-control @error('direccion_empresa') is-invalid @enderror"
                            placeholder="Avd/Cabezo de la Joya 3, escalera 3, 4ºA">
                        @error('direccion_empresa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Avatar --}}
                    <div class="mb-3">
                        <label class="form-label" for="avatar_empresa">Avatar (imagen)</label>
                        <input type="file" id="avatar_empresa" name="avatar_empresa" accept="image/*"
                            class="form-control @error('avatar_empresa') is-invalid @enderror">
                        @error('avatar_empresa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Formatos recomendados: JPG, PNG, WEBP. Tamaño máximo según
                            configuración del servidor.</small>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Guardar Empresa</button>
                    </div>
                </div>

                <!-- Columna derecha: oficios -->
                <div class="col-lg-4 rounded p-4 ms-4" style="max-height: 500px; overflow-y: auto;">
                    <div class="card shadow rounded p-3">
                        <h5 class="card-title mb-3">Selecciona tus Oficios (mínimo 1) <span class="text-danger">*</span>
                        </h5>
                        {{-- Oficios --}}
                        @foreach ($oficios as $oficio)
                            <div class="form-check mb-2">
                                <input class="form-check-input @error('oficios') is-invalid @enderror" type="checkbox"
                                    name="oficios[]" value="{{ $oficio->id }}" id="oficio{{ $oficio->id }}"
                                    {{ is_array(old('oficios')) && in_array($oficio->id, old('oficios')) ? 'checked' : '' }}>
                                <label class="form-check-label" for="oficio{{ $oficio->id }}">
                                    {{ ucfirst(str_replace('_', ' ', $oficio->nombre)) }}
                                </label>
                            </div>
                        @endforeach
                        @error('oficios')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


            </div>
        </form>
    </div>

    {{-- Prueba muestra id --}}
    @if (isset($userId))
        <div>
            <strong>User ID desde sesión:</strong> {{ $userId }}
        </div>
    @else
        <div>
            No hay User ID disponible
        </div>
    @endif


    <footer class="bg-primary text-center py-3 border-top text-white mt-5">
        <div class="container">
            <small>© {{ date('Y') }} ReformUp. Todos los derechos reservados.</small>
        </div>
    </footer>

@endsection
{{-- Alerta de éxito con SweetAlert2 --}}
<x-alertas_sweet />
