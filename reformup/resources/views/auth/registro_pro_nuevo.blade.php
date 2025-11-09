@extends('layouts.main')
@section('title', 'Registro Usuario - ReformUp')

@section('content')

<x-navbar />

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 bg-white shadow rounded p-5">

            <div class="text-center mb-4">
                <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
                <h1 class="h4 mt-3">Crear cuenta de usuario</h1>
                <p class="text-muted">Paso 1 de 2: Información personal</p>
            </div>

            {{-- Errores globales --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    Revisa los campos marcados en rojo.
                </div>
            @endif

            <form method="POST" action="{{ route('registrar.profesional') }}" novalidate enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="apellidos">Apellidos <span class="text-danger">*</span></label>
                        <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos') }}" class="form-control @error('apellidos') is-invalid @enderror" required>
                        @error('apellidos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="password">Contraseña <span class="text-danger">*</span></label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="password_confirmation">Confirma Contraseña <span class="text-danger">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="telefono">Teléfono <span class="text-danger">*</span></label>
                        <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="612345678" class="form-control @error('telefono') is-invalid @enderror" required>
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="ciudad">Ciudad</label>
                        <input type="text" id="ciudad" name="ciudad" value="{{ old('ciudad') }}" class="form-control @error('ciudad') is-invalid @enderror">
                        @error('ciudad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="provincia">Provincia</label>
                        <input type="text" id="provincia" name="provincia" value="{{ old('provincia') }}" class="form-control @error('provincia') is-invalid @enderror">
                        @error('provincia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="cp">Código Postal</label>
                        <input type="text" id="cp" name="cp" placeholder="21004" maxlength="5" value="{{ old('cp') }}" class="form-control @error('cp') is-invalid @enderror">
                        @error('cp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" placeholder="Avd/Cabezo de la Joya 3, escalera 3, 4ºA" value="{{ old('direccion') }}" class="form-control @error('direccion') is-invalid @enderror">
                    @error('direccion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="avatar">Avatar (imagen)</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" class="form-control @error('avatar') is-invalid @enderror">
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Formatos recomendados: JPG, PNG, WEBP. Tamaño máximo según configuración del servidor.</small>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Siguiente</button>
                </div>
            </form>

        </div>
    </div>

</div>

{{-- Footer  --}}
    <x-footer />

@endsection

