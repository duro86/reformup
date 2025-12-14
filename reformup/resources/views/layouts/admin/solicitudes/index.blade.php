@extends('layouts.main')

@section('title', 'Gestión de solicitudes - Admin - ReformUp')

@section('content')

    <x-navbar />
    <x-admin.admin_sidebar />
    {{-- Bienvenida --}}
    <x-admin.admin_bienvenido />

    <div class="container-fluid main-content-with-sidebar">

        {{-- Nav móvil admin --}}
        <x-admin.nav_movil active="solicitudes" />

        <div class="container py-2" id="app">

            {{-- Título + botón nueva --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-2 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text"></i> Gestión de solicitudes
                </h1>

                <a href="{{ route('admin.solicitudes.crear') }}"
                    class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-plus-circle"></i>
                    Nueva solicitud
                </a>
            </div>

            {{-- Mensajes flash --}}
            <x-alertas.alertasFlash />

            {{-- Buscador texto + fechas --}}
            <form method="GET" action="{{ route('admin.solicitudes') }}" class="row g-2 mb-3">
                {{-- Búsqueda libre --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                        placeholder="Buscar por título, cliente, profesional, ciudad, provincia o estado...">
                </div>

                {{-- Rango de fechas reutilizable (usa fecha_desde / fecha_hasta) --}}
                @include('partials.filtros.rango_fechas')

                {{-- Botón Buscar --}}
                <div class="col-6 col-md-3 col-lg-2 d-grid">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>

                {{-- Botón Limpiar --}}
                <div class="col-6 col-md-3 col-lg-2 d-grid">
                    @if (request('q') || request('fecha_desde') || request('fecha_hasta'))
                        <a href="{{ route('admin.solicitudes') }}" class="btn btn-sm btn-outline-secondary">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>

            {{-- Filtros por estado Buscador por estado --}}
            <ul class="nav nav-pills mb-3">
                {{-- Todos --}}
                <li class="nav-item">
                    @php
                        $urlTodos = route(
                            'admin.solicitudes',
                            array_filter([
                                'q' => request('q'),
                                'fecha_desde' => request('fecha_desde'),
                                'fecha_hasta' => request('fecha_hasta'),
                            ]),
                        );
                    @endphp
                    <a class="nav-link fs-6 {{ $estado === null || $estado === '' ? 'active' : '' }}"
                        href="{{ $urlTodos }}">
                        Todas
                    </a>
                </li>

                {{-- ESTADOS DEL MODELO --}}
                @foreach ($estados as $valor => $texto)
                    @php
                        $urlEstado = route(
                            'admin.solicitudes',
                            array_filter([
                                'estado' => $valor,
                                'q' => request('q'),
                                'fecha_desde' => request('fecha_desde'),
                                'fecha_hasta' => request('fecha_hasta'),
                            ]),
                        );
                    @endphp

                    <li class="nav-item">
                        <a class="nav-link fs-6 {{ $estado === $valor ? 'active' : '' }}" href="{{ $urlEstado }}">
                            {{ $texto }}
                        </a>
                    </li>
                @endforeach
            </ul>


            @if ($solicitudes->isEmpty())
                <div class="alert alert-info">
                    No hay solicitudes
                    {{ $estado ? 'con estado ' . str_replace('_', ' ', $estado) : 'registrados todavía.' }}
                </div>
            @else
                {{-- ===================================================== --}}
                {{-- TABLA SOLO ESCRITORIO (lg+)                         --}}
                {{-- ===================================================== --}}
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr class="fs-5">
                                <th>Título / Ref</th>
                                <th>Cliente</th>
                                <th>Profesional</th>
                                <th>Municipio / Provincia</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($solicitudes as $solicitud)
                                @php
                                    $cliente = $solicitud->cliente;
                                    $pro = $solicitud->profesional;
                                    $badgeClass = match ($solicitud->estado) {
                                        'abierta' => 'bg-primary',
                                        'en_revision' => 'bg-warning text-dark',
                                        'cerrada' => 'bg-success',
                                        'cancelada' => 'bg-secondary',
                                        default => 'bg-light text-dark',
                                    };
                                @endphp

                                <tr>
                                    {{-- Título / Ref --}}
                                    <td>
                                        <strong>
                                            {{ $solicitud->titulo ?? 'Solicitud #' . $solicitud->id }}
                                        </strong>
                                        <div class="small text-muted">
                                            Ref: #{{ $solicitud->id }}
                                        </div>
                                    </td>

                                    {{-- Cliente --}}
                                    <td>
                                        @if ($cliente)
                                            {{ $cliente->nombre ?? $cliente->name }}
                                            {{ $cliente->apellidos ?? '' }}<br>
                                            <small class="text-muted">{{ $cliente->email }}</small>
                                        @else
                                            <span class="text-muted small">Sin cliente</span>
                                        @endif
                                    </td>

                                    {{-- Profesional --}}
                                    <td>
                                        @if ($pro)
                                            {{ $pro->empresa }}<br>
                                            @if ($pro->email_empresa)
                                                <small class="text-muted d-block">
                                                    {{ $pro->email_empresa }}
                                                </small>
                                            @endif
                                            <small class="text-muted">
                                                {{ $pro->ciudad }}
                                                {{ $pro->provincia ? ' - ' . $pro->provincia : '' }}
                                            </small>
                                        @else
                                            <span class="text-muted small">Sin asignar</span>
                                        @endif
                                    </td>

                                    {{-- Ciudad / Provincia --}}
                                    <td>
                                        {{ $solicitud->ciudad ?? 'No indicada' }}
                                        @if ($solicitud->provincia)
                                            - {{ $solicitud->provincia }}
                                        @endif
                                    </td>

                                    {{-- Estado --}}
                                    <td>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                        </span>
                                    </td>

                                    {{-- Fecha --}}
                                    <td>
                                        {{ $solicitud->fecha?->format('d/m/Y H:i') ?? $solicitud->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-center">
                                        <div class="d-flex flex-row flex-wrap gap-1 justify-content-center">

                                            {{-- Ver (modal Vue) --}}
                                            <button type="button"
                                                class="btn btn-info btn-sm px-2 py-1 d-inline-flex align-items-center gap-1"
                                                @click="openSolicitudAdminModal({{ $solicitud->id }})">
                                                Ver
                                            </button>

                                            {{-- Editar sólo si abierta o en revisión --}}
                                            @if (in_array($solicitud->estado, ['abierta', 'en_revision']))
                                                <a href="{{ route('admin.solicitudes.editar', [$solicitud->id, 'page' => $solicitudes->currentPage()]) }}"
                                                    class="btn btn-warning btn-sm px-2 py-1 d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </a>
                                            @endif
                                            @if ($solicitud->estado)
                                                <x-admin.solicitudes.btn_eliminar :solicitud="$solicitud" />
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
                            $pro = $solicitud->profesional;
                            $badgeClass = match ($solicitud->estado) {
                                'abierta' => 'bg-primary',
                                'en_revision' => 'bg-warning text-dark',
                                'cerrada' => 'bg-success',
                                'cancelada' => 'bg-secondary',
                                default => 'bg-light text-dark',
                            };
                        @endphp

                        <div class="card mb-3 shadow-sm bg-light">
                            {{-- mismo tono que usas en otros listados --}}
                            <div class="card-body ">

                                {{-- Título + ref --}}
                                <div class="mb-2">
                                    <div class="fw-semibold">
                                        {{ $solicitud->titulo ?? 'Solicitud #' . $solicitud->id }}
                                    </div>
                                    <div class="small text-muted">
                                        Ref: #{{ $solicitud->id }}
                                    </div>
                                </div>

                                <div class="small text-muted mb-2">
                                    {{-- Cliente --}}
                                    <div>
                                        <strong>Cliente:</strong>
                                        @if ($cliente)
                                            {{ $cliente->nombre ?? $cliente->name }}
                                            {{ $cliente->apellidos ?? '' }}
                                            <br>
                                            <span>{{ $cliente->email }}</span>
                                        @else
                                            <span class="text-muted">Sin cliente</span>
                                        @endif
                                    </div>

                                    {{-- Profesional --}}
                                    <div class="mt-1">
                                        <strong>Profesional:</strong>
                                        @if ($pro)
                                            {{ $pro->empresa }}<br>
                                            @if ($pro->email_empresa)
                                                <span>{{ $pro->email_empresa }}</span><br>
                                            @endif
                                            <span>
                                                {{ $pro->ciudad }}
                                                {{ $pro->provincia ? ' - ' . $pro->provincia : '' }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin asignar</span>
                                        @endif
                                    </div>

                                    {{-- Ubicación --}}
                                    <div class="mt-1">
                                        <strong>Ubicación:</strong>
                                        @if ($solicitud->provincia)
                                            {{ $solicitud->provincia }} -
                                        @endif
                                        {{ $solicitud->ciudad ?? 'No indicada' }}
                                    </div>

                                    {{-- Estado (SOLO AQUÍ EN MÓVIL, no en otra parte) --}}
                                    <div class="mt-1">
                                        <strong>Estado:</strong>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                        </span>
                                    </div>

                                    {{-- Fecha --}}
                                    <div class="mt-1">
                                        <strong>Fecha:</strong>
                                        {{ $solicitud->fecha?->format('d/m/Y H:i') ?? $solicitud->created_at?->format('d/m/Y H:i') }}
                                    </div>
                                </div>

                                {{-- Acciones en columna, ancho completo --}}
                                <div class="d-grid gap-2">
                                    {{-- Ver --}}
                                    <button type="button" class="btn btn-info btn-sm"
                                        @click="openSolicitudAdminModal({{ $solicitud->id }})">
                                        Ver
                                    </button>

                                    {{-- Editar sólo si abierta o en revisión --}}
                                    @if (in_array($solicitud->estado, ['abierta', 'en_revision']))
                                        <a href="{{ route('admin.solicitudes.editar', [$solicitud->id, 'page' => $solicitudes->currentPage()]) }}"
                                            class="btn btn-warning btn-sm">
                                            Editar
                                        </a>
                                    @endif

                                    {{-- Eliminar --}}
                                    @if ($solicitud->estado)
                                        <x-admin.solicitudes.btn_eliminar :solicitud="$solicitud" />
                                    @endif

                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                {{ $solicitudes->links('pagination::bootstrap-5') }}

            @endif

            {{-- Modal Vue para ver solicitud (admin) --}}
            <solicitud-admin-modal ref="solicitudAdminModal"></solicitud-admin-modal>

        </div>
    </div>
@endsection
