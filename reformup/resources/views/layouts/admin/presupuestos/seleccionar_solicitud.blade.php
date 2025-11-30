@extends('layouts.main')

@section('title', 'Seleccionar solicitud - Admin - ReformUp')

@section('content')

    <x-navbar />
    <x-admin.admin_sidebar />
    <x-admin.nav_movil active="presupuestos" />

    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-plus"></i>
                    Seleccionar solicitud para nuevo presupuesto
                </h1>

            <a href="{{ route('admin.presupuestos') }}"
               class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-arrow-left"></i>
                    Volver a presupuestos
                </a>
            </div>

            {{-- Mensajes flash --}}
            <x-alertas.alertasFlash />

            @if ($solicitudes->isEmpty())
                <div class="alert alert-info">
                    No hay solicitudes abiertas o en revisión disponibles para crear un nuevo presupuesto.
                </div>
            @else
                <div class="table-responsive-md">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th class="bg-secondary text-white">Solicitud</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Cliente</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Profesional</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Ubicación</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Fecha</th>
                                <th class="bg-secondary text-white text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($solicitudes as $solicitud)
                                @php
                                    $cliente = $solicitud->cliente;
                                    $pro     = $solicitud->profesional;
                                @endphp

                                <tr>
                                    {{-- Columna principal + versión móvil --}}
                                    <td>
                                        <strong>{{ $solicitud->titulo ?? 'Solicitud #' . $solicitud->id }}</strong>
                                        <div class="small text-muted">
                                            Ref: #{{ $solicitud->id }}
                                        </div>

                                        {{-- Versión móvil --}}
                                        <div class="small text-muted d-block d-md-none mt-2">
                                            <div class="mb-1">
                                                <span class="fw-semibold">Cliente:</span>
                                                @if ($cliente)
                                                    {{ $cliente->nombre ?? $cliente->name }}
                                                    {{ $cliente->apellidos ?? '' }}
                                                @else
                                                    <span class="text-muted">Sin datos</span>
                                                @endif
                                            </div>
                                            <div class="mb-1">
                                                <span class="fw-semibold">Profesional:</span>
                                                @if ($pro)
                                                    {{ $pro->empresa }}
                                                @else
                                                    <span class="text-muted">Sin asignar</span>
                                                @endif
                                            </div>
                                            <div class="mb-1">
                                                <span class="fw-semibold">Ubicación:</span>
                                                {{ $solicitud->ciudad ?? 'No indicada' }}
                                                @if($solicitud->provincia)
                                                    - {{ $solicitud->provincia }}
                                                @endif
                                            </div>
                                            <div class="mb-1">
                                                <span class="fw-semibold">Fecha:</span>
                                                {{ $solicitud->fecha?->format('d/m/Y H:i') ?? $solicitud->created_at?->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Cliente (md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        @if ($cliente)
                                            {{ $cliente->nombre ?? $cliente->name }}
                                            {{ $cliente->apellidos ?? '' }}<br>
                                            <small class="text-muted">{{ $cliente->email }}</small>
                                        @else
                                            <span class="text-muted small">Sin datos</span>
                                        @endif
                                    </td>

                                    {{-- Profesional (md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        @if ($pro)
                                            {{ $pro->empresa }}<br>
                                            @if($pro->email_empresa)
                                                <small class="text-muted">{{ $pro->email_empresa }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted small">Sin asignar</span>
                                        @endif
                                    </td>

                                    {{-- Ubicación (md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        {{ $solicitud->ciudad ?? 'No indicada' }}
                                        @if($solicitud->provincia)
                                            - {{ $solicitud->provincia }}
                                        @endif
                                    </td>

                                    {{-- Fecha (md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        {{ $solicitud->fecha?->format('d/m/Y H:i') ?? $solicitud->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-center">
                                        <a href="{{ route('admin.presupuestos.crear', $solicitud) }}"
                                           class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-receipt"></i>
                                            Crear presupuesto
                                        </a>
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
