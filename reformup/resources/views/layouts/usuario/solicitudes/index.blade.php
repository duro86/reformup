@extends('layouts.main')

@section('title', 'Mis solicitudes - ReformUp')

@section('content')

    <x-navbar />

    {{-- SIDEBAR FIJO (escritorio) --}}
    <x-usuario.usuario_sidebar />
    {{-- BIENVENIDA (se ve igual en todos los tamaños) --}}
    <x-usuario.user_bienvenido />
    

    <div class="container-fluid main-content-with-sidebar">
        <x-usuario.nav_movil active="solicitudes" />
        <div class="container py-4">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text"></i> Mis solicitudes
                </h1>

                <a href="{{ route('usuario.solicitudes.crear') }}"
                    class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-plus-circle"></i>
                    Nueva solicitud
                </a>
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
                            ? route('usuario.solicitudes.index', ['estado' => $valor])
                            : route('usuario.solicitudes.index');
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
                    No tienes solicitudes {{ $estado ? 'con estado ' . str_replace('_', ' ', $estado) : 'todavía' }}.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th class="d-none d-md-table-cell">Empresa</th>
                                <th class="d-none d-md-table-cell">Ciudad / Provincia</th>
                                <th>Estado</th>
                                <th class="d-none d-md-table-cell">Presupuesto máx.</th>
                                <th class="d-none d-md-table-cell">Fecha</th>
                                <th class="text-start">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($solicitudes as $solicitud)
                                <tr>
                                    {{-- Título + bloque "card" en móvil --}}
                                    <td>
                                        <strong>{{ $solicitud->titulo }}</strong>

                                        {{-- Versión móvil: detalles debajo --}}
                                        <div class="small text-muted d-block d-md-none mt-1">
                                            {{-- Empresa (si la hay) --}}
                                            @if ($solicitud->profesional)
                                                <span class="d-block">
                                                    <span class="fw-semibold">Empresa:</span>
                                                    {{ $solicitud->profesional->empresa }}
                                                </span>
                                            @endif

                                            {{-- Ciudad / provincia --}}
                                            <span class="d-block">
                                                <span class="fw-semibold">Ubicación:</span>
                                                {{ $solicitud->ciudad }}
                                                {{ $solicitud->provincia ? ' - ' . $solicitud->provincia : '' }}
                                            </span>

                                            {{-- Dirección (asumo dir_empresa; cambia si tu campo se llama distinto) --}}
                                            @if ($solicitud->dir_empresa)
                                                <span class="d-block">
                                                    <span class="fw-semibold">Dirección:</span>
                                                    {{ $solicitud->dir_empresa }}
                                                </span>
                                            @endif

                                            {{-- Presupuesto máx. --}}
                                            <span class="d-block">
                                                <span class="fw-semibold">Presupuesto máx.:</span>
                                                @if ($solicitud->presupuesto_max)
                                                    {{ number_format($solicitud->presupuesto_max, 2, ',', '.') }} €
                                                @else
                                                    No indicado
                                                @endif
                                            </span>

                                            {{-- Fecha --}}
                                            <span class="d-block">
                                                <span class="fw-semibold">Fecha:</span>
                                                {{ $solicitud->fecha?->format('d/m/Y H:i') ?? $solicitud->created_at?->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Empresa (solo escritorio) --}}
                                    <td class="d-none d-md-table-cell">
                                        @if ($solicitud->profesional)
                                            {{ $solicitud->profesional->empresa }}
                                        @else
                                            <span class="text-muted small">Sin asignar</span>
                                        @endif
                                    </td>

                                    {{-- Ciudad / provincia (solo escritorio) --}}
                                    <td class="d-none d-md-table-cell">
                                        {{ $solicitud->ciudad }}
                                        {{ $solicitud->provincia ? ' - ' . $solicitud->provincia : '' }}
                                    </td>

                                    {{-- Estado --}}
                                    <td>
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
                                    </td>

                                    {{-- Presupuesto máx. (solo escritorio) --}}
                                    <td class="d-none d-md-table-cell">
                                        @if ($solicitud->presupuesto_max)
                                            {{ number_format($solicitud->presupuesto_max, 2, ',', '.') }} €
                                        @else
                                            <span class="text-muted small">No indicado</span>
                                        @endif
                                    </td>

                                    {{-- Fecha (solo escritorio) --}}
                                    <td class="d-none d-md-table-cell">
                                        {{ $solicitud->fecha?->format('d/m/Y H:i') ?? $solicitud->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-end">
                                        <x-usuario.solicitudes.btn_eliminar :solicitud="$solicitud" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $solicitudes->links() }}
                </div>
            @endif

        </div>
    </div>

@endsection

<x-alertas_sweet />
