@extends('layouts.main')

@section('title', 'Comentarios de mis clientes - ReformUp')

@section('content')

    <x-navbar />
    <x-profesional.profesional_sidebar />
    <x-profesional.profesional_bienvenido />

    <div class="container-fluid main-content-with-sidebar">
        <x-profesional.nav_movil active="comentarios" />

        <div class="container py-4" id="app">

            {{-- Título --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-chat-left-quote"></i> Comentarios de mis clientes
                </h1>
            </div>

            <x-alertas.alertasFlash />

            {{-- Filtros por estado (píldoras) --}}
            <ul class="nav nav-pills mb-3">
                {{-- Todos --}}
                <li class="nav-item">
                    <a class="nav-link {{ $estado === null ? 'active' : '' }}"
                       href="{{ route('profesional.comentarios.index', array_merge(request()->except('page', 'estado'), ['estado' => null])) }}">
                        Todos
                    </a>
                </li>

                {{-- Estados del modelo --}}
                @foreach ($estados as $valor => $texto)
                    <li class="nav-item">
                        <a class="nav-link {{ $estado === $valor ? 'active' : '' }}"
                           href="{{ route('profesional.comentarios.index', array_merge(request()->except('page'), ['estado' => $valor])) }}">
                            {{ $texto }}
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- Buscador --}}
            <form method="GET" action="{{ route('profesional.comentarios.index') }}" class="row g-2 mb-3">
                {{-- mantenemos estado en la query --}}
                @if(!is_null($estado))
                    <input type="hidden" name="estado" value="{{ $estado }}">
                @endif

                <div class="col-12 col-md-6 col-lg-4">
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           class="form-control form-control-sm"
                           placeholder="Buscar por cliente, título, opinión...">
                </div>

                <div class="col-6 col-md-3 col-lg-2 d-grid">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>

                <div class="col-6 col-md-3 col-lg-2 d-grid">
                    @if (request('q') || !is_null($estado))
                        <a href="{{ route('profesional.comentarios.index') }}" class="btn btn-sm btn-outline-secondary">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>

            @if ($comentarios->isEmpty())
                <div class="alert alert-info">
                    No tienes comentarios
                    {{ $estado ? 'con estado ' . ($estados[$estado] ?? $estado) : 'todavía' }}.
                </div>
            @else

                {{-- ========================= --}}
                {{-- TABLA (solo en lg+)      --}}
                {{-- ========================= --}}
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr class="fs-5">
                                {{-- Siempre visible --}}
                                <th class="text-center text-md-start">Trabajo / Solicitud</th>

                                {{-- En tablet (md+) --}}
                                <th class="d-none d-md-table-cell">Cliente</th>

                                {{-- Siempre visibles --}}
                                <th>Puntuación</th>
                                <th>Estado</th>

                                {{-- Solo en md+ --}}
                                <th class="d-none d-md-table-cell">Fecha</th>

                                {{-- Solo en lg+ --}}
                                <th class="d-none d-lg-table-cell">Opinión</th>

                                {{-- Acciones --}}
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($comentarios as $comentario)
                                @php
                                    $trabajo    = $comentario->trabajo;
                                    $presupuesto = $trabajo?->presupuesto;
                                    $solicitud  = $presupuesto?->solicitud;
                                    $cliente    = $solicitud?->cliente;

                                    $badgeClass = match ($comentario->estado) {
                                        'pendiente' => 'bg-warning text-dark',
                                        'publicado' => 'bg-success',
                                        'rechazado' => 'bg-secondary',
                                        default     => 'bg-light text-dark',
                                    };
                                @endphp

                                <tr>
                                    {{-- Trabajo / Solicitud --}}
                                    <td>
                                        <strong>
                                            @if ($solicitud?->titulo)
                                                {{ $solicitud->titulo }}
                                            @else
                                                Trabajo #{{ $trabajo?->id }}
                                            @endif
                                        </strong>
                                    </td>

                                    {{-- Cliente (md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        @if ($cliente)
                                            {{ $cliente->nombre ?? $cliente->name }}
                                            {{ $cliente->apellidos ?? '' }}<br>
                                            <small class="text-muted">{{ $cliente->email }}</small>
                                        @else
                                            <span class="text-muted small">Sin cliente</span>
                                        @endif
                                    </td>

                                    {{-- Puntuación --}}
                                    <td>
                                        {{ $comentario->puntuacion }} / 5
                                    </td>

                                    {{-- Estado --}}
                                    <td>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($comentario->estado) }}
                                        </span>
                                    </td>

                                    {{-- Fecha (md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        {{ $comentario->fecha?->format('d/m/Y H:i') ?? $comentario->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- Opinión (lg+) --}}
                                    <td class="d-none d-lg-table-cell">
                                        @if ($comentario->opinion)
                                            {{ \Illuminate\Support\Str::limit($comentario->opinion, 60, '...') }}
                                        @else
                                            <span class="text-muted small">Sin opinión</span>
                                        @endif
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-center">
                                        <div class="d-flex flex-row flex-wrap justify-content-center gap-2">
                                            <button type="button"
                                                    class="btn btn-sm btn-info d-inline-flex align-items-center gap-1"
                                                    @click="openComentarioModalPro({{ $comentario->id }})">
                                                Ver detalle
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ========================= --}}
                {{-- CARDS (xs–lg)            --}}
                {{-- ========================= --}}
                <div class="d-block d-lg-none">
                    @foreach ($comentarios as $comentario)
                        @php
                            $trabajo    = $comentario->trabajo;
                            $presupuesto = $trabajo?->presupuesto;
                            $solicitud  = $presupuesto?->solicitud;
                            $cliente    = $solicitud?->cliente;

                            $badgeClass = match ($comentario->estado) {
                                'pendiente' => 'bg-warning text-dark',
                                'publicado' => 'bg-success',
                                'rechazado' => 'bg-secondary',
                                default     => 'bg-light text-dark',
                            };
                        @endphp

                        <div class="card mb-3 shadow-sm">
                            <div class="card-body bg-light">

                                {{-- Cabecera: trabajo/solicitud --}}
                                <div class="mb-2">
                                    <div class="fw-semibold">
                                        @if ($solicitud?->titulo)
                                            {{ $solicitud->titulo }}
                                        @else
                                            Trabajo #{{ $trabajo?->id }}
                                        @endif
                                    </div>
                                </div>

                                {{-- Datos principales --}}
                                <div class="small text-muted mb-2">
                                    {{-- Cliente --}}
                                    <div class="mb-1">
                                        <strong>Cliente:</strong>
                                        @if ($cliente)
                                            {{ $cliente->nombre ?? $cliente->name }}
                                            {{ $cliente->apellidos ?? '' }}
                                        @else
                                            <span class="text-muted">Sin cliente</span>
                                        @endif
                                    </div>

                                    {{-- Puntuación --}}
                                    <div class="mb-1">
                                        <strong>Puntuación:</strong>
                                        {{ $comentario->puntuacion }} / 5
                                    </div>

                                    {{-- Estado --}}
                                    <div class="mb-1">
                                        <strong>Estado:</strong>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($comentario->estado) }}
                                        </span>
                                    </div>

                                    {{-- Fecha --}}
                                    <div class="mb-1">
                                        <strong>Fecha:</strong>
                                        {{ $comentario->fecha?->format('d/m/Y H:i') ?? $comentario->created_at?->format('d/m/Y H:i') }}
                                    </div>

                                    {{-- Opinión (resumen) --}}
                                    @if ($comentario->opinion)
                                        <div class="mb-1">
                                            <strong>Opinión:</strong>
                                            {{ \Illuminate\Support\Str::limit($comentario->opinion, 120, '...') }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Acciones en columna --}}
                                <div class="d-grid gap-2">
                                    <button type="button"
                                            class="btn btn-sm btn-info"
                                            @click="openComentarioModalPro({{ $comentario->id }})">
                                        Ver detalle
                                    </button>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="mt-3">
                    {{ $comentarios->links('pagination::bootstrap-5') }}
                </div>

            @endif

            {{-- Modal Vue para ver comentario --}}
            <comentario-pro-modal ref="ComentarioModalPro"></comentario-pro-modal>
        </div>

    </div>
@endsection

<x-alertas_sweet />
