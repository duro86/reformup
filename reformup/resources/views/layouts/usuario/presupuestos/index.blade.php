@extends('layouts.main')

@section('title', 'Mis presupuestos - ReformUp')

@section('content')

    {{-- Navbar superior --}}
    <x-navbar />

    {{-- SIDEBAR FIJO (escritorio) --}}
    <x-usuario.usuario_sidebar />

    {{-- BIENVENIDA (se ve igual en todos los tamaños) --}}
    <x-usuario.user_bienvenido />

    {{-- NAV SUPERIOR SOLO MÓVIL/TABLET --}}
    <x-usuario.nav_movil active="presupuestos" />

    {{-- Contenedor principal --}}
    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">

            {{-- Título --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-receipt"></i>
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

            {{-- Buscador reutilizable --}}
            <x-buscador-q :action="route('usuario.presupuestos.index')" placeholder="Buscar por título, profesional, ciudad, estado o importe..." />

            {{-- Filtros por estado --}}
            @php
                // $estados viene del controlador (Presupuesto::ESTADOS)
                // Ejemplo: ['enviado' => 'Enviados', ...]
            @endphp

            <ul class="nav nav-pills mb-3">
                {{-- Opción "Todas" --}}
                @php
                    $urlTodas = route(
                        'usuario.presupuestos.index',
                        array_filter([
                            'q' => request('q'),
                        ]),
                    );
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $estado === null ? 'active' : '' }}" href="{{ $urlTodas }}">
                        Todas
                    </a>
                </li>

                {{-- ESTADOS DEL MODELO --}}
                @foreach ($estados as $valor => $texto)
                    @php
                        $urlEstado = route(
                            'usuario.presupuestos.index',
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

            {{-- Lista de presupuestos --}}
            @if ($presupuestos->isEmpty())
                <div class="alert alert-info">
                    No tienes presupuestos
                    {{ $estado ? 'con estado ' . str_replace('_', ' ', $estado) : 'todavía' }}.
                </div>
            @else
                {{-- ===================================================== --}}
                {{-- TABLA SOLO ESCRITORIO (lg+)                         --}}
                {{-- ===================================================== --}}
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr class="fs-5">
                                <th>Solicitud</th>
                                <th>Profesional</th>
                                {{-- Estado en el centro --}}
                                <th class="text-center">Estado</th>
                                <th>Importe</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($presupuestos as $presu)
                                @php
                                    $profesional = $presu->solicitud?->profesional;
                                    $badgeClass = match ($presu->estado) {
                                        'enviado' => 'bg-primary',
                                        'aceptado' => 'bg-success',
                                        'rechazado' => 'bg-danger',
                                        'caducado' => 'bg-secondary',
                                        default => 'bg-light text-dark',
                                    };
                                @endphp

                                <tr>
                                    {{-- Solicitud / título --}}
                                    <td>
                                        <strong>
                                            {{ $presu->solicitud->titulo ?? '—' }}
                                        </strong>
                                        <div class="small text-muted">
                                            Ref. presupuesto: #{{ $presu->id }}<br>
                                            @if ($presu->solicitud)
                                                Solicitud #{{ $presu->solicitud->id }}
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Profesional --}}
                                    <td>
                                        @if ($profesional)
                                            {{ $profesional->empresa }}<br>
                                            @if ($profesional->email_empresa)
                                                <span class="small text-muted">
                                                    {{ $profesional->email_empresa }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted small">Sin datos</span>
                                        @endif
                                    </td>

                                    {{-- Estado (centrado + texto explicativo) --}}
                                    <td class="text-center">
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($presu->estado) }}
                                        </span>

                                        @if ($presu->estado === 'enviado')
                                            <div class="small text-primary mt-1">
                                                El profesional te ha enviado un presupuesto. Puedes aceptarlo o rechazarlo.
                                            </div>
                                        @endif
                                        @if ($presu->estado === 'aceptado')
                                            <div class="small text-primary mt-1">
                                                Has aceptado el presupuesto. Revisa el apartado de trabajos.
                                            </div>
                                        @endif
                                        @if ($presu->estado === 'rechazado')
                                            <div class="small text-primary mt-1">
                                                Has rechazado este presupuesto. El profesional puede enviarte otro.
                                            </div>
                                        @endif
                                        @if ($presu->estado === 'caducado')
                                            <div class="small text-primary mt-1">
                                                El presupuesto ha caducado. Crea una nueva solicitud si sigues interesado.
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Importe --}}
                                    <td>
                                        {{ number_format($presu->total, 2, ',', '.') }} €
                                    </td>

                                    {{-- Fecha --}}
                                    <td class="text-center">
                                        {{ $presu->fecha?->format('d/m/Y H:i') ?? $presu->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-center">
                                        <div class="d-flex flex-row flex-wrap gap-1 justify-content-center">

                                            {{-- Ver PDF (sólo si existe) --}}
                                            @if ($presu->docu_pdf)
                                                <a href="{{ route('presupuestos.ver_pdf', $presu) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 mx-1 fw-semibold text-dark px-2 py-1 rounded">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                    Ver presupuesto
                                                </a>
                                            @else
                                                <span class="text-muted small me-2 align-self-center">
                                                    Sin documento
                                                </span>
                                            @endif

                                            {{-- Aceptar / Rechazar solo si está ENVIADO --}}
                                            @if ($presu->estado === 'enviado')
                                                <div class="d-flex flex-row gap-2">
                                                    <x-usuario.presupuestos.btn_rechazar :presupuesto="$presu"
                                                        contexto="desktop" />
                                                    <x-usuario.presupuestos.btn_aceptar :presupuesto="$presu"
                                                        contexto="desktop" />
                                                </div>
                                            @endif
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
                    @foreach ($presupuestos as $presu)
                        @php
                            $profesional = $presu->solicitud?->profesional;
                            $badgeClass = match ($presu->estado) {
                                'enviado' => 'bg-primary',
                                'aceptado' => 'bg-success',
                                'rechazado' => 'bg-danger',
                                'caducado' => 'bg-secondary',
                                default => 'bg-light text-dark',
                            };
                        @endphp

                        <div class="card mb-3 shadow-sm">
                            <div class="card-body bg-light">

                                {{-- Título solicitud + refs --}}
                                <div class="mb-2">
                                    <div class="fw-semibold">
                                        {{ $presu->solicitud->titulo ?? '—' }}
                                    </div>
                                    <div class="small text-muted">
                                        Ref. presupuesto: #{{ $presu->id }} <br>
                                        @if ($presu->solicitud)
                                            Solicitud #{{ $presu->solicitud->id }}
                                        @endif
                                    </div>
                                </div>

                                <div class="small text-muted mb-2">
                                    {{-- Profesional --}}
                                    <div class="mb-1">
                                        <strong>Profesional:</strong>
                                        @if ($profesional)
                                            {{ $profesional->empresa }}<br>
                                            @if ($profesional->email_empresa)
                                                <span>{{ $profesional->email_empresa }}</span><br>
                                            @endif
                                        @else
                                            <span class="text-muted">Sin datos</span>
                                        @endif
                                    </div>

                                    {{-- Importe --}}
                                    <div class="mb-1">
                                        <strong>Importe:</strong>
                                        {{ number_format($presu->total, 2, ',', '.') }} €
                                    </div>

                                    {{-- Estado --}}
                                    <div class="mb-1">
                                        <strong>Estado:</strong>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($presu->estado) }}
                                        </span>
                                        @if ($presu->estado === 'enviado')
                                            <div class="small text-primary mt-1">
                                                El profesional le ha enviado un presupuesto. Puedes aceptarlo o rechazarlo.
                                            </div>
                                        @endif
                                        @if ($presu->estado === 'aceptado')
                                            <div class="small text-primary mt-1">
                                                El presupuesto ha sido aceptado. Revisa el apartado de trabajos.
                                            </div>
                                        @endif
                                        @if ($presu->estado === 'rechazado')
                                            <div class="small text-primary mt-1">
                                                Si has rechazado el presupuesto, el profesional le enviará otro
                                            </div>
                                        @endif
                                        @if ($presu->estado === 'caducado')
                                            <div class="small text-primary mt-1">
                                                El presupuesto ha caducado. Crea una nueva solicitud si sigues interesado.
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Fecha --}}
                                    <div class="mb-1">
                                        <strong>Fecha:</strong>
                                        {{ $presu->fecha?->format('d/m/Y H:i') ?? $presu->created_at?->format('d/m/Y H:i') }}
                                    </div>
                                </div>

                                {{-- Acciones en columna, ancho completo --}}
                                <div class="d-grid gap-2">
                                    {{-- Ver PDF --}}
                                    @if ($presu->docu_pdf)
                                        <a href="{{ route('presupuestos.ver_pdf', $presu) }}" target="_blank"
                                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 fw-semibold text-dark px-2 py-1 rounded">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                            Ver presupuesto
                                        </a>
                                    @else
                                        <span class="text-muted small text-center">Sin documento</span>
                                    @endif

                                    {{-- Aceptar / Rechazar solo si está ENVIADO --}}
                                    @if ($presu->estado === 'enviado')
                                        <x-usuario.presupuestos.btn_aceptar :presupuesto="$presu" contexto="mobile"
                                            :tiene-direccion="(bool) optional($presu->solicitud)->dir_cliente" :direccion-obra="optional($presu->solicitud)->dir_cliente" />

                                        <x-usuario.presupuestos.btn_rechazar :presupuesto="$presu" contexto="mobile" />
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="mt-3">
                    {{ $presupuestos->links('pagination::bootstrap-5') }}
                </div>

            @endif

        </div>
    </div>

@endsection
