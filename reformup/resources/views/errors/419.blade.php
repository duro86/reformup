@extends('layouts.main')

@section('title', 'Sesión caducada')

@section('content')
    <x-navbar />

    <div class="container py-5">
        <div class="alert alert-warning">
            <h4 class="alert-heading">Tu sesión ha caducado</h4>
            <p>Has estado un tiempo sin actividad y el formulario ha caducado.</p>
            <a href="{{ route('login') }}" class="btn btn-primary mt-3">
                Volver a iniciar sesión
            </a>
        </div>
    </div>
@endsection
