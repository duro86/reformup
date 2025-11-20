@extends('layouts.main')

@section('title', 'Solicitudes recibidas - ReformUp')

@section('content')

    <x-navbar />

    <x-profesional.profesional_sidebar />
    <x-profesional.profesional_bienvenido />

    {{-- Contenedor Principal --}}
    <div class="container-fluid main-content-with-sidebar">
        <x-profesional.nav_movil active="solicitudes" />

        <div class="container py-4" id="app">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-inbox"></i>
                    Solicitudes recibidas
                </h1>

                <div class="text-muted small">
                    @if ($perfil)
                        {{ $perfil->empresa }}
                    @endif
                </div>
            </div>

            {{-- Mensajes flash --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Filtros por estado --}}
            @php
                $estados = [
                    null => 'Todas',
                    'abierta' => 'Abiertas',
                    'en_revision' => 'En revisión',
                    'cerrada' => 'Cerradas',
                    'cancelada' => 'Canceladas',
                ];
            @endphp

            <ul class="nav nav-pills mb-3">
                @foreach ($estados as $valor => $texto)
                    @php
                        $isActive = $estado === $valor || (is_null($estado) && is_null($valor));
                        $url = $valor
                            ? route('profesional.solicitudes.index', ['estado' => $valor])
                            : route('profesional.solicitudes.index');
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $isActive ? 'active' : '' }}" href="{{ $url }}">
                            {{ $texto }}
                        </a>
                    </li>
                @endforeach
            </ul>

            @if ($solicitudes->isEmpty())
                <div class="alert alert-info">
                    No tienes solicitudes
                    {{ $estado ? 'con estado ' . str_replace('_', ' ', $estado) : 'todavía' }}.
                </div>
            @else
                {{-- Tabla Cliente --}}
                <div class="table-responsive-md">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                {{-- Solo móvil: un único encabezado --}}
                                <th class="d-md-none">Solicitud</th>

                                {{-- Escritorio / tablet: columnas normales --}}
                                <th class="d-none d-md-table-cell bg-secondary">Cliente</th>
                                <th class="d-none d-md-table-cell bg-secondary">Título</th>
                                <th class="d-none d-md-table-cell bg-secondary">Fecha</th>
                                <th class="d-none d-md-table-cell bg-secondary">Estado</th>
                                <th class="d-none d-md-table-cell text-end bg-secondary">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($solicitudes as $solicitud)
                                @php
                                    $cliente = $solicitud->cliente;
                                @endphp
                                <tr>
                                    {{-- ✅ VISTA MÓVIL: CARD EN UNA SOLA CELDA --}}
                                    <td class="d-md-none">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            {{-- Columna izquierda: datos --}}
                                            <div>
                                                {{-- Cliente --}}
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
                                                    <div class="text-muted small">Cliente no disponible</div>
                                                @endif

                                                {{-- Título --}}
                                                <div class="mt-2">
                                                    <span class="fw-semibold">Título:</span>
                                                    <span>{{ $solicitud->titulo }}</span>
                                                </div>

                                                {{-- Fecha --}}
                                                <div>
                                                    <span class="fw-semibold">Fecha:</span>
                                                    <span>
                                                        {{ optional($solicitud->fecha ?? $solicitud->created_at)->format('d/m/Y H:i') }}
                                                    </span>
                                                </div>

                                                {{-- Estado --}}
                                                <div>
                                                    <span class="fw-semibold">Estado:</span>
                                                    @php
                                                        $badgeClass = match ($solicitud->estado) {
                                                            'abierta' => 'bg-primary',
                                                            'en_revision' => 'bg-warning text-dark',
                                                            'cerrada' => 'bg-success',
                                                            'cancelada' => 'bg-secondary',
                                                            default => 'bg-light text-dark',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">
                                                        {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Columna derecha: acciones --}}
                                            <div class="text-end">
                                                {{-- Aquí tus botones --}}
                                                <button type="button" class="btn btn-sm btn-info mb-1"
                                                    @click="openSolicitudModal({{ $solicitud->id }})">
                                                    Ver
                                                </button>

                                                {{-- Ejemplo de otro botón --}}
                                                {{-- <x-profesional.solicitudes.btn_cancelar :solicitud="$solicitud" /> --}}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- ✅ VISTA TABLET/ESCRITORIO: CELDAS NORMALES --}}
                                    <td class="d-none d-md-table-cell">
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

                                    <td class="d-none d-md-table-cell">
                                        {{ $solicitud->titulo }}
                                    </td>

                                    <td class="d-none d-md-table-cell">
                                        {{ optional($solicitud->fecha ?? $solicitud->created_at)->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="d-none d-md-table-cell">
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                        </span>
                                    </td>

                                    <td class="d-none d-md-table-cell text-end">
                                        <button type="button" class="btn btn-sm btn-info me-1 mb-1"
                                            @click="openSolicitudModal({{ $solicitud->id }})">
                                            Ver
                                        </button>

                                        {{-- Más acciones aquí --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
                {{-- Paginación --}}
                <div class="mt-3">
                    {{ $solicitudes->links() }}
                </div>
            @endif

            {{-- Modal Vue --}}
            <solicitud-modal ref="solicitudModal"></solicitud-modal>
        </div>
    </div>
@endsection
