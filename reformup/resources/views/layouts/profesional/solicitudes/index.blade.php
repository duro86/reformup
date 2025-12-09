@extends('layouts.main')

@section('title', 'Solicitudes recibidas - ReformUp')

@section('content')

    <x-navbar />

    <x-profesional.profesional_sidebar />
    <x-profesional.profesional_bienvenido />

    {{-- Contenedor Principal --}}
    <div class="container-fluid main-content-with-sidebar">
        <x-profesional.nav_movil active="solicitudes" />

        <div class="container py-2" id="app">

            {{-- Título --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-1 gap-2">
                <h4 class="mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-inbox"></i>
                    Listado Solicitudes
                </h4>
            </div>

            {{-- Mensajes flash --}}
            <x-alertas.alertasFlash />

            {{-- Buscador combinado: campos + fechas --}}
            <form method="GET" action="{{ route('profesional.solicitudes.index') }}" class="row g-2 mb-3 align-items-end">

                {{-- Búsqueda por texto --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <input type="text" name="q" value="{{ request('q') }}"
                        class="form-control form-control-sm bg-pro-primary"
                        placeholder="Buscar por título, ciudad, provincia, estado o cliente...">
                </div>

                {{-- Rango de fechas reutilizable (fecha de la solicitud) --}}
                @include('partials.filtros.rango_fechas')

                {{-- Botón Buscar --}}
                <div class="col-6 col-md-3 col-lg-2 d-grid">
                    <button type="submit" class="btn btn-sm btn-primary bg-pro-primary text-black">
                        <i class="bi bi-search "></i> Buscar
                    </button>
                </div>

                {{-- Botón Limpiar --}}
                <div class="col-6 col-md-3 col-lg-2 d-grid">
                    @if (request('q') || request('estado') || request('fecha_desde') || request('fecha_hasta'))
                        <a href="{{ route('profesional.solicitudes.index') }}" class="btn btn-sm btn-outline-pro">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>


            {{-- Filtros por estado --}}
            @if (isset($estados) && is_array($estados))
                <ul class="nav nav-pills mb-3">
                    {{-- Pestaña "Todas" --}}
                    <li class="nav-item">
                        @php
                            $urlTodas = route(
                                'profesional.solicitudes.index',
                                array_filter([
                                    'q' => request('q'),
                                    'fecha_desde' => request('fecha_desde'),
                                    'fecha_hasta' => request('fecha_hasta'),
                                    // sin 'estado'
                                ]),
                            );
                        @endphp

                        <a class="nav-link {{ $estado === null || $estado === '' ? 'active bg-pro-primary text-black fw-semibold' : 'text-muted' }}"
                            href="{{ $urlTodas }}">
                            Todas
                        </a>
                    </li>

                    {{-- Pestañas por cada estado del modelo --}}
                    @foreach ($estados as $valor => $texto)
                        @php
                            $urlEstado = route(
                                'profesional.solicitudes.index',
                                array_filter([
                                    'estado' => $valor,
                                    'q' => request('q'),
                                    'fecha_desde' => request('fecha_desde'),
                                    'fecha_hasta' => request('fecha_hasta'),
                                ]),
                            );

                            $esActivo = $estado === $valor;
                        @endphp

                        <li class="nav-item">
                            <a class="nav-link {{ $esActivo ? 'active bg-pro-primary text-black fw-semibold' : 'text-muted' }}"
                                href="{{ $urlEstado }}">
                                {{ $texto }}
                            </a>
                        </li>
                    @endforeach

                </ul>
            @endif

            {{-- SI no hay solicitudes --}}
            @if ($solicitudes->isEmpty())
                <div class="alert alert-info">
                    No tienes solicitudes
                    {{ $estado ? 'con estado ' . str_replace('_', ' ', $estado) : 'todavía' }}.
                </div>
            @else
                {{-- ===================================================== --}}
                {{-- TABLA SOLO ESCRITORIO (lg+)                         --}}
                {{-- ===================================================== --}}
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-sm align-middle border border-pro-secondary rounded">
                        <thead class="text-white">
                            <tr class="small text-center align-middle fs-5">
                                <th class="text-start bg-pro-secondary text-white">Cliente</th>
                                <th class="text-start bg-pro-secondary text-white">Título</th>
                                <th class="bg-pro-secondary text-white">Fecha</th>
                                <th class="bg-pro-secondary text-white">Estado</th>
                                <th class="bg-pro-secondary text-white">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($solicitudes as $solicitud)
                                @php
                                    $cliente = $solicitud->cliente;
                                    $badgeClass = match ($solicitud->estado) {
                                        'abierta' => 'bg-pro-secondary text-white',
                                        'en_revision' => 'bg-warning text-dark',
                                        'cerrada' => 'bg-success',
                                        'cancelada' => 'bg-secondary',
                                        default => 'bg-light text-dark',
                                    };
                                    // permitir rechazar sólo si está abierta
                                    $puedeRechazar = $solicitud->estado === 'abierta';
                                @endphp

                                <tr>
                                    {{-- Cliente --}}
                                    <td class="bg-pro-primary">
                                        @if ($cliente)
                                            <div class="fw-semibold">
                                                {{ $cliente->nombre }} {{ $cliente->apellidos }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $cliente->email }}
                                            </div>
                                            @if ($cliente->telefono)
                                                <div class="small text-muted">
                                                    {{ $cliente->telefono }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted small">Cliente no disponible</span>
                                        @endif
                                    </td>

                                    {{-- Título / Ref --}}
                                    <td class="bg-pro-primary">
                                        <strong>
                                            {{ $solicitud->titulo ?? 'Solicitud #' . $solicitud->id }}
                                        </strong>
                                        <div class="small text-muted">
                                            Ref: #{{ $solicitud->id }}
                                        </div>
                                    </td>

                                    {{-- Fecha --}}
                                    <td class="bg-pro-primary">
                                        {{ optional($solicitud->fecha ?? $solicitud->created_at)->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- Estado --}}
                                    <td class="bg-pro-primary text-center align-middle">
                                        <div class="d-flex flex-column align-items-center gap-1">

                                            <span class="badge {{ $badgeClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                            </span>

                                            @if ($solicitud->estado === 'en_revision')
                                                <div class="small text-black">
                                                    El cliente está valorando el presupuesto.
                                                </div>
                                            @endif

                                            @if ($solicitud->estado === 'abierta')
                                                <div class="small text-black">
                                                    Debes enviar presupuesto o rechazar la solicitud
                                                </div>
                                            @endif

                                            @if ($solicitud->estado === 'cancelada')
                                                <div class="small text-black">
                                                    Esta solicitud se ha cancelado, debes esperar a otra nueva.
                                                </div>
                                            @endif

                                            @if ($solicitud->estado === 'cerrada')
                                                <div class="small text-black">
                                                    Tu solicitud está cerrada, tienes el presupuesto aceptado y trabajo en
                                                    marcha.
                                                </div>
                                            @endif

                                        </div>
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="bg-pro-primary">
                                        <div class="d-flex flex-row flex-wrap gap-1 justify-content-center">

                                            {{-- Ver (modal Vue) --}}
                                            <button type="button"
                                                class="btn btn-info btn-sm px-2 py-1 d-inline-flex align-items-center gap-1"
                                                @click="openSolicitudModal({{ $solicitud->id }})">
                                                Ver
                                            </button>

                                            {{-- Crear presupuesto (solo si está abierta) --}}
                                            @if ($solicitud->estado === 'abierta')
                                                <a href="{{ route('profesional.presupuestos.crear_desde_solicitud', $solicitud) }}"
                                                    class="btn btn-sm bg-pro-secondary text-white px-2 py-1 d-inline-flex align-items-center gap-1">
                                                    Crear presupuesto
                                                </a>
                                            @endif

                                            {{-- Rechazar solicitud (solo si está abierta) --}}
                                            @if ($puedeRechazar)
                                                <x-profesional.solicitudes.btn_cancelar :solicitud="$solicitud" />
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
                    @foreach ($solicitudes as $solicitud)
                        @php
                            $cliente = $solicitud->cliente;
                            $badgeClass = match ($solicitud->estado) {
                                'abierta' => 'bg-primary',
                                'en_revision' => 'bg-warning text-dark',
                                'cerrada' => 'bg-success',
                                'cancelada' => 'bg-secondary',
                                default => 'bg-light text-dark',
                            };
                            $puedeRechazar = $solicitud->estado === 'abierta';
                        @endphp

                        <div class="card mb-3 shadow-sm bg-pro-primary">
                            <div class="card-body">

                                {{-- Cliente + fecha --}}
                                <div class="mb-2">
                                    @if ($cliente)
                                        <div class="fw-semibold">
                                            {{ $cliente->nombre }} {{ $cliente->apellidos }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ $cliente->email }}
                                        </div>
                                        @if ($cliente->telefono)
                                            <div class="small text-muted">
                                                {{ $cliente->telefono }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted small">Cliente no disponible</span>
                                    @endif

                                    {{-- Título / Ref --}}
                                    <td>
                                        <strong>
                                            {{ $solicitud->titulo ?? 'Solicitud #' . $solicitud->id }}
                                        </strong>
                                        <div class="small text-muted">
                                            Ref: <strong>#{{ $solicitud->id }}</strong>
                                        </div>
                                    </td>

                                    <div class="small text-muted mt-1">
                                        <strong>Fecha:</strong>
                                        {{ optional($solicitud->fecha ?? $solicitud->created_at)->format('d/m/Y H:i') }}
                                    </div>
                                </div>

                                {{-- Ubicación --}}
                                <div class="small text-muted mt-1">
                                    <strong>Ubicación:</strong>
                                    @if ($solicitud->provincia)
                                        {{ $solicitud->provincia }} -
                                    @endif
                                    {{ $solicitud->ciudad ?? 'No indicada' }}
                                </div>

                                {{-- Título --}}
                                <div class="small text-muted mb-2">

                                    {{-- Estado --}}
                                    <div class="mt-1">
                                        <strong>Estado:</strong>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                        </span>
                                        @if ($solicitud->estado === 'en_revision')
                                            <div class="small  text-black mt-1">
                                                El cliente está valorando el presupuesto.
                                            </div>
                                        @endif
                                        @if ($solicitud->estado === 'abierta')
                                            <div class="small  text-black mt-1">
                                                Debes enviar presupuesto o rechazar la solicitud
                                            </div>
                                        @endif
                                        @if ($solicitud->estado === 'cancelada')
                                            <div class="small  text-black mt-1">
                                                Esta solicitud se ha cancelado, debes esperar a otra nueva.
                                            </div>
                                        @endif
                                        @if ($solicitud->estado === 'cerrada')
                                            <div class="small  text-black mt-1">
                                                Tu solicitud está cerrada, tienes el presupuesto aceptado y trabajo en
                                                marcha.
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Acciones en columna, ancho completo --}}
                                <div class="d-grid gap-2">
                                    {{-- Ver --}}
                                    <button type="button" class="btn btn-info btn-sm"
                                        @click="openSolicitudModal({{ $solicitud->id }})">
                                        Ver
                                    </button>

                                    {{-- Crear presupuesto --}}
                                    @if ($solicitud->estado === 'abierta')
                                        <a href="{{ route('profesional.presupuestos.crear_desde_solicitud', $solicitud) }}"
                                            class="btn btn-sm bg-pro-secondary text-white">
                                            Crear presupuesto 
                                        </a>
                                    @endif

                                    {{-- Rechazar --}}
                                    @if ($puedeRechazar)
                                        <x-profesional.solicitudes.btn_cancelar :solicitud="$solicitud" />
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="paginacion-pro">
    {{ $solicitudes->links('pagination::bootstrap-5') }}
</div>

            @endif

            {{-- Modal Vue --}}
            <solicitud-modal ref="solicitudModal"></solicitud-modal>
        </div>
    </div>
@endsection
<x-alertas_sweet />
