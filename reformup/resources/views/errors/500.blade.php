@extends('layouts.main')

@section('title', 'Error interno del servidor')

@section('content')
    <x-navbar />

    <div class="container d-flex flex-column align-items-center py-5 text-center">
        <h1 class="display-4 fw-bold text-danger">500</h1>
        <h2 class="mb-3">Ha ocurrido un error inesperado</h2>

        <p class="text-muted mb-4">
            Nuestro servidor ha tenido un problema interno.  
            Si el error persiste, por favor intenta de nuevo más tarde.
        </p>

        <a href="{{ url('home') }}" class="btn btn-outline-primary">
            Volver al inicio
        </a>
    </div>
@endsection
