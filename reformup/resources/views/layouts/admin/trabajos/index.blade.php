@extends('layouts.main')

@section('title', 'Gestión de trabajos - Admin - ReformUp')

@section('content')

    <x-navbar />
    <x-admin.admin_sidebar />
    {{-- Bienvenida --}}
    <x-admin.admin_bienvenido />

    <div class="container-fluid main-content-with-sidebar">
        {{-- NAV MÓVIL ADMIN --}}
        <x-admin.nav_movil active="trabajos" />

        <div class="container py-4" id="app">

            {{-- Título --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-briefcase"></i>
                    Gestión de trabajos
                </h1>
            </div>

            {{-- Mensajes flash --}}
            <x-alertas.alertasFlash />

            {{-- Buscador combinado: texto + fechas --}}
            <form method="GET" action="{{ route('admin.trabajos') }}" class="row g-2 mb-3">
                {{-- Búsqueda por texto --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                        placeholder="Buscar por solicitud, cliente, profesional, ciudad, provincia o estado...">
                </div>

                {{-- Rango de fechas (por fecha_ini del trabajo, por ejemplo) --}}
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
                        <a href="{{ route('admin.trabajos') }}" class="btn btn-sm btn-outline-secondary">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>

            @php
                $estados = [
                    'previsto' => 'Previstos',
                    'en_curso' => 'En curso',
                    'finalizado' => 'Finalizados',
                    'cancelado' => 'Cancelados',
                ];
            @endphp

            {{-- Filtros por estado Buscador por estado --}}
            <ul class="nav nav-pills mb-3">
                {{-- Todos --}}
                <li class="nav-item">
                    @php
                        $urlTodos = route(
                            'admin.trabajos',
                            array_filter([
                                'q' => request('q'),
                                'fecha_desde' => request('fecha_desde'),
                                'fecha_hasta' => request('fecha_hasta'),
                            ]),
                        );
                    @endphp
                    <a class="nav-link {{ $estado === null ? 'active' : '' }}" href="{{ $urlTodos }}">
                        Todos
                    </a>
                </li>

                {{-- ESTADOS DEL MODELO --}}
                @foreach ($estados as $valor => $texto)
                    @php
                        $urlEstado = route(
                            'admin.trabajos',
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

            @if ($trabajos->isEmpty())
                <div class="alert alert-info">
                    No hay trabajos
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
                                <th>Trabajo / Solicitud</th>
                                <th>Cliente</th>
                                <th>Profesional</th>
                                <th>Ciudad / Provincia</th>
                                <th>Fechas</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trabajos as $trabajo)
                                @php
                                    $presupuesto = $trabajo->presupuesto;
                                    $solicitud = $presupuesto?->solicitud;
                                    $cliente = $solicitud?->cliente;
                                    $pro = $presupuesto?->profesional ?? $solicitud?->profesional;

                                    $badgeClass = match ($trabajo->estado) {
                                        'previsto' => 'bg-secondary',
                                        'en_curso' => 'bg-warning text-dark',
                                        'finalizado' => 'bg-success',
                                        'cancelado' => 'bg-danger',
                                        default => 'bg-light text-dark',
                                    };
                                @endphp

                                <tr>
                                    {{-- Trabajo / Solicitud --}}
                                    <td>
                                        <strong>
                                            @if ($solicitud?->titulo)
                                                {{ $solicitud->titulo }}
                                            @else
                                                Trabajo #{{ $trabajo->id }}
                                            @endif
                                        </strong>
                                        <div class="small text-muted">
                                            Ref. trabajo: #{{ $trabajo->id }}
                                            @if ($solicitud)
                                                · Solicitud #{{ $solicitud->id }}
                                            @endif
                                            @if ($presupuesto)
                                                · Presupuesto #{{ $presupuesto->id }}
                                            @endif
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
                                            <span class="text-muted small">Sin profesional</span>
                                        @endif
                                    </td>

                                    {{-- Ciudad / Provincia --}}
                                    <td>
                                        {{ $solicitud->ciudad ?? 'No indicada' }}
                                        @if ($solicitud?->provincia)
                                            - {{ $solicitud->provincia }}
                                        @endif
                                    </td>

                                    {{-- Fechas --}}
                                    <td>
                                        <div class="small">
                                            <div>
                                                <span class="fw-semibold">Ini:</span>
                                                {{ $trabajo->fecha_ini?->format('d/m/Y H:i') ?? '—' }}
                                            </div>
                                            <div>
                                                <span class="fw-semibold">Fin:</span>
                                                {{ $trabajo->fecha_fin?->format('d/m/Y H:i') ?? '—' }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Estado --}}
                                    <td class="text-center">
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $trabajo->estado)) }}
                                        </span>
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-center">
                                        <div class="d-flex flex-row flex-wrap justify-content-center gap-2">

                                            {{-- Ver (modal Vue) --}}
                                            <button type="button"
                                                class="btn btn-sm btn-info d-inline-flex align-items-center gap-1"
                                                @click="openTrabajoAdminModal({{ $trabajo->id }})">
                                                Ver
                                            </button>

                                            {{-- Editar / Cancelar sólo en previsto o en_curso --}}
                                            @if (in_array($trabajo->estado, ['previsto', 'en_curso']))
                                                <a href="{{ route('admin.trabajos.editar', $trabajo) }}"
                                                    class="btn btn-sm btn-warning d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </a>

                                                <x-admin.trabajos.btn_cancelar :trabajo="$trabajo" />
                                            @endif
                                            @if ($trabajo->estado === 'cancelado' || $trabajo->estado === 'finalizado')
                                                <x-admin.trabajos.btn_eliminar :trabajo="$trabajo" />
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
                    @foreach ($trabajos as $trabajo)
                        @php
                            $presupuesto = $trabajo->presupuesto;
                            $solicitud = $presupuesto?->solicitud;
                            $cliente = $solicitud?->cliente;
                            $pro = $presupuesto?->profesional ?? $solicitud?->profesional;

                            $badgeClass = match ($trabajo->estado) {
                                'previsto' => 'bg-secondary',
                                'en_curso' => 'bg-warning text-dark',
                                'finalizado' => 'bg-success',
                                'cancelado' => 'bg-danger',
                                default => 'bg-light text-dark',
                            };
                        @endphp

                        <div class="card mb-3 shadow-sm bg-light">
                            <div class="card-body ">

                                {{-- Título / refs --}}
                                <div class="mb-2">
                                    <div class="fw-semibold">
                                        @if ($solicitud?->titulo)
                                            {{ $solicitud->titulo }}
                                        @else
                                            Trabajo #{{ $trabajo->id }}
                                        @endif
                                    </div>
                                    <div class="small text-muted">
                                        Ref. trabajo: #{{ $trabajo->id }}
                                        @if ($solicitud)
                                            · Solicitud #{{ $solicitud->id }}
                                        @endif
                                        @if ($presupuesto)
                                            · Presupuesto #{{ $presupuesto->id }}
                                        @endif
                                    </div>
                                </div>

                                <div class="small text-muted mb-2">
                                    {{-- Cliente --}}
                                    <div class="mb-1">
                                        <strong>Cliente:</strong>
                                        @if ($cliente)
                                            {{ $cliente->nombre ?? $cliente->name }}
                                            {{ $cliente->apellidos ?? '' }}
                                            @if ($cliente->email)
                                                <br><span class="text-muted">{{ $cliente->email }}</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Sin cliente</span>
                                        @endif
                                    </div>

                                    {{-- Profesional --}}
                                    <div class="mb-1">
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
                                            <span class="text-muted">Sin profesional</span>
                                        @endif
                                    </div>

                                    {{-- Ubicación --}}
                                    <div class="mb-1">
                                        <strong>Ubicación:</strong>
                                        {{ $solicitud->ciudad ?? 'No indicada' }}
                                        @if ($solicitud?->provincia)
                                            - {{ $solicitud->provincia }}
                                        @endif
                                    </div>

                                    {{-- Fechas --}}
                                    <div class="mb-1">
                                        <strong>Inicio:</strong>
                                        {{ $trabajo->fecha_ini?->format('d/m/Y H:i') ?? 'Sin iniciar' }}
                                    </div>
                                    <div class="mb-1">
                                        <strong>Fin:</strong>
                                        {{ $trabajo->fecha_fin?->format('d/m/Y H:i') ?? 'Sin finalizar' }}
                                    </div>

                                    {{-- Estado --}}
                                    <div class="mb-1">
                                        <strong>Estado:</strong>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $trabajo->estado)) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Acciones en columna, ancho completo --}}
                                <div class="d-grid gap-2">
                                    {{-- Ver --}}
                                    <button type="button" class="btn btn-info btn-sm"
                                        @click="openTrabajoAdminModal({{ $trabajo->id }})">
                                        Ver
                                    </button>

                                    {{-- Editar / Cancelar --}}
                                    @if (in_array($trabajo->estado, ['previsto', 'en_curso']))
                                        <a href="{{ route('admin.trabajos.editar', $trabajo) }}"
                                            class="btn btn-sm btn-warning d-inline-flex align-items-center justify-content-center gap-1">
                                            <i class="bi bi-pencil"></i>
                                            Editar
                                        </a>

                                        <x-admin.trabajos.btn_cancelar :trabajo="$trabajo" contexto="mobile" />
                                    @endif
                                    @if ($trabajo->estado === 'cancelado' || $trabajo->estado === 'finalizado')
                                        <x-admin.trabajos.btn_eliminar :trabajo="$trabajo" />
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="mt-3">
                    {{ $trabajos->links('pagination::bootstrap-5') }}
                </div>
            @endif

            {{-- Modal Vue para ver trabajo (admin) --}}
            <trabajo-admin-modal ref="trabajoAdminModal"></trabajo-admin-modal>

        </div>
    </div>
@endsection
