@extends('layouts.main')

@section('title', 'Mis trabajos - Profesional - ReformUp')

@section('content')

    <x-navbar />

    {{-- Sidebar profesional --}}
    <x-profesional.profesional_sidebar />
    <x-profesional.profesional_bienvenido />

    <div class="container-fluid main-content-with-sidebar">
        <x-profesional.nav_movil active="trabajos" />

        <div class="container py-4" id="app">
            <div class="d-flex flex-column flex-md-row align-items-md-center mb-3 gap-2 justify-content-center">
                <h3 class="mb-1 d-flex align-items-center gap-2 justify-content-center">
                    <i class="bi bi-hammer"></i> Mis trabajos
                </h3>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($trabajos->isEmpty())
                <div class="alert alert-info">
                    No tienes trabajos asignados todavía.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Trabajo</th>
                                <th class="d-none d-lg-table-cell">Cliente</th>
                                <th class="d-none d-lg-table-cell">Estado</th>
                                <th class="d-none d-lg-table-cell">Fecha inicio</th>
                                <th class="d-none d-lg-table-cell">Fecha fin</th>
                                <th class="d-none d-lg-table-cell">Dirección obra</th>
                                <th class="d-none d-lg-table-cell">Total presupuesto</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trabajos as $trabajo)
                                @php
                                    $presupuesto = $trabajo->presupuesto;
                                    $solicitud = $presupuesto?->solicitud;
                                    $cliente = $solicitud?->cliente;
                                @endphp

                                <tr>
                                    {{-- Trabajo / referencia + bloque móvil/tablet --}}
                                    <td>
                                        <strong>
                                            @if ($solicitud?->titulo)
                                                {{ $solicitud->titulo }}
                                            @else
                                                Trabajo #{{ $trabajo->id }}
                                            @endif
                                        </strong>

                                        {{-- Versión móvil / tablet (hasta < lg): detalles tipo card --}}
                                        <div class="small text-muted d-block d-lg-none mt-1">

                                            {{-- Cliente --}}
                                            <span class="d-block">
                                                <span class="fw-semibold">Cliente:</span>
                                                @if ($cliente)
                                                    {{ $cliente->nombre ?? $cliente->name }}
                                                    {{ $cliente->apellidos ?? '' }}
                                                    @if ($cliente->email)
                                                        <br><small class="text-muted">{{ $cliente->email }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Sin cliente</span>
                                                @endif
                                            </span>

                                            {{-- Estado --}}
                                            @php
                                                $badgeClass = match ($trabajo->estado) {
                                                    'previsto' => 'bg-primary',
                                                    'en_curso' => 'bg-warning text-dark',
                                                    'finalizado' => 'bg-success',
                                                    'cancelado' => 'bg-secondary',
                                                    default => 'bg-light text-dark',
                                                };
                                            @endphp
                                            <span class="d-block mt-1">
                                                <span class="fw-semibold">Estado:</span>
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ ucfirst(str_replace('_', ' ', $trabajo->estado)) }}
                                                </span>
                                            </span>

                                            {{-- Fechas --}}
                                            <span class="d-block">
                                                <span class="fw-semibold">Inicio:</span>
                                                {{ $trabajo->fecha_ini?->format('d/m/Y H:i') ?? 'Sin iniciar' }}
                                            </span>

                                            <span class="d-block">
                                                <span class="fw-semibold">Fin:</span>
                                                {{ $trabajo->fecha_fin?->format('d/m/Y H:i') ?? 'Sin finalizar' }}
                                            </span>

                                            {{-- Dirección obra --}}
                                            <span class="d-block">
                                                <span class="fw-semibold">Dir. obra:</span>
                                                {{ $trabajo->dir_obra ?? 'No indicada' }}
                                            </span>

                                            {{-- Total presupuesto --}}
                                            <span class="d-block">
                                                <span class="fw-semibold">Total:</span>
                                                @if ($presupuesto?->total)
                                                    {{ number_format($presupuesto->total, 2, ',', '.') }} €
                                                @else
                                                    <span class="text-muted">No indicado</span>
                                                @endif
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Cliente (solo escritorio lg+) --}}
                                    <td class="d-none d-lg-table-cell">
                                        @if ($cliente)
                                            {{ $cliente->nombre ?? $cliente->name }}
                                            {{ $cliente->apellidos ?? '' }}<br>
                                            <small class="text-muted">{{ $cliente->email }}</small>
                                        @else
                                            <span class="text-muted small">Sin cliente</span>
                                        @endif
                                    </td>

                                    {{-- Estado (solo escritorio lg+) --}}
                                    <td class="d-none d-lg-table-cell">
                                        @php
                                            $badgeClass = match ($trabajo->estado) {
                                                'previsto' => 'bg-primary',
                                                'en_curso' => 'bg-warning text-dark',
                                                'finalizado' => 'bg-success',
                                                'cancelado' => 'bg-secondary',
                                                default => 'bg-light text-dark',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $trabajo->estado)) }}
                                        </span>
                                    </td>

                                    {{-- Fechas (solo escritorio lg+) --}}
                                    <td class="d-none d-lg-table-cell">
                                        {{ $trabajo->fecha_ini?->format('d/m/Y H:i') ?? 'Sin iniciar' }}
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        {{ $trabajo->fecha_fin?->format('d/m/Y H:i') ?? 'Sin finalizar' }}
                                    </td>

                                    {{-- Dirección (solo escritorio lg+) --}}
                                    <td class="d-none d-lg-table-cell">
                                        {{ \Illuminate\Support\Str::limit($trabajo->dir_obra ?? 'No indicada', 30, '...') }}
                                    </td>

                                    {{-- Total presupuesto (solo escritorio lg+) --}}
                                    <td class="d-none d-lg-table-cell text-center">
                                        @if ($presupuesto?->total)
                                            {{ number_format($presupuesto->total, 2, ',', '.') }} €
                                        @else
                                            <span class="text-muted small">No indicado</span>
                                        @endif
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-center">
                                        <div
                                            class="d-flex flex-column flex-md-row flex-wrap justify-content-center align-items-center gap-2">

                                            {{-- VER MODAL TRABAJO (Profesional) --}}
                                            <button type="button"
                                                class="btn btn-info btn-sm px-2 py-1 mx-1 d-inline-flex align-items-center gap-1"
                                                @click="openTrabajoProModal({{ $trabajo->id }})">
                                                <i class="bi bi-eye"></i>
                                                Ver
                                            </button>

                                            {{-- PREVISTO: Empezar / Cancelar --}}
                                            @if ($trabajo->estado === 'previsto' && is_null($trabajo->fecha_ini))
                                                {{-- Empezar trabajo --}}
                                                <form action="{{ route('profesional.trabajos.empezar', $trabajo) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-sm px-2 py-1 mx-1">
                                                        Empezar
                                                    </button>
                                                </form>

                                                {{-- Cancelar trabajo (con motivo) --}}
                                                <x-profesional.trabajos.btn_cancelar :trabajo="$trabajo" />

                                                {{-- EN CURSO: Finalizar --}}
                                            @elseif ($trabajo->estado === 'en_curso')
                                                <form action="{{ route('profesional.trabajos.finalizar', $trabajo) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-primary btn-sm px-2 py-1 mx-1">
                                                        Finalizar
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Ver presupuesto PDF --}}
                                            @if ($presupuesto?->docu_pdf)
                                                <a href="{{ asset('storage/' . $presupuesto->docu_pdf) }}"
                                                    class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 mx-1 fw-semibold text-dark px-2 py-1 rounded"
                                                    target="_blank">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                    Ver presupuesto
                                                </a>
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

            {{-- Modal Vue para ver trabajo como profesional --}}
            <trabajo-pro-modal ref="trabajoProModal"></trabajo-pro-modal>
        </div>
    </div>
@endsection

<x-alertas_sweet />
