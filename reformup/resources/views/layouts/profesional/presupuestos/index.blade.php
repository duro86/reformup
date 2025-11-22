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

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    Mis presupuestos
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
                    'enviado' => 'Enviados',
                    'aceptado' => 'Aceptados',
                    'rechazado' => 'Rechazados',
                    'caducado' => 'Caducados',
                ];
            @endphp

            {{-- Filtros por estado --}}
            <ul class="nav nav-pills mb-3">
                @foreach ($estados as $valor => $texto)
                    @php
                        $isActive = $estado === $valor || (is_null($estado) && is_null($valor));
                        $url = $valor
                            ? route('profesional.presupuestos.index', ['estado' => $valor])
                            : route('profesional.presupuestos.index');
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $isActive ? 'active' : '' }}" href="{{ $url }}">
                            {{ $texto }}
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- Lista de presupuestos --}}
            @if ($presupuestos->isEmpty())
                <div class="alert alert-info">
                    No tienes presupuestos {{ $estado ? 'con estado ' . str_replace('_', ' ', $estado) : 'todavía' }}.
                </div>
            @else
                <div class="table-responsive-md">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Solicitud</th>
                                <th class="d-none d-md-table-cell bg-secondary">Cliente</th>
                                <th class="d-none d-md-table-cell bg-secondary">Importe</th>
                                <th class="d-none d-md-table-cell bg-secondary">Estado</th>
                                <th class="d-none d-md-table-cell bg-secondary">Fecha</th>
                                <th class="text-start d-none d-md-table-cell bg-secondary">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Estados y estilos --}}
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
                                    {{-- Columna principal: Solicitud + bloque móvil --}}
                                    <td>
                                        <strong>
                                            {{ $presu->solicitud->titulo ?? '—' }}
                                        </strong>

                                        {{-- Versión móvil: detalles debajo --}}
                                        <div class="small text-muted d-block d-md-none mt-1">

                                            {{-- Cliente --}}
                                            <div class="mb-1">
                                                <span class="fw-semibold">Cliente:</span>
                                                @if ($cliente)
                                                    {{ $cliente->nombre }} {{ $cliente->apellidos }}
                                                @else
                                                    <span class="text-muted">Sin datos</span>
                                                @endif
                                            </div>

                                            {{-- Importe --}}
                                            <div class="mb-1">
                                                <span class="fw-semibold">Importe:</span>
                                                {{ number_format($presu->total, 2, ',', '.') }} €
                                            </div>

                                            {{-- Estado --}}
                                            <div class="mb-1">
                                                <span class="fw-semibold">Estado:</span>
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ ucfirst($presu->estado) }}
                                                </span>
                                            </div>

                                            {{-- Fecha --}}
                                            <div class="mb-2">
                                                <span class="fw-semibold">Fecha:</span>
                                                {{ $presu->fecha?->format('d/m/Y H:i') ?? $presu->created_at?->format('d/m/Y H:i') }}
                                            </div>

                                            {{-- Acciones (solo móvil) --}}
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @if ($presu->docu_pdf)
                                                    <a href="{{ asset('storage/' . $presu->docu_pdf) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 fw-semibold text-dark px-2 py-1 rounded">
                                                        Ver PDF
                                                    </a>
                                                @else
                                                    <span class="text-muted small me-2">Sin PDF</span>
                                                @endif                                              

                                                {{-- Botón Cancelar presupuesto --}}
                                                @if ($presu->solicitud === 'abierta')
                                                    <x-profesional.presupuestos.btn_cancelar :presupuesto="$presu" />
                                                @endif

                                                @if (
                                                    $presu->estado === 'rechazado' &&
                                                        $presu->solicitud &&
                                                        in_array($presu->solicitud->estado, ['abierta', 'en_revision']))
                                                    <a href="{{ route('profesional.presupuestos.crear_desde_solicitud', $presu->solicitud) }}"
                                                        class="btn btn-primary btn-sm">
                                                        Nuevo presupuesto
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Cliente (solo escritorio/tablet) --}}
                                    <td class="d-none d-md-table-cell">
                                        @if ($cliente)
                                            {{ $cliente->nombre }} {{ $cliente->apellidos }}
                                        @else
                                            <span class="text-muted small">Sin datos</span>
                                        @endif
                                    </td>

                                    {{-- Importe (solo escritorio/tablet) --}}
                                    <td class="d-none d-md-table-cell">
                                        {{ number_format($presu->total, 2, ',', '.') }} €
                                    </td>

                                    {{-- Estado (solo escritorio/tablet) --}}
                                    <td class="d-none d-md-table-cell">
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($presu->estado) }}
                                        </span>
                                    </td>

                                    {{-- Fecha (solo escritorio/tablet) --}}
                                    <td class="d-none d-md-table-cell">
                                        {{ $presu->fecha?->format('d/m/Y H:i') ?? $presu->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- Acciones (solo escritorio/tablet) --}}
                                    <td class="text-start d-none d-md-table-cell">
                                        @if ($presu->docu_pdf)
                                            <a href="{{ asset('storage/' . $presu->docu_pdf) }}"
                                                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 mx-1 fw-semibold text-dark px-2 py-1 rounded"
                                                target="_blank"><i class="bi bi-file-earmark-pdf"></i>
                                                Ver PDF
                                            </a>
                                        @else
                                            <span class="text-muted small me-2">Sin PDF</span>
                                        @endif

                                        {{-- Botón Cancelar presupuesto --}}
                                        @if ($presu->estado === 'enviado')
                                            <x-profesional.presupuestos.btn_cancelar :presupuesto="$presu" />
                                        @endif

                                        {{-- Botón Nuevo presupuesto desde solicitud --}}
                                        @if (
                                            $presu->estado === 'rechazado' &&
                                                $presu->solicitud &&
                                                in_array($presu->solicitud->estado, ['abierta', 'en_revision']))
                                            <a href="{{ route('profesional.presupuestos.crear_desde_solicitud', $presu->solicitud) }}"
                                                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 mx-1 fw-semibold text-dark px-2 py-1 rounded"
                                                target="_blank"><i class="bi bi-file-earmark-pdf"></i>
                                                Nuevo presupuesto
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $presupuestos->links() }}
                </div>
            @endif

        </div>
    </div>

@endsection
