@extends('layouts.main')

@section('title', 'Conflicto de estado')

@section('content')
    <x-navbar />

    <div class="container py-5">
        <div class="alert alert-warning">
            <h4 class="alert-heading">Error 409 · Conflicto de estado</h4>

            <p>
                Tu usuario tiene rol profesional, pero todavía no dispone de un perfil profesional completo.
                Para continuar, debes completar antes los datos de tu empresa.
            </p>

            <div class="mt-3 d-flex flex-wrap gap-2">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    Volver atrás
                </a>

                <a href="{{ route('usuario.dashboard') }}" class="btn btn-primary">
                    Ir a mi panel
                </a>
            </div>
        </div>
    </div>
@endsection
