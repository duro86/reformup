@extends('layouts.main')

@section('title', 'Gestión de presupuestos - Admin - ReformUp')

@section('content')

    <x-navbar />
    <x-admin.admin_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        {{-- NAV MÓVIL ADMIN --}}
        <x-admin.nav_movil active="presupuestos" />

        <div class="container py-4" id="app">

            {{-- Título + feedback --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text"></i> Mis Presupuestos
                </h1>

                <a href="{{ route('admin.presupuestos.seleccionar_solicitud') }}"
                    class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-plus-circle"></i>
                    Nuevo Presupuesto
                </a>
            </div>

            {{-- Mensajes flash --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Filtros por estado (si vienen desde el controlador) --}}
            @if (isset($estados) && is_array($estados))
                <ul class="nav nav-pills mb-3">
                    @foreach ($estados as $valor => $texto)
                        @php
                            $isActive = $estado === $valor || (is_null($estado) && is_null($valor));
                            $url = $valor
                                ? route('admin.presupuestos', ['estado' => $valor])
                                : route('admin.presupuestos');
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link {{ $isActive ? 'active' : '' }}" href="{{ $url }}">
                                {{ $texto }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($presupuestos->isEmpty())
                <div class="alert alert-info">
                    No hay presupuestos registrados
                    {{ $estado ? 'con estado ' . str_replace('_', ' ', $estado) : 'todavía' }}.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                {{-- En móvil solo se ve esta columna, dentro va todo --}}
                                <th class="bg-secondary text-white">Presupuesto / Solicitud</th>
                                <th class="d-none d-lg-table-cell bg-secondary text-white">Cliente</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Profesional</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Ciudad / Provincia</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Total</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Estado / Fecha</th>
                                <th class="bg-secondary text-white text-center">Documento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($presupuestos as $presupuesto)
                                @php
                                    $solicitud = $presupuesto->solicitud;
                                    $cliente = $solicitud?->cliente;
                                    $pro = $presupuesto->profesional;

                                    $badgeClass = match ($presupuesto->estado) {
                                        'enviado' => 'bg-primary',
                                        'aceptado' => 'bg-success',
                                        'rechazado' => 'bg-danger',
                                        'cancelado' => 'bg-secondary',
                                        'caducado' => 'bg-dark',
                                        default => 'bg-light text-dark',
                                    };
                                @endphp

                                <tr>
                                    {{-- COLUMNA PRINCIPAL: Título + bloque móvil --}}
                                    <td>
                                        {{-- Título / id --}}
                                        <strong>
                                            @if ($solicitud && $solicitud->titulo)
                                                {{ $solicitud->titulo }}
                                            @else
                                                Presupuesto #{{ $presupuesto->id }}
                                            @endif
                                        </strong>

                                        <div class="small text-muted">
                                            Ref. presupuesto: #{{ $presupuesto->id }}
                                            @if ($solicitud)
                                                · Solicitud #{{ $solicitud->id }}
                                            @endif
                                        </div>

                                        {{-- Versión móvil (md-) con más detalles --}}
                                        <div class="small text-muted d-block d-md-none mt-2">

                                            {{-- Cliente --}}
                                            <div class="mb-1">
                                                <span class="fw-semibold">Cliente:</span>
                                                @if ($cliente)
                                                    {{ $cliente->nombre ?? $cliente->name }}
                                                    {{ $cliente->apellidos ?? '' }}<br>
                                                    <span class="text-muted">{{ $cliente->email }}</span>
                                                @else
                                                    <span class="text-muted">Sin cliente</span>
                                                @endif
                                            </div>

                                            {{-- Profesional --}}
                                            <div class="mb-1">
                                                <span class="fw-semibold">Profesional:</span>
                                                @if ($pro)
                                                    {{ $pro->empresa }}<br>
                                                    @if ($pro->email_empresa)
                                                        <span class="text-muted">{{ $pro->email_empresa }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Sin asignar</span>
                                                @endif
                                            </div>

                                            {{-- Ciudad / provincia --}}
                                            <div class="mb-1">
                                                <span class="fw-semibold">Ubicación:</span>
                                                @if ($solicitud)
                                                    {{ $solicitud->ciudad ?? 'No indicada' }}
                                                    @if ($solicitud->provincia)
                                                        - {{ $solicitud->provincia }}
                                                    @endif
                                                @else
                                                    <span class="text-muted">No indicada</span>
                                                @endif
                                            </div>

                                            {{-- Total --}}
                                            <div class="mb-1">
                                                <span class="fw-semibold">Total:</span>
                                                {{ number_format($presupuesto->total, 2, ',', '.') }} €
                                            </div>

                                            {{-- Estado + fecha --}}
                                            <div class="mb-1">
                                                <span class="fw-semibold">Estado:</span>
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ ucfirst(str_replace('_', ' ', $presupuesto->estado)) }}
                                                </span>
                                            </div>

                                            <div class="mb-1">
                                                <span class="fw-semibold">Fecha:</span>
                                                {{ $presupuesto->fecha?->format('d/m/Y H:i') ?? $presupuesto->created_at?->format('d/m/Y H:i') }}
                                            </div>

                                            {{-- Documento (PDF) --}}
                                            <div class="mt-1">
                                                <span class="fw-semibold">Documento:</span>
                                                @if ($presupuesto->docu_pdf)
                                                    <a href="{{ asset('storage/' . $presupuesto->docu_pdf) }}"
                                                        target="_blank"
                                                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 mt-1">
                                                        <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                                                    </a>
                                                @else
                                                    <span class="text-muted">Sin PDF</span>
                                                @endif
                                            </div>
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
                                                <small class="text-muted d-block">{{ $pro->email_empresa }}</small>
                                            @endif
                                            <small class="text-muted d-block">
                                                {{ $pro->ciudad }}
                                                {{ $pro->provincia ? ' - ' . $pro->provincia : '' }}
                                            </small>
                                        @else
                                            <span class="text-muted small">Sin asignar</span>
                                        @endif
                                    </td>

                                    {{-- CIUDAD / PROVINCIA (md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        @if ($solicitud)
                                            {{ $solicitud->ciudad ?? 'No indicada' }}
                                            @if ($solicitud->provincia)
                                                - {{ $solicitud->provincia }}
                                            @endif
                                        @else
                                            <span class="text-muted small">No indicada</span>
                                        @endif
                                    </td>

                                    {{-- TOTAL (md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        {{ number_format($presupuesto->total, 2, ',', '.') }} €
                                    </td>

                                    {{-- ESTADO / FECHA (md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $presupuesto->estado)) }}
                                        </span><br>
                                        <small class="text-muted">
                                            {{ $presupuesto->fecha?->format('d/m/Y H:i') ?? $presupuesto->created_at?->format('d/m/Y H:i') }}
                                        </small>
                                    </td>

                                    {{-- ACCIONES --}}
                                    <td class="text-center">
                                        <div class="d-flex flex-column flex-md-row flex-wrap justify-content-center gap-2">

                                            {{-- Ver (modal Vue) --}}
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1"
                                                @click="openPresupuestoAdminModal({{ $presupuesto->id }})">
                                                <i class="bi bi-eye"></i> Ver
                                            </button>

                                            {{-- Editar presupuesto (solo si está ENVIADO) --}}
                                            @if ($presupuesto->estado === 'enviado')
                                                <a href="{{ route('admin.presupuestos.editar', $presupuesto) }}"
                                                    class="btn btn-warning btn-sm d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-pencil-square"></i>
                                                    Editar
                                                </a>
                                            @endif

                                            {{-- Cancelar presupuesto (solo si está ENVIADO y no está ya rechazado) --}}
                                            @if ($presupuesto->estado === 'enviado')
                                                <x-admin.presupuestos.btn_cancelar :presupuesto="$presupuesto" />
                                            @endif
                                        </div>

                                        {{-- PDF --}}
                                        @if ($presupuesto->docu_pdf)
                                            <a href="{{ asset('storage/' . $presupuesto->docu_pdf) }}" target="_blank"
                                                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 mt-2">
                                                <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                                            </a>
                                        @else
                                            <span class="text-muted small d-block mt-2">Sin PDF</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="mt-3">
                    {{ $presupuestos->links() }}
                </div>
            @endif
            <presupuesto-admin-modal ref="presupuestoAdminModal"></presupuesto-admin-modal>
        </div>
    </div>
@endsection
