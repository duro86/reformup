@extends('layouts.main')

@section('title', 'Gestión de trabajos - Admin - ReformUp')

@section('content')

    <x-navbar />
    <x-admin.admin_sidebar />

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
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @php
                // Si prefieres, esto puede venir del controlador
                $estados = [
                    'previsto' => 'Previstos',
                    'en_curso' => 'En curso',
                    'finalizado' => 'Finalizados',
                    'cancelado' => 'Cancelados',
                ];
            @endphp

            {{-- Buscador --}}
            <x-buscador-q :action="route('admin.trabajos')" placeholder="Buscar por título, cliente, profesional, ciudad, estado..." />

            {{-- Filtros por estado --}}
            <ul class="nav nav-pills mb-3">
                {{-- Todos --}}
                <li class="nav-item">
                    @php
                        $urlTodos = route(
                            'admin.trabajos',
                            array_filter([
                                'q' => request('q'),
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

                        <div class="card mb-3 shadow-sm">
                            <div class="card-body bg-light">

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
