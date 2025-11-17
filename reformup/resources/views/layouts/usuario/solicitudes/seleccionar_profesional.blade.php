@extends('layouts.main')

@section('title', 'Elegir profesional - ReformUp')

@section('content')

    <x-navbar />
    <x-usuario.usuario_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-search"></i> Elige un profesional
                </h1>

                <a href="{{ route('usuario.solicitudes.index') }}"
                    class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i>
                    Volver a mis solicitudes
                </a>
            </div>

            {{-- FILTROS --}}
            <form method="GET" action="{{ route('usuario.solicitudes.crear') }}" class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Ciudad</label>
                            <input type="text" name="ciudad" value="{{ old('ciudad', $ciudad) }}" class="form-control"
                                placeholder="Ej: Sevilla">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Provincia</label>
                            <input type="text" name="provincia" value="{{ old('provincia', $provincia) }}"
                                class="form-control" placeholder="Ej: Sevilla">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Oficios</label>

                            <div class="border rounded p-2"
                                style="max-height: 180px; overflow-y: auto; background-color: #fff;">
                                @foreach ($oficios as $oficio)
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="oficios[]"
                                            value="{{ $oficio->id }}" id="oficio_{{ $oficio->id }}"
                                            {{ in_array($oficio->id, (array) $oficiosSeleccionados) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="oficio_{{ $oficio->id }}">
                                            {{ ucfirst(str_replace('_', ' ', $oficio->nombre)) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <small class="text-muted">Selecciona uno o varios oficios</small>
                        </div>

                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Buscar
                        </button>

                        <a href="{{ route('usuario.solicitudes.crear') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-file-x"></i>Limpiar filtros
                        </a>
                    </div>
                </div>
            </form>

            {{-- RESULTADOS --}}
            @if ($profesionales->isEmpty())
                <div class="alert alert-info">
                    No se han encontrado profesionales con esos filtros.
                    Prueba a ampliar la zona o quitar algún filtro.
                </div>
            @else
                @php
                    // Helper para mostrar estrellas de 0-5
                    $renderStars = function ($score) {
                        $score = $score ?? 0;
                        $full = floor($score);
                        $half = $score - $full >= 0.5 ? 1 : 0;
                        $empty = 5 - $full - $half;

                        $html = str_repeat('<i class="bi bi-star-fill text-warning"></i>', $full);
                        $html .= str_repeat('<i class="bi bi-star-half text-warning"></i>', $half);
                        $html .= str_repeat('<i class="bi bi-star text-warning"></i>', $empty);

                        return $html;
                    };
                @endphp

                {{-- Listado Profesionales Cards --}}
                <div class="row g-3">
                    @foreach ($profesionales as $pro)
                        <div class="col-12 col-md-6 col-lg-4">
                            <article class="card h-100 shadow-sm border-0">
                                <div class="card-body d-flex flex-column">

                                    <div class="d-flex align-items-center mb-3">
                                        {{-- Avatar / logo --}}
                                        @if ($pro->avatar)
                                            <img src="{{ asset('storage/' . $pro->avatar) }}"
                                                alt="Logo {{ $pro->empresa }}" class="rounded-circle me-3"
                                                style="width:48px;height:48px;object-fit:cover;">
                                        @else
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3"
                                                style="width:48px;height:48px;">
                                                <i class="bi bi-building text-secondary fs-4"></i>
                                            </div>
                                        @endif

                                        <div>
                                            <h2 class="h6 mb-1">
                                                {{ $pro->empresa ?? 'Empresa sin nombre' }}
                                            </h2>
                                            <div class="text-muted small">
                                                {{ $pro->ciudad ?? 'Ciudad no indicada' }}
                                                @if ($pro->provincia)
                                                    · {{ $pro->provincia }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Oficios --}}
                                    @if ($pro->oficios->isNotEmpty())
                                        <div class="mb-2 small">
                                            @foreach ($pro->oficios as $of)
                                                <span class="badge bg-light text-muted border me-1">
                                                    {{ ucfirst(str_replace('_', ' ', $of->nombre)) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="mb-2 small text-muted">
                                            Oficios no especificados
                                        </div>
                                    @endif

                                    {{-- Telefono + puntuación --}}
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <div class="small">
                                            @if ($pro->telefono_empresa)
                                                <i class="bi bi-telephone me-1"></i>
                                                {{ $pro->telefono_empresa }}
                                            @else
                                                <span class="text-muted">
                                                    Teléfono no indicado
                                                </span>
                                            @endif
                                        </div>

                                        <div class="text-end">
                                            <div class="text-warning small">
                                                {!! $renderStars($pro->puntuacion_media) !!}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $pro->puntuacion_media ? number_format($pro->puntuacion_media, 1) . ' / 5' : 'Sin valoraciones' }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Botón seleccionar --}}
                                    <div class="mt-3">
                                        <a href="{{ route('usuario.solicitudes.crear_con_profesional', $pro->id) }}"
                                            class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1">
                                            <i class="bi bi-check2-circle"></i>
                                            Elegir este profesional
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    {{ $profesionales->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection
<x-alertas_sweet />
