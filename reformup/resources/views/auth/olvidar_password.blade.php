@extends('layouts.main')
@section('title', 'Restablecer contraseña - ReformUp')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h4 mb-3">¿Has olvidado tu contraseña?</h2>
                    <p class="text-muted">
                        Escribe tu correo y te enviaremos un enlace para restablecerla.
                    </p>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="email">Correo electrónico</label>
                            <input type="email" id="email" name="email"
                                   value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('login') }}" class="btn btn-link">
                                Volver al login
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Enviar enlace
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
