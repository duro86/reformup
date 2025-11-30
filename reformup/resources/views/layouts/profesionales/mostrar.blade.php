@extends('layouts.main')

@section('title', $perfil->empresa . ' - ReformUp')

@section('content')
    {{-- NAVBAR simple --}}
    <x-navbar />
    <div class="container py-4">
        {{-- Botón volver --}}
        <div class="mb-3">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <div class="row">
            {{-- COLUMNA IZQUIERDA: info principal del profesional --}}
            <div class="col-12 col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">

                        {{-- Avatar --}}
                        @if ($perfil->avatar)
                            <img src="{{ asset('storage/' . $perfil->avatar) }}" alt="Avatar {{ $perfil->empresa }}"
                                class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="mb-3">
                                <i class="bi bi-building" style="font-size: 4rem;"></i>
                            </div>
                        @endif

                        {{-- Empresa --}}
                        <h1 class="h5 mb-1">
                            {{ $perfil->empresa }}
                        </h1>

                        {{-- Ciudad / provincia --}}
                        <p class="text-muted mb-2">
                            {{ $perfil->ciudad }}
                            @if ($perfil->provincia)
                                ({{ $perfil->provincia }})
                            @endif
                        </p>

                        {{-- Puntuación media --}}
                        @if (!is_null($perfil->puntuacion_media))
                            <p class="mb-2">
                                <strong>Valoración media:</strong>
                                {{ number_format($perfil->puntuacion_media, 1, ',', '.') }} / 5
                            </p>
                        @endif

                        {{-- Teléfono --}}
                        @if ($perfil->telefono_empresa)
                            <p class="mb-1">
                                <i class="bi bi-telephone me-1"></i>
                                {{ $perfil->telefono_empresa }}
                            </p>
                        @endif

                        {{-- Email --}}
                        @if ($perfil->email_empresa)
                            <p class="mb-1">
                                <i class="bi bi-envelope me-1"></i>
                                {{ $perfil->email_empresa }}
                            </p>
                        @endif

                        {{-- Web --}}
                        @if ($perfil->web)
                            <p class="mb-2">
                                <i class="bi bi-globe me-1"></i>
                                <a href="{{ $perfil->web }}" target="_blank" rel="noopener noreferrer">
                                    {{ $perfil->web }}
                                </a>
                            </p>
                        @endif

                        {{-- Dirección empresa --}}
                        @if ($perfil->dir_empresa)
                            <p class="small text-muted mb-3">
                                {{ $perfil->dir_empresa }}
                            </p>
                        @endif

                        {{-- Bio --}}
                        @if ($perfil->bio)
                            <div class="text-start small mb-3" style="white-space: pre-line;">
                                {{ $perfil->bio }}
                            </div>
                        @endif

                        {{-- Botón CONTRATAR --}}
                        <a href="{{ route('public.profesionales.contratar', $perfil) }}" class="btn btn-primary w-100">
                            Contratar a esta empresa
                        </a>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: trabajos y opiniones --}}
            <div class="col-12 col-md-8">

                {{-- Bloque trabajos + opiniones --}}
                <h2 class="h5 mb-3">Trabajos realizados y opiniones</h2>

                @if ($trabajos->isEmpty())
                    <div class="alert alert-info">
                        Todavía no hay trabajos finalizados publicados para este profesional.
                    </div>
                @else
                    @foreach ($trabajos as $trabajo)
                        @php
                            $presupuesto = $trabajo->presupuesto;
                            $solicitud = $presupuesto?->solicitud;
                        @endphp

                        <article class="card mb-3 shadow-sm">
                            <div class="card-body">
                                {{-- Cabecera del trabajo --}}
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h3 class="h6 mb-1">
                                            {{ $solicitud?->titulo ?? 'Trabajo #' . $trabajo->id }}
                                        </h3>
                                        <div class="small text-muted">
                                            @if ($trabajo->fecha_fin)
                                                Finalizado el {{ $trabajo->fecha_fin->format('d/m/Y') }}
                                            @else
                                                Finalizado
                                            @endif
                                            @if ($trabajo->dir_obra)
                                                · {{ \Illuminate\Support\Str::limit($trabajo->dir_obra, 60, '...') }}
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Total presupuesto --}}
                                    @if ($presupuesto?->total)
                                        <div class="text-end">
                                            <span class="badge bg-light text-dark">
                                                {{ number_format($presupuesto->total, 2, ',', '.') }} €
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Opiniones de este trabajo --}}
                                @if ($trabajo->comentarios->isEmpty())
                                    <p class="small text-muted mb-0">
                                        Aún no hay opiniones para este trabajo.
                                    </p>
                                @else
                                    @foreach ($trabajo->comentarios as $comentario)
                                        <div class="border-top pt-2 mt-2">
                                            <div class="d-flex align-items-center mb-1">
                                                {{-- Puntuación --}}
                                                @if (!is_null($comentario->puntuacion))
                                                    <span class="badge bg-warning text-dark me-2">
                                                        {{ $comentario->puntuacion }} / 5
                                                    </span>
                                                @endif

                                                {{-- Nombre del cliente --}}
                                                @if ($comentario->cliente)
                                                    <small class="text-muted">
                                                        {{ $comentario->cliente->name ?? 'Cliente' }}
                                                    </small>
                                                @endif
                                            </div>

                                            {{-- Opinión con formato CKEditor --}}
                                            @if ($comentario->opinion)
                                                <div class="mb-2 small">
                                                    {!! $comentario->opinion !!}
                                                </div>
                                            @endif

                                            {{-- Fotos asociadas al comentario --}}
                                            @if ($comentario->imagenes->isNotEmpty())
                                                <div id="carouselComentario{{ $comentario->id }}"
                                                    class="carousel slide mt-2" data-bs-ride="carousel">
                                                    <div class="carousel-inner rounded-3 overflow-hidden">
                                                        @foreach ($comentario->imagenes as $idx => $img)
                                                            <div
                                                                class="carousel-item @if ($idx === 0) active @endif">
                                                                <img src="{{ Storage::url($img->ruta) }}"
                                                                    class="d-block w-100"
                                                                    alt="Foto del trabajo asociado al comentario #{{ $comentario->id }}"
                                                                    style="object-fit: cover; max-height: 260px;">
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    @if ($comentario->imagenes->count() > 1)
                                                        <button class="carousel-control-prev" type="button"
                                                            data-bs-target="#carouselComentario{{ $comentario->id }}"
                                                            data-bs-slide="prev">
                                                            <span class="carousel-control-prev-icon"></span>
                                                            <span class="visually-hidden">Anterior</span>
                                                        </button>
                                                        <button class="carousel-control-next" type="button"
                                                            data-bs-target="#carouselComentario{{ $comentario->id }}"
                                                            data-bs-slide="next">
                                                            <span class="carousel-control-next-icon"></span>
                                                            <span class="visually-hidden">Siguiente</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif

                                        </div>
                                    @endforeach
                                @endif

                            </div>
                        </article>
                    @endforeach
                @endif

            </div>
        </div>
    </div>
@endsection
