@extends('layouts.main')

@section('title', 'Comentarios de mis clientes - ReformUp')

@section('content')

    <x-navbar />
    <x-profesional.profesional_sidebar />
    <x-profesional.profesional_bienvenido />

    <div class="container-fluid main-content-with-sidebar">
        <x-profesional.nav_movil active="comentarios" />

        <div class="container py-2" id="app">

            {{-- Título --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-1 gap-2">
                <h4 class="mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-chat-left-quote"></i>
                    Listado Comentarios
                </h4>
            </div>

            <x-alertas.alertasFlash />

            {{-- Buscador combinado: campos + fechas --}}
            <form method="GET"
                  action="{{ route('profesional.comentarios.index') }}"
                  class="row g-2 mb-3 align-items-end">

                {{-- Búsqueda por texto --}}
                <div class="col-12 col-md-4 col-lg-3">
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           class="form-control form-control-sm bg-pro-primary"
                           placeholder="Buscar por cliente, título o opinión...">
                </div>

                {{-- Puntuación mínima --}}
                <div class="col-6 col-md-4 col-lg-3">
                    <select name="puntuacion_min" class="form-select form-select-sm bg-pro-primary">
                        <option value="">Cualquier puntuación</option>
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}"
                                {{ (string) request('puntuacion_min') === (string) $i ? 'selected' : '' }}>
                                Desde {{ $i }} / 5
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Rango de fechas (fecha del comentario) --}}
                @include('partials.filtros.rango_fechas')

                {{-- Botón Buscar --}}
                <div class="col-6 col-md-3 col-lg-2 d-grid">
                    <button type="submit" class="btn btn-sm bg-pro-primary text-black">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>

                {{-- Botón Limpiar --}}
                <div class="col-6 col-md-3 col-lg-2 d-grid">
                    @if (request('q') || request('fecha_desde') || request('fecha_hasta') || request('puntuacion_min'))
                        <a href="{{ route('profesional.comentarios.index') }}"
                           class="btn btn-sm btn-outline-pro">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>

            {{-- Nota opcional para que el profesional lo entienda --}}
            <p class="small text-muted mb-3">
                Mostrando solo los comentarios <strong>publicados y visibles</strong> sobre tus trabajos.
            </p>

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
                    <table class="table table-sm align-middle border border-pro-secondary rounded">
                        <thead class="text-white">
                            <tr class="small text-center align-middle bg-pro-secondary fs-5">
                                {{-- Siempre visible --}}
                                <th class="text-center text-md-start bg-pro-secondary text-white">
                                    Trabajo 
                                </th>

                                {{-- En tablet (md+) --}}
                                <th class="bg-pro-secondary text-white d-none d-md-table-cell">
                                    Cliente
                                </th>

                                {{-- Siempre visibles --}}
                                <th class="bg-pro-secondary text-white">
                                    Puntuación
                                </th>
                                <th class="bg-pro-secondary text-white">
                                    Estado
                                </th>

                                {{-- Solo en md+ --}}
                                <th class="bg-pro-secondary text-white d-none d-md-table-cell text-center">
                                    Fecha
                                </th>

                                {{-- Solo en lg+ --}}
                                <th class="bg-pro-secondary text-white d-none d-lg-table-cell">
                                    Opinión
                                </th>

                                {{-- Acciones --}}
                                <th class="bg-pro-secondary text-white text-center">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($comentarios as $comentario)
                                @php
                                    $trabajo     = $comentario->trabajo;
                                    $presupuesto = $trabajo?->presupuesto;
                                    $solicitud   = $presupuesto?->solicitud;
                                    $cliente     = $solicitud?->cliente;

                                    $badgeClass = match ($comentario->estado) {
                                        'pendiente' => 'bg-warning text-dark',
                                        'publicado' => 'bg-success',
                                        'rechazado' => 'bg-secondary',
                                        default     => 'bg-light text-dark',
                                    };
                                @endphp

                                <tr>
                                    {{-- Trabajo / Solicitud --}}
                                    <td class="bg-pro-primary">
                                        <strong>
                                            @if ($solicitud?->titulo)
                                                {{ $solicitud->titulo }}
                                            @else
                                                Trabajo #{{ $trabajo?->id }}
                                            @endif
                                        </strong>
                                    </td>

                                    {{-- Cliente (md+) --}}
                                    <td class="d-none d-md-table-cell bg-pro-primary">
                                        @if ($cliente)
                                            {{ $cliente->nombre ?? $cliente->name }}
                                            {{ $cliente->apellidos ?? '' }}<br>
                                            <small class="text-muted">{{ $cliente->email }}</small>
                                        @else
                                            <span class="text-muted small">Sin cliente</span>
                                        @endif
                                    </td>

                                    {{-- Puntuación --}}
                                    <td class="bg-pro-primary">
                                        {{ $comentario->puntuacion }} / 5
                                    </td>

                                    {{-- Estado --}}
                                    <td class="bg-pro-primary">
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($comentario->estado) }}
                                        </span>
                                    </td>

                                    {{-- Fecha (md+) --}}
                                    <td class="d-none d-md-table-cell bg-pro-primary">
                                        {{ $comentario->fecha?->format('d/m/Y H:i') ?? $comentario->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- Opinión (resumen) --}}
                                    <td class="bg-pro-primary">
                                        @if ($comentario->opinion)
                                            <div class="mb-1">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($comentario->opinion), 50, '...') }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-center bg-pro-primary">
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
                            $trabajo     = $comentario->trabajo;
                            $presupuesto = $trabajo?->presupuesto;
                            $solicitud   = $presupuesto?->solicitud;
                            $cliente     = $solicitud?->cliente;

                            $badgeClass = match ($comentario->estado) {
                                'pendiente' => 'bg-warning text-dark',
                                'publicado' => 'bg-success',
                                'rechazado' => 'bg-secondary',
                                default     => 'bg-light text-dark',
                            };
                        @endphp

                        <div class="card mb-3 shadow-sm bg-pro-primary">
                            <div class="card-body ">

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
                                            {{ \Illuminate\Support\Str::limit(strip_tags($comentario->opinion), 120, '...') }}
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
