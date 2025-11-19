@extends('layouts.main')

@section('title', 'Editar comentario - ReformUp')

@section('content')
    <x-navbar />
    <x-usuario.usuario_sidebar />
    <x-usuario.user_bienvenido />

    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">

            <h1 class="h4 mb-3">
                Editar comentario #{{ $comentario->id }}
            </h1>

            @php
                $trabajo = $comentario->trabajo;
                $presu = $trabajo?->presupuesto;
                $solicitud = $presu?->solicitud;
            @endphp

            <div class="mb-3">
                <div class="small text-muted">
                    Trabajo:
                    @if ($solicitud?->titulo)
                        <strong>{{ $solicitud->titulo }}</strong>
                    @else
                        <strong>Trabajo #{{ $trabajo?->id }}</strong>
                    @endif
                </div>
                <div class="small text-muted">
                    Estado actual del comentario:
                    <strong>{{ ucfirst($comentario->estado) }}</strong>
                </div>
            </div>

            <form action="{{ route('usuario.comentarios.actualizar', $comentario) }}" method="POST" class="card p-3">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Puntuación</label>
                    <select name="puntuacion" class="form-select @error('puntuacion') is-invalid @enderror" required>
                        <option value="">Elige una puntuación</option>
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" @selected(old('puntuacion', $comentario->puntuacion) == $i)>
                                {{ $i }} {{ Str::plural('estrella', $i) }}
                            </option>
                        @endfor
                    </select>
                    @error('puntuacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Opinión (opcional)</label>
                    <textarea name="opinion" rows="4" class="form-control @error('opinion') is-invalid @enderror">{{ old('opinion', $comentario->opinion) }}</textarea>
                    @error('opinion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info small">
                    Al guardar los cambios, tu comentario volverá a estado
                    <strong>pendiente</strong> hasta que el administrador lo revise.
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('usuario.comentarios.index') }}" class="btn btn-outline-secondary">
                        Volver a mis comentarios
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Guardar cambios
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection

<x-alertas_sweet />
