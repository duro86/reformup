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
            <x-alertas.alertasFlash />

            {{-- Buscador combinado: campos + fechas --}}
            <form method="GET" action="{{ route('usuario.solicitudes.index') }}" class="row g-2 mb-3">
                {{-- Búsqueda por texto --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                        placeholder="Buscar por título, profesional, ciudad, provincia o estado...">
                </div>

                {{-- Rango de fechas reutilizable (fecha de la solicitud) --}}
                @include('partials.filtros.rango_fechas')

                {{-- Botón Buscar --}}
                <div class="col-6 col-md-3 col-lg-2 d-grid">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>

                {{-- Botón Limpiar --}}
                <div class="col-6 col-md-3 col-lg-2 d-grid">
                    @if (request('q') || request('estado') || request('fecha_desde') || request('fecha_hasta'))
                        <a href="{{ route('usuario.solicitudes.index') }}" class="btn btn-sm btn-outline-secondary">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>

            {{-- Filtros por estado --}}
            <ul class="nav nav-pills mb-3">
                {{-- Todas --}}
                @php
                    $paramsBase = request()->except('page', 'estado');
                    $urlTodas = route('usuario.solicitudes.index', $paramsBase);
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $estado === null ? 'active' : '' }}" href="{{ $urlTodas }}">
                        Todas
                    </a>
                </li>

                {{-- ESTADOS DEL MODELO --}}
                @foreach ($estados as $valor => $texto)
                    @php
                        $params = request()->except('page', 'estado');
                        $params['estado'] = $valor;

                        $urlEstado = route('usuario.solicitudes.index', $params);
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
                                <th class="bg-secondary">Título / Ref</th>
                                <th class="bg-secondary">Profesional</th>
                                <th class="bg-secondary">Provincia / Municipio</th>
                                <th class="text-center bg-secondary">Estado</th>
                                <th class="bg-secondary">Presupuesto máx.</th>
                                <th class="bg-secondary">Fecha</th>
                                <th class="text-center bg-secondary">Acciones</th>
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

                                    {{--  provincia / municipio --}}
                                    <td>
                                        {{ $solicitud->provincia ? ' ' . $solicitud->provincia : '' }}
                                        @if ($solicitud->ciudad)
                                            - {{ $solicitud->ciudad }}
                                        @endif
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
                                                Tu solicitud está cerrada, tienes un presupuesto aceptado y se ha creado una orden de trabajo.
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
                                    @php
                                        $puedeEditarUsuario =
                                            $solicitud->estado === 'abierta' &&
                                            !$solicitud->presupuestos
                                                ->whereIn('estado', ['enviado', 'aceptado'])
                                                ->count();
                                    @endphp

                                    <td class="text-center">
                                        <div class="d-flex flex-row flex-wrap gap-1 justify-content-center">
                                            {{-- Ver (modal Vue) --}}
                                            <button type="button"
                                                class="btn btn-info btn-sm px-2 py-1 d-inline-flex align-items-center gap-1"
                                                @click="openSolicitudUsuarioModal({{ $solicitud->id }})">
                                                Ver
                                            </button>

                                            {{-- Editar (solo si aún editable) --}}
                                            @if ($puedeEditarUsuario)
                                                <a href="{{ route('usuario.solicitudes.editar', $solicitud) }}"
                                                    class="btn btn-warning btn-sm px-2 py-1 d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </a>
                                            @endif

                                            @if ($solicitud->estado == 'abierta')
                                                <x-usuario.solicitudes.btn_cancelar :solicitud="$solicitud" contexto="desktop" />
                                            @endif

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

                        <div class="card mb-3 shadow-sm bg-light">
                            <div class="card-body ">
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
                                        {{ $solicitud->provincia ?? 'No indicada' }}
                                        @if ($solicitud->ciudad)
                                            - {{ $solicitud->ciudad }}
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

                                {{-- ACCIONES --}}
                                @php
                                    $puedeEditarUsuario =
                                        $solicitud->estado === 'abierta' &&
                                        !$solicitud->presupuestos->whereIn('estado', ['enviado', 'aceptado'])->count();
                                @endphp

                                <div class="d-grid gap-2">
                                    {{-- Ver --}}
                                    <button type="button" class="btn btn-info btn-sm"
                                        @click="openSolicitudUsuarioModal({{ $solicitud->id }})">
                                        Ver
                                    </button>

                                    {{-- Editar (móvil) --}}
                                    @if ($puedeEditarUsuario)
                                        <a href="{{ route('usuario.solicitudes.editar', $solicitud) }}"
                                            class="btn btn-warning btn-sm">
                                            Editar
                                        </a>
                                    @endif

                                    {{-- Cancelar --}}
                                    @if ($solicitud->estado == 'abierta')
                                                <x-usuario.solicitudes.btn_cancelar :solicitud="$solicitud" contexto="mobile" />
                                            @endif

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
