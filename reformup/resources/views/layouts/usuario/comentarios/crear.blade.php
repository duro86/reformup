@extends('layouts.main')

@section('title', 'Valorar trabajo - ReformUp')

@section('content')
    <x-navbar />
    <x-usuario.usuario_sidebar />
    <x-usuario.user_bienvenido />

    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">

            <h1 class="h4 mb-3">
                Valorar trabajo #{{ $trabajo->id }}
                @if ($trabajo->presupuesto?->solicitud?->titulo)
                    - {{ $trabajo->presupuesto->solicitud->titulo }}
                @endif
            </h1>

            <form action="{{ route('usuario.comentarios.guardar', $trabajo) }}" method="POST" class="card p-3">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Puntuación</label>
                    <select name="puntuacion" class="form-select @error('puntuacion') is-invalid @enderror" required>
                        <option value="">Elige una puntuación</option>
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" @selected(old('puntuacion') == $i)>
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
                    <textarea style="resize: none;" name="opinion" rows="5" class="form-control @error('opinion') is-invalid @enderror"
                        placeholder="Cuenta brevemente cómo ha sido tu experiencia con este profesional...">{{ old('opinion') }}</textarea>
                    @error('opinion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('usuario.trabajos.index') }}" class="btn btn-outline-secondary">
                        Volver a mis trabajos
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Enviar valoración
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection

<x-alertas_sweet />
