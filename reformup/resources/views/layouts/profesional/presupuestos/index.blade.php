@extends('layouts.main')

@section('title', 'Mis presupuestos - ReformUp')

@section('content')

    <x-navbar />
    <x-profesional.profesional_sidebar />
    <x-profesional.profesional_bienvenido />

    {{-- Contenedor Principal --}}
    <div class="container-fluid main-content-with-sidebar">
        <x-profesional.nav_movil active="presupuestos" />

        <div class="container py-4">

            {{-- Título --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text"></i>
                    Mis presupuestos
                </h1>
            </div>

            {{-- Mensajes flash --}}
            <x-alertas.alertasFlash />

            {{-- Buscador combinado: campos + fechas --}}
            <form method="GET" action="{{ route('profesional.presupuestos.index') }}" class="row g-2 mb-3">
                {{-- Búsqueda por texto --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                        placeholder="Buscar por título, cliente, ciudad, estado o importe...">
                </div>

                {{-- Rango de fechas reutilizable (fecha del presupuesto) --}}
                @include('partials.filtros.rango_fechas')

                {{-- Botón Buscar --}}
                <div class="col-6 col-md-3 col-lg-2 d-grid">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>

                {{-- Botón Limpiar --}}
                <div class="col-6 col-md-3 col-lg-2 d-grid">
                    @if (request('q') || request('estado') || request('fecha_desde') || request('fecha_hasta'))
                        <a href="{{ route('profesional.presupuestos.index') }}" class="btn btn-sm btn-outline-secondary">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>

            {{-- Filtros por estado --}}
            @if (isset($estados) && is_array($estados))
                <ul class="nav nav-pills mb-3">
                    {{-- Pestaña "Todos" --}}
                    <li class="nav-item">
                        @php
                            $urlTodos = route(
                                'profesional.presupuestos.index',
                                array_filter([
                                    'q' => request('q'),
                                    'fecha_desde' => request('fecha_desde'),
                                    'fecha_hasta' => request('fecha_hasta'),
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
                                'profesional.presupuestos.index',
                                array_filter([
                                    'estado' => $valor,
                                    'q' => request('q'),
                                    'fecha_desde' => request('fecha_desde'),
                                    'fecha_hasta' => request('fecha_hasta'),
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



            {{-- Lista de presupuestos --}}
            @if ($presupuestos->isEmpty())
                <div class="alert alert-info">
                    No tienes presupuestos
                    {{ $estado ? 'con estado ' . str_replace('_', ' ', $estado) : 'todavía' }}.
                </div>
            @else
                {{-- ===================================================== --}}
                {{-- TABLA SOLO ESCRITORIO (lg+)                         --}}
                {{-- ===================================================== --}}
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr class="fs-5">
                                <th class="bg-secondary">Presupuesto</th>
                                <th class="bg-secondary">Cliente</th>
                                <th class="bg-secondary">Importe</th>
                                <th class="bg-secondary text-center">Estado</th>
                                <th class="bg-secondary text-center">Fecha</th>
                                <th class=" bg-secondary text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($presupuestos as $presu)
                                @php
                                    $cliente = $presu->solicitud?->cliente;
                                    $badgeClass = match ($presu->estado) {
                                        'enviado' => 'bg-primary',
                                        'aceptado' => 'bg-success',
                                        'rechazado' => 'bg-danger',
                                        'caducado' => 'bg-secondary',
                                        default => 'bg-light text-dark',
                                    };
                                @endphp

                                <tr>
                                    {{-- Solicitud / título --}}
                                    <td>
                                        <strong>
                                            {{ $presu->solicitud->titulo ?? '—' }}
                                        </strong>
                                        <div class="small text-muted">
                                            Ref. presupuesto: #{{ $presu->id }}
                                            @if ($presu->solicitud)
                                                · Solicitud #{{ $presu->solicitud->id }}
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Cliente --}}
                                    <td>
                                        @if ($cliente)
                                            {{ $cliente->nombre }} {{ $cliente->apellidos }}
                                        @else
                                            <span class="text-muted small">Sin datos</span>
                                        @endif
                                    </td>

                                    {{-- Importe --}}
                                    <td>
                                        {{ number_format($presu->total, 2, ',', '.') }} €
                                    </td>

                                    {{-- Estado (centrado) --}}
                                    <td class="text-center">
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($presu->estado) }}
                                        </span>
                                        @if ($presu->estado === 'enviado')
                                            <div class="small text-primary mt-1">
                                                El cliente está valorando el presupuesto
                                            </div>
                                        @endif
                                        @if ($presu->estado === 'aceptado')
                                            <div class="small text-primary mt-1">
                                                El cliente ha aceptado el presupuesto, revisa tus trabajos.
                                            </div>
                                        @endif
                                        @if ($presu->estado === 'rechazado')
                                            <div class="small text-primary mt-1">
                                                Se ha rechazado el presupuesto, puedes enviar uno nuevo.
                                            </div>
                                        @endif
                                        @if ($presu->estado === 'caducado')
                                            <div class="small text-primary mt-1">
                                                La solicitud está cerrada, el trabajo ya está en marcha o finalizado.
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Fecha --}}
                                    <td>
                                        {{ $presu->fecha?->format('d/m/Y H:i') ?? $presu->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-start">
                                        <div class="d-flex flex-row flex-wrap gap-1 justify-content-start">

                                            {{-- Ver PDF (sólo si existe) --}}
                                            @if ($presu->docu_pdf)
                                                <a href="{{ route('presupuestos.ver_pdf', $presu) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 mx-1 fw-semibold text-dark px-2 py-1 rounded">
                                                    <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                                                </a>
                                            @else
                                                <span class="text-muted small me-2">Sin PDF</span>
                                            @endif

                                            {{-- Botón Cancelar presupuesto --}}
                                            @if ($presu->estado === 'enviado')
                                                <x-profesional.presupuestos.btn_cancelar :presupuesto="$presu" />
                                            @endif

                                            {{-- Botón Nuevo presupuesto desde solicitud (si fue rechazado) --}}
                                            @php
                                                $solicitud = $presu->solicitud;

                                                $esUltimoPresuDeSolicitud = $solicitud
                                                    ? $solicitud->presupuestos->max('id') === $presu->id
                                                    : false;

                                                $tieneOtroActivo = $solicitud
                                                    ? $solicitud->presupuestos
                                                        ->whereIn('estado', ['enviado', 'aceptado'])
                                                        ->where('id', '!=', $presu->id)
                                                        ->isNotEmpty()
                                                    : false;
                                            @endphp

                                            @if (
                                                $presu->estado === 'rechazado' &&
                                                    $solicitud &&
                                                    in_array($solicitud->estado, ['abierta', 'en_revision']) &&
                                                    $esUltimoPresuDeSolicitud &&
                                                    !$tieneOtroActivo)
                                                <a href="{{ route('profesional.presupuestos.crear_desde_solicitud', $solicitud) }}"
                                                    class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 mx-1 fw-semibold text-dark px-2 py-1 rounded">
                                                    <i class="bi bi-plus-circle"></i>
                                                    Nuevo presupuesto
                                                </a>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ===================================================== --}}
                {{-- VISTA CARDS MÓVIL/TABLET (xs–lg)                     --}}
                {{-- ===================================================== --}}
                <div class="d-block d-lg-none">
                    @foreach ($presupuestos as $presu)
                        @php
                            $cliente = $presu->solicitud?->cliente;
                            $badgeClass = match ($presu->estado) {
                                'enviado' => 'bg-primary',
                                'aceptado' => 'bg-success',
                                'rechazado' => 'bg-danger',
                                'caducado' => 'bg-secondary',
                                default => 'bg-light text-dark',
                            };
                        @endphp

                        <div class="card mb-3 shadow-sm bg-light">
                            <div class="card-body">

                                {{-- Título solicitud + refs --}}
                                <div class="mb-2">
                                    <div class="fw-semibold">
                                        {{ $presu->solicitud->titulo ?? '—' }}
                                    </div>
                                    <div class="small text-muted">
                                        Ref. presupuesto: #{{ $presu->id }}
                                        @if ($presu->solicitud)
                                            · Solicitud #{{ $presu->solicitud->id }}
                                        @endif
                                    </div>
                                </div>

                                <div class="small text-muted mb-2">
                                    {{-- Cliente --}}
                                    <div class="mb-1">
                                        <strong>Cliente:</strong>
                                        @if ($cliente)
                                            {{ $cliente->nombre }} {{ $cliente->apellidos }}
                                        @else
                                            <span class="text-muted">Sin datos</span>
                                        @endif
                                    </div>

                                    {{-- Importe --}}
                                    <div class="mb-1">
                                        <strong>Importe:</strong>
                                        {{ number_format($presu->total, 2, ',', '.') }} €
                                    </div>

                                    {{-- Estado --}}
                                    <div class="mb-1">
                                        <strong>Estado:</strong>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($presu->estado) }}
                                        </span>
                                        @if ($presu->estado === 'enviado')
                                            <div class="small text-primary mt-1">
                                                El cliente está valorando el presupuesto
                                            </div>
                                        @endif
                                        @if ($presu->estado === 'aceptado')
                                            <div class="small text-primary mt-1">
                                                El cliente ha aceptado el presupuesto, revisa tus trabajos.
                                            </div>
                                        @endif
                                        @if ($presu->estado === 'rechazado')
                                            <div class="small text-primary mt-1">
                                                Se ha rechazado el presupuesto, puedes enviar uno nuevo.
                                            </div>
                                        @endif
                                        @if ($presu->estado === 'caducado')
                                            <div class="small text-primary mt-1">
                                                La solicitud está cerrada, el trabajo ya está en marcha o finalizado.
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Fecha --}}
                                    <div class="mb-1">
                                        <strong>Fecha:</strong>
                                        {{ $presu->fecha?->format('d/m/Y H:i') ?? $presu->created_at?->format('d/m/Y H:i') }}
                                    </div>
                                </div>

                                {{-- Acciones en columna, ancho completo --}}
                                <div class="d-grid gap-2">
                                    {{-- Ver PDF --}}
                                    @if ($presu->docu_pdf)
                                        <a href="{{ route('presupuestos.ver_pdf', $presu) }}" target="_blank"
                                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 fw-semibold text-dark px-2 py-1 rounded">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                            Ver PDF
                                        </a>
                                    @else
                                        <span class="text-muted small">Sin PDF</span>
                                    @endif

                                    {{-- Cancelar presupuesto (si está enviado) --}}
                                    @if ($presu->estado === 'enviado')
                                        <x-profesional.presupuestos.btn_cancelar :presupuesto="$presu" />
                                    @endif

                                    {{-- Nuevo presupuesto (si rechazado y solicitud abierta/en revisión) --}}
                                    @php
                                        $solicitud = $presu->solicitud;

                                        $esUltimoPresuDeSolicitud = $solicitud
                                            ? $solicitud->presupuestos()->max('id') === $presu->id
                                            : false;

                                        $tieneOtroActivo = $solicitud
                                            ? $solicitud
                                                ->presupuestos()
                                                ->whereIn('estado', ['enviado', 'aceptado'])
                                                ->where('id', '!=', $presu->id)
                                                ->exists()
                                            : false;
                                    @endphp

                                    @if (
                                        $presu->estado === 'rechazado' &&
                                            $solicitud &&
                                            in_array($solicitud->estado, ['abierta', 'en_revision']) &&
                                            $esUltimoPresuDeSolicitud &&
                                            !$tieneOtroActivo)
                                        <a href="{{ route('profesional.presupuestos.crear_desde_solicitud', $solicitud) }}"
                                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 mx-1 fw-semibold text-dark px-2 py-1 rounded">
                                            <i class="bi bi-plus-circle"></i>
                                            Nuevo presupuesto
                                        </a>
                                    @endif

                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="mt-3">
                    {{ $presupuestos->links('pagination::bootstrap-5') }}
                </div>

            @endif

        </div>
    </div>

@endsection
<x-alertas_sweet />
