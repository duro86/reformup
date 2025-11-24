@extends('layouts.main')

@section('title', 'Mis trabajos - ReformUp')

@section('content')

    <x-navbar />

    {{-- SIDEBAR FIJO (escritorio) --}}
    <x-usuario.usuario_sidebar />

    {{-- BIENVENIDA (se ve igual en todos los tamaños) --}}
    <x-usuario.user_bienvenido />

    <div class="container-fluid main-content-with-sidebar">
        {{-- Nav móvil, pestaña activa trabajos --}}
        <x-usuario.nav_movil active="trabajos" />

        <div class="container py-4" id="app">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-hammer"></i> Mis trabajos
                </h1>
            </div>

            {{-- Mensajes flash --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Filtros por estado --}}
            <ul class="nav nav-pills mb-3">
                {{-- Todos --}}
                <li class="nav-item">
                    <a class="nav-link {{ $estado === null ? 'active' : '' }}" href="{{ route('usuario.trabajos.index') }}">
                        Todos
                    </a>
                </li>

                {{-- ESTADOS DEL MODELO --}}
                @foreach ($estados as $valor => $texto)
                    <li class="nav-item">
                        <a class="nav-link {{ $estado === $valor ? 'active' : '' }}"
                            href="{{ route('usuario.trabajos.index', ['estado' => $valor]) }}">
                            {{ $texto }}
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- Si no hay trabajos --}}
            @if ($trabajos->isEmpty())
                <div class="alert alert-info">
                    No tienes trabajos
                    {{ $estado ? 'con estado ' . str_replace('_', ' ', $estado) : 'todavía' }}.
                </div>
            @else
                {{-- Tabla listado Trabajos --}}
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th class="bg-secondary">Trabajo / Referencia</th>
                                <th class="d-none d-lg-table-cell bg-secondary">Empresa</th>
                                <th class="d-none d-lg-table-cell bg-secondary">Estado</th>
                                <th class="d-none d-lg-table-cell bg-secondary">Fecha inicio</th>
                                <th class="d-none d-lg-table-cell bg-secondary">Fecha fin</th>
                                <th class="d-none d-lg-table-cell bg-secondary">Dirección obra</th>
                                <th class="d-none d-lg-table-cell text-center bg-secondary">Total presupuesto</th>

                                <th class="text-center bg-secondary">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trabajos as $trabajo)
                                @php
                                    $presupuesto = $trabajo->presupuesto;
                                    $solicitud = $presupuesto?->solicitud;
                                    $profesionalPresu = $presupuesto?->profesional;
                                    $profesionalSol = $solicitud?->profesional;

                                    // ¿el usuario ya ha comentado este trabajo?
                                    $yaComentado = $trabajo->comentarios
                                        ->where('cliente_id', $usuario->id)
                                        ->isNotEmpty();
                                @endphp

                                <tr>
                                    {{-- Trabajo / referencia + bloque móvil --}}
                                    <td>
                                        <strong>
                                            @if ($solicitud?->titulo)
                                                {{ $solicitud->titulo }}
                                            @else
                                                Trabajo #{{ $trabajo->id }}
                                            @endif
                                        </strong>

                                        {{-- Versión móvil: detalles debajo --}}
                                        <div class="small text-muted d-block d-lg-none mt-1">

                                            {{-- Empresa (presupuesto / solicitud) --}}
                                            <span class="d-block">
                                                <span class="fw-semibold">Empresa:</span>
                                                @if ($profesionalPresu?->empresa || $profesionalSol?->empresa || $solicitud?->empresa)
                                                    @if ($profesionalPresu?->empresa)
                                                        {{ $profesionalPresu->empresa }}
                                                    @endif
                                                    @if ($profesionalSol?->empresa && $profesionalSol?->empresa !== ($profesionalPresu->empresa ?? null))
                                                        <br><span class="text-muted">Sol.:
                                                            {{ $profesionalSol->empresa }}</span>
                                                    @elseif($solicitud?->empresa && !$profesionalSol)
                                                        <br><span class="text-muted">Sol.: {{ $solicitud->empresa }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Sin empresa</span>
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
                                                {{ Str::limit($trabajo->dir_obra ?? 'No indicada', 20, '...') }}
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

                                    {{-- Empresa (solo escritorio) --}}
                                    <td class="d-none d-lg-table-cell">
                                        @if ($profesionalPresu?->empresa || $profesionalSol?->empresa || $solicitud?->empresa)
                                            @if ($profesionalPresu?->empresa)
                                                {{ $profesionalPresu->empresa }}
                                            @endif
                                            @if ($profesionalSol?->empresa && $profesionalSol?->empresa !== ($profesionalPresu->empresa ?? null))
                                                <br><span class="text-muted small">Sol.:
                                                    {{ $profesionalSol->empresa }}</span>
                                            @elseif($solicitud?->empresa && !$profesionalSol)
                                                <br><span class="text-muted small">Sol.: {{ $solicitud->empresa }}</span>
                                            @endif
                                        @else
                                            <span class="text-muted small">Sin empresa</span>
                                        @endif
                                    </td>

                                    {{-- Estado (solo escritorio grande) --}}
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

                                    {{-- Fecha inicio (solo escritorio) --}}
                                    <td class="d-none d-lg-table-cell">
                                        {{ $trabajo->fecha_ini?->format('d/m/Y H:i') ?? 'Sin iniciar' }}
                                    </td>

                                    {{-- Fecha fin (solo escritorio) --}}
                                    <td class="d-none d-lg-table-cell">
                                        {{ $trabajo->fecha_fin?->format('d/m/Y H:i') ?? 'Sin finalizar' }}
                                    </td>

                                    {{-- Dirección obra (solo escritorio) --}}
                                    <td class="d-none d-lg-table-cell">
                                        {{ Str::limit($trabajo->dir_obra ?? 'No indicada', 20, '...') }}
                                    </td>

                                    {{-- Total presupuesto (solo escritorio) --}}
                                    <td class="d-none d-lg-table-cell justify-center text-center">
                                        @if ($presupuesto?->total)
                                            {{ number_format($presupuesto->total, 2, ',', '.') }} €
                                        @else
                                            <span class="text-muted small">No indicado</span>
                                        @endif
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-center">
                                        {{-- Ver detalle trabajo (modal Vue) --}}
                                        <button type="button" class="btn btn-info btn-sm px-2 py-1 mx-1"
                                            @click="openTrabajoModal({{ $trabajo->id }})">
                                            Ver
                                        </button>

                                        {{-- Ver presupuesto PDF --}}
                                        @if ($presupuesto?->docu_pdf)
                                            <a href="{{ asset('storage/' . $presupuesto->docu_pdf) }}"
                                                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1"
                                                target="_blank">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                                Ver presupuesto
                                            </a>
                                        @endif

                                        {{-- Cancelar trabajo (solo si está previsto y no ha empezado) --}}
                                        @if ($trabajo->estado === 'previsto' && is_null($trabajo->fecha_ini))
                                            <x-usuario.trabajos.btn_cancelar :trabajo="$trabajo" />
                                        @endif

                                        {{-- Botón comentar (solo si finalizado y sin comentario del cliente) --}}
                                        @if ($trabajo->estado === 'finalizado' && !$yaComentado)
                                            <a href="{{ route('usuario.comentarios.crear', $trabajo) }}"
                                                class="btn btn-sm btn-warning d-inline-flex align-items-center gap-1 mb-1">
                                                <i class="bi bi-star"></i>
                                                Valorar
                                            </a>
                                        @endif
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

            {{-- Modal Vue --}}
            <trabajo-modal ref="trabajoModal"></trabajo-modal>
        </div>
    </div>

@endsection

<x-alertas_sweet />
