@extends('layouts.main')

@section('title', 'Mis presupuestos - ReformUp')

@section('content')

    <x-navbar />
    <x-usuario.usuario_sidebar />
    <x-user_bienvenido />
    {{-- NAV SUPERIOR SOLO MÓVIL/TABLET --}}
    <x-usuario.nav_movil active="presupuestos" />

    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-receipt"></i> Mis presupuestos
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
                    null       => 'Todos',
                    'enviado'  => 'Pendientes',
                    'aceptado' => 'Aceptados',
                    'rechazado'=> 'Rechazados',
                    'caducado' => 'Caducados',
                ];
            @endphp

            {{-- Filtros por estado --}}
            <ul class="nav nav-pills mb-3">
                @foreach ($estados as $valor => $texto)
                    @php
                        $isActive = $estado === $valor || (is_null($estado) && is_null($valor));
                        $url = $valor
                            ? route('usuario.presupuestos.index', ['estado' => $valor])
                            : route('usuario.presupuestos.index');
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
                    No tienes presupuestos
                    {{ $estado ? 'con estado ' . str_replace('_', ' ', $estado) : 'todavía' }}.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Solicitud</th>
                                <th class="d-none d-md-table-cell">Profesional</th>
                                <th>Importe</th>
                                <th>Estado</th>
                                <th class="d-none d-md-table-cell">Fecha</th>
                                <th class="text-start">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($presupuestos as $presu)
                                <tr>
                                    {{-- Solicitud + mini-card en móvil --}}
                                    <td>
                                        <strong>{{ $presu->solicitud->titulo ?? '—' }}</strong>

                                        {{-- Versión móvil: info compacta debajo --}}
                                        <div class="small text-muted d-block d-md-none mt-1">
                                            @if ($presu->solicitud && $presu->solicitud->profesional)
                                                <div>Profesional: {{ $presu->solicitud->profesional->empresa }}</div>
                                            @endif

                                            <div>
                                                Fecha:
                                                {{ $presu->fecha?->format('d/m/Y H:i') ?? $presu->created_at?->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Profesional solo en escritorio --}}
                                    <td class="d-none d-md-table-cell">
                                        @if ($presu->solicitud && $presu->solicitud->profesional)
                                            {{ $presu->solicitud->profesional->empresa }}
                                        @else
                                            <span class="text-muted small">Sin datos</span>
                                        @endif
                                    </td>

                                    {{-- Importe --}}
                                    <td>
                                        {{ number_format($presu->total, 2, ',', '.') }} €
                                    </td>

                                    {{-- Estado --}}
                                    <td>
                                        @php
                                            $badgeClass = match ($presu->estado) {
                                                'enviado'  => 'bg-primary',
                                                'aceptado' => 'bg-success',
                                                'rechazado'=> 'bg-danger',
                                                'caducado' => 'bg-secondary',
                                                default    => 'bg-light text-dark',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($presu->estado) }}
                                        </span>
                                    </td>

                                    {{-- Fecha solo en escritorio --}}
                                    <td class="d-none d-md-table-cell">
                                        {{ $presu->fecha?->format('d/m/Y H:i') ?? $presu->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-end">
                                        {{-- Ver PDF si existe --}}
                                        @if ($presu->docu_pdf)
                                            <a href="{{ asset('storage/' . $presu->docu_pdf) }}"
                                               target="_blank"
                                               class="btn btn-outline-secondary btn-sm me-1 mb-1">
                                                Ver presupuesto
                                            </a>
                                        @else
                                            <span class="text-muted small me-2">Sin documento</span>
                                        @endif

                                        {{-- Aceptar / Rechazar solo si está ENVIADO --}}
                                        @if ($presu->estado === 'enviado')
                                            <x-usuario.presupuestos.btn_aceptar
                                                :presupuesto="$presu"
                                                :tiene-direccion="(bool) optional($presu->solicitud)->dir_cliente"
                                            />
                                            <x-usuario.presupuestos.btn_rechazar :presupuesto="$presu" />
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
