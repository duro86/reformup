@extends('layouts.main')

@section('title', 'Acceso no autorizado')

@section('content')
    <x-navbar />

    <div class="container py-5">
        <div class="alert alert-danger">
            <h4 class="alert-heading">Error 403 · Acceso prohibido</h4>
            <p>No tienes permisos para acceder a esta página o realizar esta acción.</p>

            <div class="mt-3 d-flex flex-wrap gap-2">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    Volver atrás
                </a>

                <a href="{{ route('home') }}" class="btn btn-primary">
                    Ir al inicio
                </a>
            </div>
        </div>
    </div>
@endsection

