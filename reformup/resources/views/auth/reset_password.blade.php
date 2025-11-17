@extends('layouts.main')
@section('title', 'Nueva contraseña - ReformUp')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h4 mb-3">Establecer nueva contraseña</h2>

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <label class="form-label" for="email">Correo electrónico</label>
                            <input type="email" id="email" name="email"
                                   value="{{ old('email', $email) }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password">Nueva contraseña</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password_confirmation">Repite la contraseña</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('login') }}" class="btn btn-link">
                                Volver al login
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Guardar contraseña
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<x-alertas_sweet />
@endsection
