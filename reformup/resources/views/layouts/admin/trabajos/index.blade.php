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
                    Trabajos
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
                $estados = [
                    null => 'Todos',
                    'previsto' => 'Previstos',
                    'en_curso' => 'En curso',
                    'finalizado' => 'Finalizados',
                    'cancelado' => 'Cancelados',
                ];
            @endphp

            {{-- Filtros por estado --}}
            <ul class="nav nav-pills mb-3">
                @foreach ($estados as $valor => $texto)
                    @php
                        $isActive = $estado === $valor || (is_null($estado) && is_null($valor));
                        $url = $valor ? route('admin.trabajos', ['estado' => $valor]) : route('admin.trabajos');
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $isActive ? 'active' : '' }}" href="{{ $url }}">
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
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                {{-- Móvil: una sola columna principal --}}
                                <th class="d-md-none bg-secondary text-white">Trabajo</th>

                                {{-- Escritorio / tablet --}}
                                <th class="d-none d-md-table-cell bg-secondary text-white">Título / Solicitud</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Cliente</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Profesional</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Ciudad / Provincia</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Fechas</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Estado</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trabajos as $trabajo)
                                @php
                                    $presupuesto = $trabajo->presupuesto;
                                    $solicitud = $presupuesto?->solicitud;
                                    $cliente = $solicitud?->cliente;
                                    $pro = $solicitud?->profesional;

                                    $badgeClass = match ($trabajo->estado) {
                                        'previsto' => 'bg-secondary',
                                        'en_curso' => 'bg-warning text-dark',
                                        'finalizado' => 'bg-success',
                                        'cancelado' => 'bg-danger',
                                        default => 'bg-light text-dark',
                                    };
                                @endphp

                                <tr>
                                    {{-- 🔹 VISTA MÓVIL: TODO en una celda tipo "card" --}}
                                    <td class="d-md-none">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            <div>
                                                {{-- Título / trabajo --}}
                                                <div class="fw-semibold">
                                                    @if ($solicitud?->titulo)
                                                        {{ $solicitud->titulo }}
                                                    @else
                                                        Trabajo #{{ $trabajo->id }}
                                                    @endif
                                                </div>

                                                {{-- Cliente --}}
                                                <div class="small text-muted mt-1">
                                                    <span class="fw-semibold">Cliente:</span>
                                                    @if ($cliente)
                                                        {{ $cliente->nombre ?? $cliente->name }}
                                                        {{ $cliente->apellidos ?? '' }}
                                                    @else
                                                        <span class="text-muted">Sin cliente</span>
                                                    @endif
                                                </div>

                                                {{-- Profesional --}}
                                                <div class="small text-muted">
                                                    <span class="fw-semibold">Profesional:</span>
                                                    @if ($pro)
                                                        {{ $pro->empresa }}
                                                    @else
                                                        <span class="text-muted">Sin asignar</span>
                                                    @endif
                                                </div>
                                                <div class="small text-muted">
                                                    <span class="fw-semibold">Email:</span>
                                                    @if ($pro)
                                                        {{ $pro->email_empresa }}
                                                    @else
                                                        <span class="text-muted">Sin asignar</span>
                                                    @endif
                                                </div>

                                                {{-- Ciudad / provincia --}}
                                                <div class="small text-muted">
                                                    <span class="fw-semibold">Ubicación:</span>
                                                    {{ $solicitud->ciudad ?? 'No indicada' }}
                                                    @if ($solicitud?->provincia)
                                                        - {{ $solicitud->provincia }}
                                                    @endif
                                                </div>

                                                {{-- Fechas --}}
                                                <div class="small text-muted">
                                                    <span class="fw-semibold">Fechas:</span><br>
                                                    <span>
                                                        Ini:
                                                        {{ $trabajo->fecha_ini?->format('d/m/Y H:i') ?? '—' }}
                                                    </span><br>
                                                    <span>
                                                        Fin:
                                                        {{ $trabajo->fecha_fin?->format('d/m/Y H:i') ?? '—' }}
                                                    </span>
                                                </div>

                                                {{-- Estado (solo aquí en móvil) --}}
                                                <div class="mt-1">
                                                    <span class="fw-semibold">Estado:</span>
                                                    <span class="badge {{ $badgeClass }}">
                                                        {{ ucfirst(str_replace('_', ' ', $trabajo->estado)) }}
                                                    </span>
                                                </div>

                                                {{-- Acciones en móvil --}}
                                                <div class="mt-2 d-flex flex-wrap gap-2">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1"
                                                        @click="openTrabajoAdminModal({{ $trabajo->id }})">
                                                        <i class="bi bi-eye"></i> Ver
                                                    </button>

                                                    @if (in_array($trabajo->estado, ['previsto', 'en_curso']))
                                                        <a href="{{ route('admin.trabajos.editar', $trabajo) }}"
                                                            class="btn btn-sm btn-warning d-inline-flex align-items-center gap-1">
                                                            <i class="bi bi-pencil"></i> Editar
                                                        </a>
                                                        <x-admin.trabajos.btn_cancelar :trabajo="$trabajo" />
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 🔹 VISTA ESCRITORIO / TABLET (md+) --}}

                                    {{-- Título / solicitud --}}
                                    <td class="d-none d-md-table-cell">
                                        <strong>
                                            @if ($solicitud?->titulo)
                                                {{ $solicitud->titulo }}
                                            @else
                                                Trabajo #{{ $trabajo->id }}
                                            @endif
                                        </strong>
                                        <div class="small text-muted">
                                            Ref. trabajo: #{{ $trabajo->id }}
                                            @if ($presupuesto)
                                                — Presupuesto #{{ $presupuesto->id }}
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Cliente --}}
                                    <td class="d-none d-md-table-cell">
                                        @if ($cliente)
                                            {{ $cliente->nombre ?? $cliente->name }}
                                            {{ $cliente->apellidos ?? '' }}<br>
                                            <small class="text-muted">{{ $cliente->email }}</small>
                                        @else
                                            <span class="text-muted small">Sin cliente</span>
                                        @endif
                                    </td>

                                    {{-- Profesional --}}
                                    <td class="d-none d-md-table-cell">
                                        @if ($pro)
                                            {{ $pro->empresa }}<br>
                                            {{ $pro->email_empresa }}<br>
                                            <small class="text-muted">
                                                {{ $pro->ciudad }}
                                                {{ $pro->provincia ? ' - ' . $pro->provincia : '' }}
                                            </small>
                                        @else
                                            <span class="text-muted small">Sin profesional</span>
                                        @endif
                                    </td>

                                    {{-- Ciudad / provincia --}}
                                    <td class="d-none d-md-table-cell">
                                        {{ $solicitud->ciudad ?? 'No indicada' }}
                                        @if ($solicitud?->provincia)
                                            - {{ $solicitud->provincia }}
                                        @endif
                                    </td>

                                    {{-- Fechas --}}
                                    <td class="d-none d-md-table-cell">
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

                                    {{-- Estado (solo escritorio / tablet) --}}
                                    <td class="d-none d-md-table-cell">
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $trabajo->estado)) }}
                                        </span>
                                    </td>

                                    {{-- Acciones (solo escritorio / tablet) --}}
                                    <td class="d-none d-md-table-cell text-center">
                                        <div class="d-flex flex-column flex-md-row flex-wrap justify-content-center gap-2">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1"
                                                @click="openTrabajoAdminModal({{ $trabajo->id }})">
                                                <i class="bi bi-eye"></i> Ver
                                            </button>

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

                <div class="mt-3">
                    {{ $trabajos->links() }}
                </div>
            @endif

            <trabajo-admin-modal ref="trabajoAdminModal"></trabajo-admin-modal>
        </div>
    </div>
@endsection
