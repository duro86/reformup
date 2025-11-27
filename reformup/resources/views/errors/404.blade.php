@extends('layouts.main')

@section('title', 'Página no encontrada')

@section('content')
    <x-navbar />

    <div class="container d-flex flex-column align-items-center py-5 text-center">
        <h1 class="display-4 fw-bold text-danger">404</h1>
        <h2 class="mb-3">Ups… no encontramos lo que buscas</h2>

        <p class="text-muted mb-4">
            Puede que el enlace esté roto, la página ya no exista o se haya movido de sitio.
        </p>

        <a href="{{ url('home') }}" class="btn btn-primary">
            Volver al inicio
        </a>
    </div>
@endsection
