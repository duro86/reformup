@extends('layouts.main')
@section('title', 'Iniciar Sesión - ReformUp')

@section('content')

    {{-- Estructura de dos paneles: formulario a la izquierda (o arriba en móvil) e imagen a la derecha (oculta en móvil) --}}
    <div class="d-flex flex-column flex-md-row align-items-stretch"
        style="height: 100vh; min-height: 100vh; width: 100vw; overflow: hidden;">

        <!-- Panel Izquierdo: Formulario -->
        <div class="d-flex flex-column justify-content-center align-items-center w-100 w-md-50">
            <!-- Logo arriba -->
            <div class="w-100 py-4 text-start ps-4 position-absolute" style="top:0; left:0;">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('img/logo/Reformup_favicon_prueba.png') }}" alt="Logo ReformUp" style="height: 50px;">
                </a>
            </div>
            <!-- Formulario centrado -->
            <div class="bg-white p-4 rounded shadow mt-5 mb-4" style="width: 350px; max-width: 90vw;">
                <h2 class="mb-4 text-center">Iniciar Sesión</h2>
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" autofocus required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Recuérdame</label>
                        </div>

                        <a href="{{ route('password.request') }}" class="small">
                            ¿Has olvidado la contraseña?
                        </a>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                    </div>
                </form>
                <div class="mt-3 text-center text-secondary">
                    ¿No tienes cuenta? <a href="{{ route('registrar.cliente') }}">Regístrate</a>
                </div>
            </div>
        </div>

        <!-- Panel Derecho: Imagen (solo escritorio) -->
        <div class="d-none d-md-block w-50 h-100">
            <img src="{{ asset('img/login/panel_login2.jpg') }}" alt="Imagen Inicio de Sesión" class="login-panel">
        </div>
    </div>
@endsection
{{-- Alerta de error con SweetAlert --}}
<x-alertas_sweet />
