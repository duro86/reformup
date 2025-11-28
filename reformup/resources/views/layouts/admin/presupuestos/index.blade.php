@extends('layouts.main')

@section('title', 'Gestión de presupuestos - Admin - ReformUp')

@section('content')

    <x-navbar />
    <x-admin.admin_sidebar />

    <div class="container-fluid main-content-with-sidebar">

        {{-- Nav móvil admin --}}
        <x-admin.nav_movil active="presupuestos" />

        <div class="container py-4" id="app">

            {{-- Título + botón nuevo --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text"></i> Gestión de presupuestos
                </h1>

                <a href="{{ route('admin.presupuestos.seleccionar_solicitud') }}"
                    class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-plus-circle"></i>
                    Nuevo presupuesto
                </a>
            </div>

            {{-- Mensajes flash --}}
            <x-alertas.alertasFlash />

            {{-- Buscador combinado: campos + fechas --}}
            <form method="GET" action="{{ route('admin.presupuestos') }}" class="row g-2 mb-3">
                {{-- Búsqueda por texto --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                        placeholder="Buscar por ref., solicitud, cliente, profesional o ubicación...">
                </div>

                {{-- Rango de fechas reutilizable --}}
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
                        <a href="{{ route('admin.presupuestos') }}" class="btn btn-sm btn-outline-secondary">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>


            {{-- Filtros por estado --}}
            @if (isset($estados) && is_array($estados))
                <ul class="nav nav-pills mb-3">
                    @foreach ($estados as $valor => $texto)
                        @php
                            $isActive = $estado === $valor || (is_null($estado) && is_null($valor));
                            // Conservamos la query 'q' al cambiar de estado
                            $url = $valor
                                ? route(
                                    'admin.presupuestos',
                                    array_merge(request()->except('page'), ['estado' => $valor]),
                                )
                                : route('admin.presupuestos', request()->except('page', 'estado'));
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link {{ $isActive ? 'active' : '' }}" href="{{ $url }}">
                                {{ $texto }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($presupuestos->isEmpty())
                <div class="alert alert-info">
                    No hay presupuestos registrados
                    {{ $estado ? 'con estado ' . str_replace('_', ' ', $estado) : 'todavía' }}.
                </div>
            @else
                {{-- ===================================================== --}}
                {{-- TABLA SOLO ESCRITORIO (lg+)                          --}}
                {{-- ===================================================== --}}
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr class="fs-6">
                                <th>Presupuesto / Solicitud</th>
                                <th>Cliente</th>
                                <th>Profesional</th>
                                <th>Ciudad / Provincia</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th class="text-center">Doc / Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($presupuestos as $presupuesto)
                                @php
                                    $solicitud = $presupuesto->solicitud;
                                    $cliente = $solicitud?->cliente;
                                    $pro = $presupuesto->profesional;

                                    $badgeClass = match ($presupuesto->estado) {
                                        'enviado' => 'bg-primary',
                                        'aceptado' => 'bg-success',
                                        'rechazado' => 'bg-danger',
                                        'cancelado' => 'bg-secondary',
                                        'caducado' => 'bg-dark',
                                        default => 'bg-light text-dark',
                                    };
                                @endphp

                                <tr>
                                    {{-- Presupuesto / Solicitud --}}
                                    <td>
                                        <strong>
                                            @if ($solicitud && $solicitud->titulo)
                                                {{ $solicitud->titulo }}
                                            @else
                                                Presupuesto #{{ $presupuesto->id }}
                                            @endif
                                        </strong>
                                        <div class="small text-muted">
                                            Ref: #{{ $presupuesto->id }}
                                            @if ($solicitud)
                                                · Solicitud #{{ $solicitud->id }}
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
                                            <span class="text-muted small">Sin asignar</span>
                                        @endif
                                    </td>

                                    {{-- Ciudad / Provincia --}}
                                    <td>
                                        @if ($solicitud)
                                            {{ $solicitud->ciudad ?? 'No indicada' }}
                                            @if ($solicitud->provincia)
                                                - {{ $solicitud->provincia }}
                                            @endif
                                        @else
                                            <span class="text-muted small">No indicada</span>
                                        @endif
                                    </td>

                                    {{-- Total --}}
                                    <td>
                                        {{ number_format($presupuesto->total, 2, ',', '.') }} €
                                    </td>

                                    {{-- Estado --}}
                                    <td>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $presupuesto->estado)) }}
                                        </span>
                                    </td>

                                    {{-- Fecha --}}
                                    <td>
                                        {{ $presupuesto->fecha?->format('d/m/Y H:i') ?? $presupuesto->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- Doc / Acciones --}}
                                    <td class="text-center">
                                        <div class="d-flex flex-row flex-wrap gap-1 justify-content-center mb-1">

                                            {{-- Ver (modal Vue) --}}
                                            <button type="button"
                                                class="btn btn-info btn-sm px-2 py-1 d-inline-flex align-items-center gap-1"
                                                @click="openPresupuestoAdminModal({{ $presupuesto->id }})">
                                                Ver
                                            </button>

                                            {{-- Editar sólo si ENVIADO --}}
                                            @if ($presupuesto->estado === 'enviado')
                                                <a href="{{ route('admin.presupuestos.editar', $presupuesto) }}"
                                                    class="btn btn-warning btn-sm px-2 py-1 d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </a>

                                                {{-- Cancelar --}}
                                                <x-admin.presupuestos.btn_cancelar :presupuesto="$presupuesto" />
                                            @endif

                                            {{-- Botón ELIMINAR para admin --}}
                                            @if (in_array($presupuesto->estado, ['aceptado', 'rechazado']))
                                                <x-admin.presupuestos.btn_eliminar :presupuesto="$presupuesto" />
                                            @endif
                                        </div>

                                        {{-- PDF --}}
                                        @if ($presupuesto->docu_pdf)
                                            <a href="{{ route('presupuestos.ver_pdf', $presupuesto) }}" target="_blank"
                                                class="btn btn-sm btn-outline-secondary px-2 py-1 d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-file-earmark-pdf"></i> PDF
                                            </a>
                                        @else
                                            <span class="text-muted small d-block">Sin PDF</span>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ===================================================== --}}
                {{-- VISTA CARDS MÓVIL/TABLET (xs–lg)                    --}}
                {{-- ===================================================== --}}
                <div class="d-block d-lg-none">
                    @foreach ($presupuestos as $presupuesto)
                        @php
                            $solicitud = $presupuesto->solicitud;
                            $cliente = $solicitud?->cliente;
                            $pro = $presupuesto->profesional;

                            $badgeClass = match ($presupuesto->estado) {
                                'enviado' => 'bg-primary',
                                'aceptado' => 'bg-success',
                                'rechazado' => 'bg-danger',
                                'cancelado' => 'bg-secondary',
                                'caducado' => 'bg-dark',
                                default => 'bg-light text-dark',
                            };
                        @endphp

                        <div class="card mb-3 shadow-sm">
                            <div class="card-body bg-light">

                                {{-- Título + refs --}}
                                <div class="mb-2">
                                    <div class="fw-semibold">
                                        @if ($solicitud && $solicitud->titulo)
                                            {{ $solicitud->titulo }}
                                        @else
                                            Presupuesto #{{ $presupuesto->id }}
                                        @endif
                                    </div>
                                    <div class="small text-muted">
                                        Ref presupuesto: #{{ $presupuesto->id }}
                                        @if ($solicitud)
                                            · Solicitud #{{ $solicitud->id }}
                                        @endif
                                    </div>
                                </div>

                                <div class="small text-muted mb-2">

                                    {{-- Cliente --}}
                                    <div>
                                        <strong>Cliente:</strong>
                                        @if ($cliente)
                                            {{ $cliente->nombre ?? $cliente->name }}
                                            {{ $cliente->apellidos ?? '' }}<br>
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
                                        @if ($solicitud)
                                            {{ $solicitud->ciudad ?? 'No indicada' }}
                                            @if ($solicitud->provincia)
                                                - {{ $solicitud->provincia }}
                                            @endif
                                        @else
                                            <span class="text-muted">No indicada</span>
                                        @endif
                                    </div>

                                    {{-- Total --}}
                                    <div class="mt-1">
                                        <strong>Total:</strong>
                                        {{ number_format($presupuesto->total, 2, ',', '.') }} €
                                    </div>

                                    {{-- Estado --}}
                                    <div class="mt-1">
                                        <strong>Estado:</strong>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $presupuesto->estado)) }}
                                        </span>
                                    </div>

                                    {{-- Fecha --}}
                                    <div class="mt-1">
                                        <strong>Fecha:</strong>
                                        {{ $presupuesto->fecha?->format('d/m/Y H:i') ?? $presupuesto->created_at?->format('d/m/Y H:i') }}
                                    </div>

                                    {{-- Documento --}}
                                    <div class="mt-1">
                                        <strong>Documento:</strong>
                                        @if ($presupuesto->docu_pdf)
                                            <a href="{{ route('presupuestos.ver_pdf', $presupuesto) }}"target="_blank"
                                                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 mt-1">
                                                <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                                            </a>
                                        @else
                                            <span class="text-muted">Sin PDF</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Acciones en columna --}}
                                <div class="d-grid gap-2">
                                    {{-- Ver --}}
                                    <button type="button" class="btn btn-info btn-sm"
                                        @click="openPresupuestoAdminModal({{ $presupuesto->id }})">
                                        Ver
                                    </button>

                                    {{-- Editar / Cancelar sólo si ENVIADO --}}
                                    @if ($presupuesto->estado === 'enviado')
                                        <a href="{{ route('admin.presupuestos.editar', $presupuesto) }}"
                                            class="btn btn-warning btn-sm">
                                            Editar
                                        </a>

                                        <x-admin.presupuestos.btn_cancelar :presupuesto="$presupuesto" />
                                    @endif
                                    {{-- Botón ELIMINAR para admin --}}
                                    @if (in_array($presupuesto->estado, ['aceptado', 'rechazado']))
                                        <x-admin.presupuestos.btn_eliminar :presupuesto="$presupuesto" />
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                {{ $presupuestos->links('pagination::bootstrap-5') }}

            @endif

            {{-- Modal Vue para ver presupuesto (admin) --}}
            <presupuesto-admin-modal ref="presupuestoAdminModal"></presupuesto-admin-modal>

        </div>
    </div>
@endsection
