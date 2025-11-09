@extends('layouts.main')
@section('title', 'Validar Usuario - ReformUp')

@section('content')

<x-navbar />

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5 bg-white p-4 rounded shadow">

            <h2 class="mb-4 text-center">Confirma tu usuario</h2>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('validar.usuario.post') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autofocus required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Validar y continuar</button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection
{{-- Alerta de error con SweetAlert --}}
<x-alertas_sweet />
