@extends('layouts.main')

@section('title', 'Gestión de solicitudes - Admin - ReformUp')

@section('content')

    <x-navbar />
    <x-admin.admin_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        {{-- NAV MÓVIL ADMIN --}}
        <x-admin.nav_movil active="solicitudes" />

        <div class="container py-4" id="app">

            {{-- Título + feedback --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text"></i> Mis solicitudes
                </h1>

                <a href="{{ route('admin.solicitudes.crear') }}"
                    class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-plus-circle"></i>
                    Nueva solicitud
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($solicitudes->isEmpty())
                <div class="alert alert-info">
                    No hay solicitudes registradas todavía.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th class="bg-secondary text-white">Título / Solicitud</th>
                                <th class="d-none d-lg-table-cell bg-secondary text-white">Cliente</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Profesional</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Ciudad / Provincia</th>
                                <th class="bg-secondary text-white">Estado</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Fecha</th>
                                <th class="text-center bg-secondary text-white">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($solicitudes as $solicitud)
                                @php
                                    $cliente = $solicitud->cliente;
                                    $pro = $solicitud->profesional;
                                    $badgeClass = match ($solicitud->estado) {
                                        'abierta' => 'bg-primary',
                                        'en_revision' => 'bg-warning text-dark',
                                        'cerrada' => 'bg-success',
                                        'cancelada' => 'bg-secondary',
                                        default => 'bg-light text-dark',
                                    };
                                @endphp

                                <tr>
                                    {{-- TITULO + BLOQUE MÓVIL --}}
                                    <td>
                                        <strong>
                                            {{ $solicitud->titulo ?? 'Solicitud #' . $solicitud->id }}
                                        </strong>

                                        <div class="small text-muted">
                                            @if ($solicitud->created_at)
                                                Ref: #{{ $solicitud->id }}
                                            @endif
                                        </div>

                                        {{-- Versión móvil (md-) con más detalles --}}
                                        <div class="small text-muted d-block d-md-none mt-1">

                                            {{-- Cliente --}}
                                            <span class="d-block">
                                                <span class="fw-semibold">Cliente:</span>
                                                @if ($cliente)
                                                    {{ $cliente->nombre ?? $cliente->name }}
                                                    {{ $cliente->apellidos ?? '' }}
                                                @else
                                                    <span class="text-muted">Sin cliente</span>
                                                @endif
                                            </span>

                                            {{-- Profesional --}}
                                            <span class="d-block">
                                                <span class="fw-semibold">Profesional:</span>
                                                @if ($pro)
                                                    {{ $pro->empresa }}
                                                    {{ $pro->email_empresa }}
                                                @else
                                                    <span class="text-muted">Sin asignar</span>
                                                @endif
                                            </span>

                                            {{-- Ciudad / provincia --}}
                                            <span class="d-block">
                                                <span class="fw-semibold">Ubicación:</span>
                                                {{ $solicitud->ciudad ?? 'No indicada' }}
                                                @if ($solicitud->provincia)
                                                    - {{ $solicitud->provincia }}
                                                @endif
                                            </span>

                                            {{-- Estado --}}
                                            <span class="d-block">
                                                <span class="fw-semibold">Estado:</span>
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                                </span>
                                            </span>

                                            {{-- Fecha --}}
                                            <span class="d-block">
                                                <span class="fw-semibold">Fecha:</span>
                                                {{ $solicitud->fecha?->format('d/m/Y H:i') ?? $solicitud->created_at?->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- CLIENTE (lg+) --}}
                                    <td class="d-none d-lg-table-cell">
                                        @if ($cliente)
                                            {{ $cliente->nombre ?? $cliente->name }}
                                            {{ $cliente->apellidos ?? '' }}<br>
                                            <small class="text-muted">{{ $cliente->email }}</small>
                                        @else
                                            <span class="text-muted small">Sin cliente</span>
                                        @endif
                                    </td>

                                    {{-- PROFESIONAL (md+) --}}
                                    <td class="d-none d-md-table-cell">
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

                                    {{-- CIUDAD / PROVINCIA (md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        {{ $solicitud->ciudad ?? 'No indicada' }}
                                        @if ($solicitud->provincia)
                                            - {{ $solicitud->provincia }}
                                        @endif
                                    </td>

                                    {{-- ESTADO --}}
                                    <td>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                        </span>
                                    </td>

                                    {{-- FECHA (md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        {{ $solicitud->fecha?->format('d/m/Y H:i') ?? $solicitud->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- ACCIONES --}}
                                    <td class="text-center">
                                        <div class="d-flex flex-column flex-md-row flex-wrap justify-content-center gap-2">

                                            {{-- Ver en modal Vue (ya tienes SolicitudModal y openSolicitudModal en app.js) --}}
                                            <button type="button"
                                                class="btn btn-info btn-sm px-2 py-1 mb-1 mb-md-0 d-inline-flex align-items-center gap-1"
                                                @click="openSolicitudAdminModal({{ $solicitud->id }})">
                                                Ver
                                            </button>

                                            {{-- Editar: solo si abierta o en revisión --}}
                                            @if (in_array($solicitud->estado, ['abierta', 'en_revision']))
                                                <a href="{{ route('admin.solicitudes.editar', $solicitud) }}"
                                                    class="btn btn-warning btn-sm px-2 py-1 mb-1 mb-md-0 d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-pencil"></i> Editar
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
                    {{ $solicitudes->links() }}
                </div>
            @endif

            {{-- Modal Vue para ver solicitud (admin) --}}
            <solicitud-admin-modal ref="solicitudAdminModal"></solicitud-admin-modal>

        </div>
    </div>
@endsection
