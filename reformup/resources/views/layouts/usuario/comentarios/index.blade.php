@extends('layouts.main')

@section('title', 'Mis comentarios - ReformUp')

@section('content')
    <x-navbar />
    <x-usuario.usuario_sidebar />
    <x-usuario.user_bienvenido />

    <div class="container-fluid main-content-with-sidebar">
        <x-usuario.nav_movil active="comentarios" />

        <div class="container py-2">

            {{-- Título --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-1 gap-2">
                <h4 class="mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-chat-left-text"></i>
                    Listado Comentarios
                </h4>
            </div>

            {{-- Mensajes flash --}}
            <x-alertas.alertasFlash />

            {{-- Buscador combinado: texto + fechas --}}
            <form method="GET" action="{{ route('usuario.comentarios.index') }}" class="row g-2 mb-3">

                {{-- Búsqueda por texto --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                        placeholder="Buscar por trabajo, profesional, opinión o estado...">
                </div>

                {{-- Rango de fechas (fecha del comentario) --}}
                @include('partials.filtros.rango_fechas')

                {{-- Botón Buscar --}}
                <div class="col-6 col-md-3 col-lg-2 d-grid">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>

                {{-- Botón Limpiar --}}
                <div class="col-6 col-md-3 col-lg-2 d-grid">
                    @if (request('q') ||
                            request('estado') ||
                            request('fecha_desde') ||
                            request('fecha_hasta') ||
                            request('puntuacion_min') ||
                            request('puntuacion_max'))
                        <a href="{{ route('usuario.comentarios.index') }}" class="btn btn-sm btn-outline-secondary">
                            Limpiar
                        </a>
                    @endif
                </div>
                {{-- Fila extra para filtros de puntuación --}}
                <div class="row g-2 mb-3">
                    <div class="col-12 col-md-3 col-lg-2 d-flex align-items-center">
                        <small class="text-muted">
                            Filtrar por puntuación:
                        </small>
                    </div>

                    {{-- Puntuación mínima --}}
                    <div class="col-6 col-md-2 col-lg-1">
                        <input type="number" name="puntuacion_min" value="{{ request('puntuacion_min') }}"
                            class="form-control form-control-sm" min="1" max="5" placeholder="Mín">
                    </div>

                    {{-- Puntuación máxima --}}
                    <div class="col-6 col-md-2 col-lg-1">
                        <input type="number" name="puntuacion_max" value="{{ request('puntuacion_max') }}"
                            class="form-control form-control-sm" min="1" max="5" placeholder="Máx">
                    </div>
                </div>
            </form>

            {{-- Filtros por estado (pills) --}}
            @if (isset($estados) && is_array($estados))
                <ul class="nav nav-pills mb-3">
                    {{-- Pestaña "Todos" --}}
                    <li class="nav-item">
                        @php
                            $urlTodos = route(
                                'usuario.comentarios.index',
                                array_filter([
                                    'q' => request('q'),
                                    'fecha_desde' => request('fecha_desde'),
                                    'fecha_hasta' => request('fecha_hasta'),
                                    'puntuacion_min' => request('puntuacion_min'),
                                    'puntuacion_max' => request('puntuacion_max'),
                                    // sin 'estado'
                                ]),
                            );
                        @endphp

                        <a class="nav-link {{ $estado === null || $estado === '' ? 'active' : '' }}"
                            href="{{ $urlTodos }}">
                            Todos
                        </a>
                    </li>

                    {{-- Pestañas por cada estado del modelo --}}
                    @foreach ($estados as $valor => $texto)
                        @php
                            $urlEstado = route(
                                'usuario.comentarios.index',
                                array_filter([
                                    'estado' => $valor,
                                    'q' => request('q'),
                                    'fecha_desde' => request('fecha_desde'),
                                    'fecha_hasta' => request('fecha_hasta'),
                                    'puntuacion_min' => request('puntuacion_min'),
                                    'puntuacion_max' => request('puntuacion_max'),
                                ]),
                            );
                        @endphp

                        <li class="nav-item">
                            <a class="nav-link {{ $estado === $valor ? 'active' : '' }}" href="{{ $urlEstado }}">
                                {{ $texto }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif


            @if ($comentarios->isEmpty())
                <div class="alert alert-info">
                    Todavía no has dejado ningún comentario.
                </div>
            @else
                {{-- ========================= --}}
                {{-- TABLA (solo en lg+)      --}}
                {{-- ========================= --}}
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr class="fs-5">
                                <th class="text-center text-md-start bg-secondary">Trabajo</th>
                                <th class="d-none d-md-table-cell bg-secondary">Profesional</th>
                                <th class="bg-secondary">Puntuación</th>
                                <th class="d-none d-md-table-cell bg-secondary">Estado</th>
                                <th class="d-none d-md-table-cell bg-secondary">Fecha</th>
                                <th class="text-end bg-secondary">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($comentarios as $comentario)
                                @php
                                    $trabajo = $comentario->trabajo;
                                    $presu = $trabajo?->presupuesto;
                                    $solicitud = $presu?->solicitud;
                                    $pro = $presu?->profesional;

                                    $badgeClass = match ($comentario->estado) {
                                        'pendiente' => 'bg-warning text-dark',
                                        'publicado' => 'bg-success',
                                        'rechazado' => 'bg-secondary',
                                        default => 'bg-light text-dark',
                                    };
                                @endphp

                                <tr>
                                    {{-- Trabajo --}}
                                    <td>
                                        <div class="fw-semibold">
                                            @if ($solicitud?->titulo)
                                                {{ $solicitud->titulo }}
                                            @else
                                                Trabajo #{{ $trabajo?->id }}
                                            @endif
                                        </div>
                                        <div class="small text-muted">
                                            Comentario #{{ $comentario->id }}
                                        </div>
                                    </td>

                                    {{-- Profesional (md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        @if ($pro)
                                            {{ $pro->empresa }}<br>
                                            <small class="text-muted">
                                                {{ $pro->ciudad }}
                                                {{ $pro->provincia ? ' - ' . $pro->provincia : '' }}
                                            </small>
                                        @else
                                            <span class="text-muted small">Sin profesional</span>
                                        @endif
                                    </td>

                                    {{-- Puntuación (estrellas) --}}
                                    <td>
                                        <div class="text-nowrap text-start">
                                            <x-estrellas :valor="$comentario->puntuacion" />
                                        </div>
                                    </td>

                                    {{-- Estado (md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($comentario->estado) }}
                                        </span>
                                    </td>

                                    {{-- Fecha (md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        {{ optional($comentario->fecha)->format('d/m/Y H:i') ?? $comentario->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-end">
                                        @if ($comentario->opinion)
                                            <button type="button" class="btn btn-sm btn-info mb-1" data-bs-toggle="modal"
                                                data-bs-target="#comentarioUserModal{{ $comentario->id }}">
                                                Ver
                                            </button>
                                        @endif

                                        @if (in_array($comentario->estado, ['pendiente', 'rechazado']))
                                            <a href="{{ route('usuario.comentarios.editar', $comentario) }}"
                                                class="btn btn-sm btn-warning mb-1">
                                                Editar
                                            </a>
                                        @endif
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
                            $trabajo = $comentario->trabajo;
                            $presu = $trabajo?->presupuesto;
                            $solicitud = $presu?->solicitud;
                            $pro = $presu?->profesional;

                            $badgeClass = match ($comentario->estado) {
                                'pendiente' => 'bg-warning text-dark',
                                'publicado' => 'bg-success',
                                'rechazado' => 'bg-secondary',
                                default => 'bg-light text-dark',
                            };
                        @endphp

                        <div class="card mb-3 shadow-sm bg-light">
                            <div class="card-body">

                                {{-- Cabecera: trabajo --}}
                                <div class="mb-2">
                                    <div class="fw-semibold">
                                        @if ($solicitud?->titulo)
                                            {{ $solicitud->titulo }}
                                        @else
                                            Trabajo #{{ $trabajo?->id }}
                                        @endif
                                    </div>
                                    <div class="small text-muted">
                                        Comentario #{{ $comentario->id }}
                                    </div>
                                </div>

                                {{-- Datos principales --}}
                                <div class="small text-muted mb-2">

                                    {{-- Profesional --}}
                                    <div class="mb-1">
                                        <strong>Profesional:</strong>
                                        @if ($pro)
                                            {{ $pro->empresa }}<br>
                                            <span class="text-muted">
                                                {{ $pro->email_empresa }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin profesional</span>
                                        @endif
                                    </div>

                                    {{-- Puntuación --}}
                                    <div class="mb-1">
                                        <strong>Puntuación:</strong>
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $comentario->puntuacion)
                                                <i class="bi bi-star-fill text-warning"></i>
                                            @else
                                                <i class="bi bi-star text-muted"></i>
                                            @endif
                                        @endfor
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
                                        {{ optional($comentario->fecha)->format('d/m/Y H:i') ?? $comentario->created_at?->format('d/m/Y H:i') }}
                                    </div>

                                    {{-- Opinión (resumen) --}}
                                    @if ($comentario->opinion)
                                        <div class="mb-1">
                                            <strong>Opinión:</strong>
                                            {{ \Illuminate\Support\Str::limit(strip_tags($comentario->opinion), 120, '...') }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Acciones --}}
                                <div class="d-grid gap-2">
                                    @if ($comentario->opinion)
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                            data-bs-target="#comentarioUserModal{{ $comentario->id }}">
                                            Ver
                                        </button>
                                    @endif

                                    @if ($comentario->estado === 'pendiente')
                                        <a href="{{ route('usuario.comentarios.editar', $comentario) }}"
                                            class="btn btn-sm btn-warning">
                                            Editar
                                        </a>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Modales para ver opinión --}}
                @foreach ($comentarios as $comentario)
                    @if ($comentario->opinion)
                        <div class="modal fade" id="comentarioUserModal{{ $comentario->id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            Comentario #{{ $comentario->id }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Puntuación:</strong> {{ $comentario->puntuacion }} / 5</p>
                                        <p><strong>Estado:</strong> {{ ucfirst($comentario->estado) }}</p>
                                        <p><strong>Opinión:</strong><br>{!! $comentario->opinion !!}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            Cerrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- Paginación --}}
                <div class="mt-3">
                    {{ $comentarios->links('pagination::bootstrap-5') }}
                </div>

            @endif
        </div>
    </div>
@endsection

<x-alertas_sweet />
