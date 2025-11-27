@extends('layouts.main')

@section('title', 'Mis solicitudes - ReformUp')

@section('content')

    <x-navbar />

    {{-- SIDEBAR FIJO (escritorio) --}}
    <x-usuario.usuario_sidebar />
    {{-- BIENVENIDA (se ve igual en todos los tamaños) --}}
    <x-usuario.user_bienvenido />

    {{-- Contenedor principal con Vue --}}
    <div class="container-fluid main-content-with-sidebar" id="app">

        {{-- Nav móvil usuario --}}
        <x-usuario.nav_movil active="solicitudes" />

        <div class="container py-4">

            {{-- Título + botón nueva --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text"></i> Mis solicitudes
                </h1>

                <a href="{{ route('usuario.solicitudes.seleccionar_profesional') }}"
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

            {{-- Buscador --}}
            <x-buscador-q :action="route('usuario.solicitudes.index')" placeholder="Buscar por título, profesional, ciudad o estado..." />


            {{-- Filtros por estado --}}
            <ul class="nav nav-pills mb-3">
                {{-- Opción "Todas" --}}
                <li class="nav-item">
                    @php
                        $urlTodas = route(
                            'usuario.solicitudes.index',
                            array_filter([
                                'q' => request('q'),
                            ]),
                        );
                    @endphp
                    <a class="nav-link {{ $estado === null ? 'active' : '' }}" href="{{ $urlTodas }}">
                        Todas
                    </a>
                </li>

                {{-- ESTADOS DEL MODELO --}}
                @foreach ($estados as $valor => $texto)
                    @php
                        $urlEstado = route(
                            'usuario.solicitudes.index',
                            array_filter([
                                'estado' => $valor,
                                'q' => request('q'),
                            ]),
                        );
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $estado === $valor ? 'active' : '' }}" href="{{ $urlEstado }}">
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
                {{-- ===================================================== --}}
                {{-- TABLA SOLO ESCRITORIO (lg+)                         --}}
                {{-- ===================================================== --}}
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr class="fs-5">
                                <th>Título / Ref</th>
                                <th>Profesional</th>
                                <th>Ciudad / Provincia</th>
                                <th class="text-center">Estado</th>
                                <th>Presupuesto máx.</th>
                                <th>Fecha</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($solicitudes as $solicitud)
                                @php
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
                                    {{-- Título / Ref --}}
                                    <td>
                                        <strong>
                                            {{ $solicitud->titulo ?? 'Solicitud #' . $solicitud->id }}
                                        </strong>
                                        <div class="small text-muted">
                                            Ref: #{{ $solicitud->id }}
                                        </div>
                                    </td>

                                    {{-- Profesional --}}
                                    <td>
                                        @if ($pro)
                                            {{ $pro->empresa }}<br>
                                            @if ($pro->email_empresa)
                                                <small class="text-muted d-block">
                                                    {{ $pro->email_empresa }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted small">Sin asignar</span>
                                        @endif
                                    </td>

                                    {{-- Ciudad / provincia --}}
                                    <td>
                                        {{ $solicitud->ciudad }}
                                        {{ $solicitud->provincia ? ' - ' . $solicitud->provincia : '' }}
                                    </td>

                                    {{-- Estado --}}
                                    <td class="text-center">
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                        </span>
                                        @if ($solicitud->estado === 'en_revision')
                                            <div class="small text-primary mt-1">
                                                Revisa los presupuestos enviados para esta solicitud.
                                            </div>
                                        @endif
                                        @if ($solicitud->estado === 'abierta')
                                            <div class="small text-primary mt-1">
                                                Tu solicitud está pendiente de revisar por el profesional.
                                            </div>
                                        @endif
                                        @if ($solicitud->estado === 'cancelada')
                                            <div class="small text-primary mt-1">
                                                Tu solicitud ha sido cancelada, puedes crear una nueva cuando quieras.
                                            </div>
                                        @endif
                                        @if ($solicitud->estado === 'cerrada')
                                            <div class="small text-primary mt-1">
                                                Tu solicitud está cerrada, tienes un presupuesto aceptado y trabajo en
                                                marcha.
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Presupuesto máx. --}}
                                    <td>
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
                                    <td class="text-center">
                                        <div class="d-flex flex-row flex-wrap gap-1 justify-content-center">

                                            {{-- Ver (modal Vue) --}}
                                            <button type="button"
                                                class="btn btn-info btn-sm px-2 py-1 d-inline-flex align-items-center gap-1"
                                                @click="openSolicitudUsuarioModal({{ $solicitud->id }})">
                                                Ver
                                            </button>

                                            {{-- Eliminar --}}
                                            <x-usuario.solicitudes.btn_eliminar :solicitud="$solicitud" />
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ===================================================== --}}
                {{-- VISTA CARDS MÓVIL/TABLET (xs–lg)                     --}}
                {{-- ===================================================== --}}
                <div class="d-block d-lg-none">
                    @foreach ($solicitudes as $solicitud)
                        @php
                            $pro = $solicitud->profesional;
                            $badgeClass = match ($solicitud->estado) {
                                'abierta' => 'bg-primary',
                                'en_revision' => 'bg-warning text-dark',
                                'cerrada' => 'bg-success',
                                'cancelada' => 'bg-secondary',
                                default => 'bg-light text-dark',
                            };
                        @endphp

                        <div class="card mb-3 shadow-sm">
                            <div class="card-body bg-light">

                                {{-- Título + ref --}}
                                <div class="mb-2">
                                    <div class="fw-semibold">
                                        {{ $solicitud->titulo ?? 'Solicitud #' . $solicitud->id }}
                                    </div>
                                    <div class="small text-muted">
                                        Ref: #{{ $solicitud->id }}
                                    </div>
                                </div>

                                <div class="small text-muted mb-2">
                                    {{-- Profesional --}}
                                    <div class="mb-1">
                                        <strong>Profesional:</strong>
                                        @if ($pro)
                                            {{ $pro->empresa }}<br>
                                            @if ($pro->email_empresa)
                                                <span>{{ $pro->email_empresa }}</span><br>
                                            @endif
                                        @else
                                            <span class="text-muted">Sin asignar</span>
                                        @endif
                                    </div>

                                    {{-- Ubicación --}}
                                    <div class="mt-1">
                                        <strong>Ubicación:</strong>
                                        {{ $solicitud->ciudad ?? 'No indicada' }}
                                        @if ($solicitud->provincia)
                                            - {{ $solicitud->provincia }}
                                        @endif
                                    </div>

                                    {{-- Presupuesto max --}}
                                    <div class="mt-1">
                                        <strong>Presupuesto máx.:</strong>
                                        @if ($solicitud->presupuesto_max)
                                            {{ number_format($solicitud->presupuesto_max, 2, ',', '.') }} €
                                        @else
                                            <span class="text-muted">No indicado</span>
                                        @endif
                                    </div>

                                    {{-- Estado --}}
                                    <div class="mt-1">
                                        <strong>Estado:</strong>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                        </span>
                                        @if ($solicitud->estado === 'en_revision')
                                            <div class="small text-primary mt-1">
                                                Revisa los presupuestos enviados para esta solicitud
                                            </div>
                                        @endif
                                        @if ($solicitud->estado === 'abierta')
                                            <div class="small text-primary mt-1">
                                                Tu solicitud está pendiente de revisar por el profesional
                                            </div>
                                        @endif
                                        @if ($solicitud->estado === 'cancelada')
                                            <div class="small text-primary mt-1">
                                                Tu solicitud ha sido cancelada, puedes crear una nueva
                                            </div>
                                        @endif
                                        @if ($solicitud->estado === 'cerrada')
                                            <div class="small text-primary mt-1">
                                                Tu solicitud está cerrada, tienes el presupuesto aceptado y trabajo en
                                                marcha
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Fecha --}}
                                    <div class="mt-1">
                                        <strong>Fecha:</strong>
                                        {{ $solicitud->fecha?->format('d/m/Y H:i') ?? $solicitud->created_at?->format('d/m/Y H:i') }}
                                    </div>
                                </div>

                                {{-- Acciones en columna, ancho completo --}}
                                <div class="d-grid gap-2">
                                    {{-- Ver --}}
                                    <button type="button" class="btn btn-info btn-sm"
                                        @click="openSolicitudUsuarioModal({{ $solicitud->id }})">
                                        Ver
                                    </button>

                                    {{-- Eliminar --}}
                                    <x-usuario.solicitudes.btn_eliminar :solicitud="$solicitud" />
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                {{ $solicitudes->links('pagination::bootstrap-5') }}

            @endif

            {{-- Modal Vue para ver solicitud (usuario) --}}
            <solicitud-usuario-modal ref="solicitudUsuarioModal"></solicitud-usuario-modal>

        </div>
    </div>

@endsection

@if (session('info_solicitudes'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Presupuestos pendientes',
                text: @json(session('info_solicitudes')),
                icon: 'info',
                confirmButtonText: 'Ver mis solicitudes'
            });
        });
    </script>
@endif

<x-alertas_sweet />
