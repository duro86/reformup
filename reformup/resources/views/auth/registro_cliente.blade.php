@extends('layouts.main')
@section('title', 'Registro Cliente - ReformUp')

@section('content')

    {{-- NAVBAR simple --}}
    <x-navbar />

    <div class="container my-5">

        {{-- Registro de cliente --}}
        <div class="row g-0 shadow rounded overflow-hidden">

            {{-- Panel izquierdo decorativo --}}
            <div class="col-lg-5 d-none d-lg-block" style="background:#E9F5DB;">
                <div class="h-100 p-5">
                    <h2 class="mb-3 text-success">Únete a ReformUp</h2>
                    <p class="text-muted">Encuentra profesionales verificados y gestiona presupuestos desde un único lugar.
                    </p>
                    <ul class="text-muted small">
                        <li>Regístrate en 1 minuto</li>
                        <li>Solicita presupuestos sin compromiso</li>
                        <li>Valora y guarda tus favoritos</li>
                    </ul>

                    {{-- Imagen centrada y responsive justo debajo de la lista --}}
                    <div class="text-center mt-3">
                        <img src="{{ asset('img/User/panel_registro/panel_registro_user.png') }}" alt="Reformas" class="img-fluid rounded mx-auto d-block" style="max-width:85%; height:auto;">
                    </div>                    
                </div>
            </div>

            {{-- Formulario --}}
            <div class="col-lg-7 bg-white">
                <div class="p-4 p-lg-5">
                    <h1 class="h4 mb-4"><i class="bi-person-bounding-box me-2"></i> Crear cuenta <span
                            class="text-primary">(Cliente)</span></h1>

                    {{-- Errores globales --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            Revisa los campos marcados en rojo.
                        </div>
                    @endif

                    {{-- Formulario de registro --}}
                    <form method="POST" action="{{ route('registrar.cliente') }}" novalidate enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre<span class="text-danger">*</span></label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}"
                                    class="form-control @error('nombre') is-invalid @enderror">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Apellidos<span class="text-danger">*</span></label>
                                <input type="text" name="apellidos" value="{{ old('apellidos') }}"
                                    class="form-control @error('apellidos') is-invalid @enderror">
                                @error('apellidos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email<span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contraseña<span class="text-danger">*</span></label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Repite contraseña<span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono<span class="text-danger">*</label>
                                <input type="text" placeholder="612345678" name="telefono" value="{{ old('telefono') }}"
                                    class="form-control @error('telefono') is-invalid @enderror">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" value="{{ old('ciudad') }}"
                                    class="form-control @error('ciudad') is-invalid @enderror">
                                @error('ciudad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Provincia <span class="text-danger"></span></label>
                                <input type="text" name="provincia" value="{{ old('provincia') }}"
                                    class="form-control @error('provincia') is-invalid @enderror">
                                @error('provincia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Código postal <span class="text-danger"></span></label>
                                <input type="text" name="cp" placeholder="21004" value="{{ old('cp') }}"
                                    class="form-control @error('cp') is-invalid @enderror" maxlength="5">
                                @error('cp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion" placeholder="Avd/Cabezo de la Joya 3, escalera 3, 4ºA" value="{{ old('direccion') }}"
                                class="form-control @error('direccion') is-invalid @enderror">
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Avatar (imagen)</label>
                            <input type="file" name="avatar" accept="image/*"
                                class="form-control @error('avatar') is-invalid @enderror">
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Formato recomendado: JPG/PNG/WEBP. Tamaño máximo según
                                configuración del servidor.</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Crear cuenta</button>
                            <a href="#" class="btn btn-outline-secondary">Ya tengo cuenta</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer básico --}}
    <footer class="bg-primary text-center py-3 border-top text-white">
        <div class="container">
            <small>© {{ date('Y') }} ReformUp. Todos los derechos reservados.</small>
        </div>
    </footer>

@endsection


