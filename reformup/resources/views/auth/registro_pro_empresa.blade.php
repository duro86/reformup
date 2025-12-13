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
                <div class="col-lg-7 bg-white shadow rounded p-2">
                    <div class="text-center mb-1">
                        <i class="bi bi-briefcase text-primary" style="font-size: 2rem;"></i>
                        <h4 class="mt-2">Registro de Empresa</h4>
                        <p class="text-muted">Paso 2 de 2: Información de tu empresa</p>
                    </div>

                    {{-- Errores globales --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            Revisa los campos marcados en rojo.
                        </div>
                    @endif

                    {{-- Fila 1: Empresa + CIF --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre de la empresa <span class="text-danger">*</span></label>
                            <input type="text" name="empresa" value="{{ old('empresa') }}"
                                class="form-control @error('empresa') is-invalid @enderror" required>
                            @error('empresa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email de la empresa <span class="text-danger">*</span></label>
                            <input type="email" name="email_empresa" value="{{ old('email_empresa') }}"
                                class="form-control @error('email_empresa') is-invalid @enderror" required>

                            @error('email_empresa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    {{-- Fila 2: Web + Teléfono --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CIF <span class="text-danger">*</span></label>
                            <input type="text" name="cif" value="{{ old('cif') }}"
                                class="form-control @error('cif') is-invalid @enderror" required>
                            @error('cif')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>



                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono empresa <span class="text-danger">*</span></label>
                            <input type="text" name="telefono_empresa" value="{{ old('telefono_empresa') }}"
                                class="form-control @error('telefono_empresa') is-invalid @enderror" required>
                            @error('telefono_empresa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Fila 3: Provincia + Municipio --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Provincia <span class="text-danger">*</span></label>
                            <select name="provincia" id="provincia"
                                class="form-control @error('provincia') is-invalid @enderror">
                                <option value="">Selecciona una provincia</option>
                                <option value="Huelva" {{ old('provincia') == 'Huelva' ? 'selected' : '' }}>Huelva</option>
                                <option value="Sevilla" {{ old('provincia') == 'Sevilla' ? 'selected' : '' }}>Sevilla
                                </option>
                            </select>
                            @error('provincia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Municipio</label>
                            <select name="ciudad" id="ciudad"
                                class="form-control @error('ciudad') is-invalid @enderror">
                                <option value="">Selecciona primero una provincia</option>
                            </select>
                            @error('ciudad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Fila 4: Dirección --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion_empresa" value="{{ old('direccion_empresa') }}"
                                class="form-control @error('direccion_empresa') is-invalid @enderror">
                            @error('direccion_empresa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sitio web</label>
                            <input type="url" name="web" value="{{ old('web') }}"
                                class="form-control @error('web') is-invalid @enderror">
                            @error('web')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Fila 5: Bio --}}
                    <div class="mb-3">
                        <label class="form-label">Descripción / Bio</label>
                        <textarea name="bio" rows="4" class="form-control @error('bio') is-invalid @enderror" style="resize: none;">{{ old('bio') }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Fila 6: Avatar --}}
                    <div class="mb-3">
                        <label class="form-label">Avatar (imagen)</label>
                        <input type="file" name="avatar_empresa" accept="image/*"
                            class="form-control @error('avatar_empresa') is-invalid @enderror">
                        @error('avatar_empresa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
    <x-ciudadProvincia.ciudades_provincias :oldProvincia="old('provincia')" :oldCiudad="old('ciudad')" />
    {{-- Footer  --}}
    <x-footer />

@endsection

{{-- Alerta de éxito con SweetAlert2 --}}
<x-alertas_sweet />
