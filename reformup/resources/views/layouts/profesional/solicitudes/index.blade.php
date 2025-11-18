@extends('layouts.main')

@section('title', 'Solicitudes recibidas - ReformUp')

@section('content')

    <x-navbar />

    <x-profesional.profesional_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        <x-user_bienvenido />
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
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Título</th>
                                <th class="d-none d-md-table-cell">Ciudad / Provincia</th>
                                <th>Estado</th>
                                <th class="d-none d-md-table-cell">Presupuesto máx.</th>
                                <th>Fecha</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($solicitudes as $solicitud)
                                <tr>
                                    {{-- Cliente --}}
                                    <td>
                                        @if ($solicitud->cliente)
                                            <strong>{{ $solicitud->cliente->nombre }}
                                                {{ $solicitud->cliente->apellidos }}</strong>
                                            <div class="small text-muted">
                                                {{ $solicitud->cliente->email }}<br>
                                                {{ $solicitud->cliente->telefono }}
                                            </div>
                                        @else
                                            <span class="text-muted small">Sin datos cliente</span>
                                        @endif
                                    </td>

                                    {{-- Título + info extra en móvil --}}
                                    <td>
                                        <strong>{{ $solicitud->titulo }}</strong>
                                        <div class="small text-muted d-block d-md-none mt-1">
                                            {{ $solicitud->ciudad }}
                                            {{ $solicitud->provincia ? ' - ' . $solicitud->provincia : '' }}
                                        </div>
                                    </td>

                                    {{-- Ciudad / provincia en escritorio --}}
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

                                    {{-- Presupuesto max --}}
                                    <td class="d-none d-md-table-cell">
                                        @if ($solicitud->presupuesto_max)
                                            {{ number_format($solicitud->presupuesto_max, 2, ',', '.') }} €
                                        @else
                                            <span class="text-muted small">No indicado</span>
                                        @endif
                                    </td>

                                    {{-- Fecha --}}
                                    <td>
                                        {{ $solicitud->fecha?->format('d/m/Y H:i') ?? $solicitud->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-end">

                                        {{-- VER (modal Vue) - AZUL --}}
                                        <button class="btn btn-info btn-sm px-2 py-1 mx-1"
                                            @click="openSolicitudModal({{ $solicitud->id }})">
                                            Ver
                                        </button>

                                        {{-- Sólo si se puede actuar sobre la solicitud --}}
                                        @if (in_array($solicitud->estado, ['abierta']))
                                            {{-- PRESUPUESTO - VERDE --}}
                                            <a href="{{ route('profesional.presupuestos.crear_desde_solicitud', $solicitud->id) }}"
                                                class="btn btn-success btn-sm px-2 py-1 mx-1">
                                                Enviar presupuesto
                                            </a>

                                            {{-- CANCELAR - ROJO (SweetAlert desde el componente) --}}
                                            <x-profesional.solicitudes.btn_cancelar :solicitud="$solicitud" />
                                        @else
                                            <span class="btn btn-success btn-sm px-2 py-1 mx-1 disabled" role="button"
                                                aria-disabled="true">
                                                Presupuesto enviado
                                            </span>
                                        @endif

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

            {{-- Modal Vue --}}
            <solicitud-modal ref="solicitudModal"></solicitud-modal>
        </div>
    </div>
@endsection
