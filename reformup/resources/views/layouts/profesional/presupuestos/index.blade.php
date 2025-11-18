@extends('layouts.main')

@section('title', 'Mis presupuestos - ReformUp')

@section('content')

    <x-navbar />
    <x-profesional.profesional_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    Mis presupuestos
                </h1>
            </div>

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

            @if ($presupuestos->isEmpty())
                <div class="alert alert-info">
                    No tienes presupuestos {{ $estado ? 'con estado ' . str_replace('_', ' ', $estado) : 'todavía' }}.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Solicitud</th>
                                <th>Cliente</th>
                                <th>Importe</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($presupuestos as $presu)
                                <tr>
                                    <td>
                                        {{ $presu->solicitud->titulo ?? '—' }}
                                    </td>
                                    <td>
                                        @if ($presu->solicitud && $presu->solicitud->cliente)
                                            {{ $presu->solicitud->cliente->nombre }}
                                            {{ $presu->solicitud->cliente->apellidos }}
                                        @else
                                            <span class="text-muted small">Sin datos</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ number_format($presu->total, 2, ',', '.') }} €
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match ($presu->estado) {
                                                'enviado' => 'bg-primary',
                                                'aceptado' => 'bg-success',
                                                'rechazado' => 'bg-danger',
                                                'caducado' => 'bg-secondary',
                                                default => 'bg-light text-dark',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($presu->estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $presu->fecha?->format('d/m/Y H:i') ?? $presu->created_at?->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-end">

                                        {{-- Ver PDF si existe --}}
                                        @if ($presu->docu_pdf)
                                            <a href="{{ asset('storage/' . $presu->docu_pdf) }}" target="_blank"
                                                class="btn btn-outline-secondary btn-sm me-1 mb-1">
                                                Ver PDF
                                            </a>
                                        @else
                                            <span class="text-muted small me-2">Sin PDF</span>
                                        @endif

                                        {{-- Cancelar presupuesto (solo si está ENVIADO) --}}
                                        @if ($presu->estado === 'enviado')
                                            <x-profesional.presupuestos.btn_cancelar :presupuesto="$presu" />
                                        @endif

                                        {{-- Volver a enviar / crear nuevo presupuesto
         solo si está RECHAZADO y la solicitud no está cerrada/cancelada --}}
                                        @if (
                                            $presu->estado === 'rechazado' &&
                                                $presu->solicitud &&
                                                in_array($presu->solicitud->estado, ['abierta', 'en_revision']))
                                            <a href="{{ route('profesional.presupuestos.crear_desde_solicitud', $presu->solicitud) }}"
                                                class="btn btn-primary btn-sm ms-1 mb-1">
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
