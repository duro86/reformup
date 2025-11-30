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

            <form action="{{ route('usuario.comentarios.guardar', $trabajo) }}" method="POST" enctype="multipart/form-data"
                class="card p-3">
                @csrf

                {{-- PUNTUACIÓN --}}
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

                {{-- OPINIÓN (CKEDITOR) --}}
                <div class="mb-3">
                    <label class="form-label">Opinión (opcional)</label>
                    <textarea id="opinion" name="opinion" rows="5" class="form-control @error('opinion') is-invalid @enderror">{{ old('opinion') }}</textarea>
                    @error('opinion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <x-ckeditor.ckeditor_descripcion for="opinion" />

                {{-- IMÁGENES NUEVAS --}}
                <div class="mb-3">
                    <label class="form-label">
                        <strong>Fotos del trabajo (opcional)</strong>
                        <span class="text-muted small d-block">
                            Puedes subir hasta 3 imágenes (JPG, PNG o WEBP, máx. 2MB cada una).
                        </span>
                    </label>

                    <input type="file" name="imagenes[]"
                        class="form-control @error('imagenes') is-invalid @enderror @error('imagenes.*') is-invalid @enderror"
                        multiple accept="image/jpeg,image/png,image/webp">

                    @error('imagenes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @error('imagenes.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    Enviar valoración
                </button>
            </form>


        </div>
    </div>
@endsection

<x-alertas_sweet />
