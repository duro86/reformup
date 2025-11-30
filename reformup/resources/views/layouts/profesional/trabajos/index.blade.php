@extends('layouts.main')

@section('title', 'Mis trabajos - Profesional - ReformUp')

@section('content')

    {{-- Navbar --}}
    <x-navbar />

    {{-- Sidebar profesional + bienvenida --}}
    <x-profesional.profesional_sidebar />
    <x-profesional.profesional_bienvenido />

    <div class="container-fluid main-content-with-sidebar">
        {{-- Nav superior móvil --}}
        <x-profesional.nav_movil active="trabajos" />

        <div class="container py-4" id="app">

            {{-- Título --}}
            <div class="d-flex flex-column flex-md-row align-items-md-center mb-3 gap-2 justify-content-center">
                <h3 class="mb-1 d-flex align-items-center gap-2 justify-content-center">
                    <i class="bi bi-hammer"></i> Mis trabajos
                </h3>
            </div>

            {{-- Mensajes flash --}}
            <x-alertas.alertasFlash />

            {{-- Buscador combinado: campos + fechas --}}
            <form method="GET" action="{{ route('profesional.trabajos.index') }}" class="row g-2 mb-3">
                {{-- Búsqueda por texto --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                        placeholder="Buscar por título, cliente, ciudad, provincia, estado o importe...">
                </div>

                {{-- Rango de fechas reutilizable (creación del trabajo) --}}
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
                        <a href="{{ route('profesional.trabajos.index') }}" class="btn btn-sm btn-outline-secondary">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>

            {{-- Filtros por estado --}}
            <ul class="nav nav-pills mb-3">
                {{-- Todos --}}
                @php
                    $paramsBase = request()->except('page', 'estado');
                    $urlTodos = route('profesional.trabajos.index', $paramsBase);
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $estado === null ? 'active' : '' }}" href="{{ $urlTodos }}">
                        Todos
                    </a>
                </li>

                {{-- ESTADOS DEL MODELO --}}
                @foreach ($estados as $valor => $texto)
                    @php
                        $params = request()->except('page', 'estado');
                        $params['estado'] = $valor;

                        $urlEstado = route('profesional.trabajos.index', $params);
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $estado === $valor ? 'active' : '' }}" href="{{ $urlEstado }}">
                            {{ $texto }}
                        </a>
                    </li>
                @endforeach
            </ul>




            @if ($trabajos->isEmpty())
                <div class="alert alert-info">
                    No tienes trabajos asignados todavía.
                </div>
            @else
                {{-- ========================= --}}
                {{-- TABLA (solo en lg+)      --}}
                {{-- ========================= --}}
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr class="fs-5">
                                <th class="bg-secondary">Trabajo</th>
                                <th class="bg-secondary">Cliente</th>
                                <th class="bg-secondary text-center">Estado</th>
                                <th class="bg-secondary">Fecha inicio</th>
                                <th class="bg-secondary ">Fecha fin</th>
                                <th class="bg-secondary ">Dirección obra</th>
                                <th class="bg-secondary  text-center">Total presupuesto</th>
                                <th class="bg-secondary  text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trabajos as $trabajo)
                                @php
                                    $presupuesto = $trabajo->presupuesto;
                                    $solicitud = $presupuesto?->solicitud;
                                    $cliente = $solicitud?->cliente;

                                    $badgeClass = match ($trabajo->estado) {
                                        'previsto' => 'bg-primary',
                                        'en_curso' => 'bg-warning text-dark',
                                        'finalizado' => 'bg-success',
                                        'cancelado' => 'bg-secondary',
                                        default => 'bg-light text-dark',
                                    };
                                @endphp

                                <tr>
                                    {{-- Trabajo / título --}}
                                    <td>
                                        <strong>
                                            @if ($solicitud?->titulo)
                                                {{ $solicitud->titulo }}
                                            @else
                                                Trabajo #{{ $trabajo->id }}
                                            @endif
                                        </strong>
                                        @if ($solicitud)
                                            <div class="small text-muted">
                                                Ref. solicitud: #{{ $solicitud->id }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Cliente --}}
                                    <td>
                                        @if ($cliente)
                                            {{ $cliente->nombre ?? $cliente->name }}
                                            {{ $cliente->apellidos ?? '' }}<br>
                                            <small class="text-muted">{{ $cliente->email }}</small>
                                        @else
                                            <span class="text-muted small">Sin cliente</span>
                                        @endif
                                    </td>

                                    {{-- Estado --}}
                                    <td class="text-center">
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $trabajo->estado)) }}
                                        </span>
                                        @if ($trabajo->estado === 'previsto')
                                            <div class="small text-primary mt-1">
                                                Puede usted comenzar el trabajo inicializándolo
                                            </div>
                                        @endif
                                        @if ($trabajo->estado === 'en_curso')
                                            <div class="small text-primary mt-1">
                                                El trabajo está iniciado, si ha finalizado, puede comunicarlo en finalizar
                                            </div>
                                        @endif
                                        @if ($trabajo->estado === 'finalizado')
                                            <div class="small text-primary mt-1">
                                                El trabajo se ha finalizado, ¡compruebe sus reseñas en comentarios!
                                            </div>
                                        @endif
                                        @if ($trabajo->estado === 'cancelado')
                                            <div class="small text-primary mt-1">
                                                El trabajo ha sido cancelado y notificado al cliente y profesional
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Fechas --}}
                                    <td>
                                        {{ $trabajo->fecha_ini?->format('d/m/Y H:i') ?? 'Sin iniciar' }}
                                    </td>
                                    <td>
                                        {{ $trabajo->fecha_fin?->format('d/m/Y H:i') ?? 'Sin finalizar' }}
                                    </td>

                                    {{-- Dirección --}}
                                    <td>
                                        {{ \Illuminate\Support\Str::limit($trabajo->dir_obra ?? 'No indicada', 40, '...') }}
                                    </td>

                                    {{-- Total --}}
                                    <td class="text-center">
                                        @if ($presupuesto?->total)
                                            {{ number_format($presupuesto->total, 2, ',', '.') }} €
                                        @else
                                            <span class="text-muted small">No indicado</span>
                                        @endif
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-center">
                                        <div
                                            class="d-flex flex-row flex-wrap justify-content-center align-items-center gap-2">

                                            {{-- Ver modal --}}
                                            <button type="button"
                                                class="btn btn-info btn-sm px-2 py-1 d-inline-flex align-items-center gap-1"
                                                @click="openTrabajoProModal({{ $trabajo->id }})">
                                                Ver
                                            </button>

                                            {{-- PREVISTO: Empezar / Cancelar --}}
                                            @if ($trabajo->estado === 'previsto' && is_null($trabajo->fecha_ini))
                                                <form action="{{ route('profesional.trabajos.empezar', $trabajo) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-sm px-2 py-1">
                                                        Empezar
                                                    </button>
                                                </form>

                                                <x-profesional.trabajos.btn_cancelar :trabajo="$trabajo" />

                                                {{-- EN CURSO: Finalizar --}}
                                            @elseif ($trabajo->estado === 'en_curso')
                                                <form action="{{ route('profesional.trabajos.finalizar', $trabajo) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-danger btn-sm px-2 py-1">
                                                        Finalizar
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Ver presupuesto PDF --}}
                                            @if ($presupuesto?->docu_pdf)
                                                <a href="{{ route('presupuestos.ver_pdf', $presupuesto) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 fw-semibold text-dark px-2 py-1 rounded">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                    Ver PDF
                                                </a>
                                            @else
                                                <span class="text-muted small">Sin PDF</span>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ========================= --}}
                {{-- CARDS (xs–lg)            --}}
                {{-- ========================= --}}
                <div class="d-block d-lg-none">
                    @foreach ($trabajos as $trabajo)
                        @php
                            $presupuesto = $trabajo->presupuesto;
                            $solicitud = $presupuesto?->solicitud;
                            $cliente = $solicitud?->cliente;

                            $badgeClass = match ($trabajo->estado) {
                                'previsto' => 'bg-primary',
                                'en_curso' => 'bg-warning text-dark',
                                'finalizado' => 'bg-success',
                                'cancelado' => 'bg-secondary',
                                default => 'bg-light text-dark',
                            };
                        @endphp

                        <div class="card mb-3 shadow-sm bg-light">
                            <div class="card-body ">

                                {{-- Cabecera: título --}}
                                <div class="mb-2">
                                    <div class="fw-semibold">
                                        @if ($solicitud?->titulo)
                                            {{ $solicitud->titulo }}
                                        @else
                                            Trabajo #{{ $trabajo->id }}
                                        @endif
                                    </div>
                                    <div class="small text-muted">
                                        @if ($solicitud)
                                            Ref. solicitud: #{{ $solicitud->id }}
                                        @endif
                                    </div>
                                </div>

                                {{-- Datos detalle --}}
                                <div class="small text-muted mb-2">
                                    {{-- Cliente --}}
                                    <div class="mb-1">
                                        <strong>Cliente:</strong>
                                        @if ($cliente)
                                            {{ $cliente->nombre ?? $cliente->name }}
                                            {{ $cliente->apellidos ?? '' }}
                                            @if ($cliente->email)
                                                <br><span class="text-muted">{{ $cliente->email }}</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Sin cliente</span>
                                        @endif
                                    </div>

                                    {{-- Estado --}}
                                    <div class="mb-1">
                                        <strong>Estado:</strong>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $trabajo->estado)) }}
                                        </span>
                                        @if ($trabajo->estado === 'previsto')
                                            <div class="small text-primary mt-1">
                                                Puede usted comenzar el trabajo inicializándolo
                                            </div>
                                        @endif
                                        @if ($trabajo->estado === 'en_curso')
                                            <div class="small text-primary mt-1">
                                                El trabajo está iniciado, si ha finalizado, puede comunicarlo en finalizar
                                            </div>
                                        @endif
                                        @if ($trabajo->estado === 'finalizado')
                                            <div class="small text-primary mt-1">
                                                El trabajo se ha finalizado, ¡compruebe sus reseñas en comentarios!
                                            </div>
                                        @endif
                                        @if ($trabajo->estado === 'cancelado')
                                            <div class="small text-primary mt-1">
                                                El trabajo ha sido cancelado y notificado al cliente y profesional
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Fechas --}}
                                    <div class="mb-1">
                                        <strong>Inicio:</strong>
                                        {{ $trabajo->fecha_ini?->format('d/m/Y H:i') ?? 'Sin iniciar' }}
                                    </div>
                                    <div class="mb-1">
                                        <strong>Fin:</strong>
                                        {{ $trabajo->fecha_fin?->format('d/m/Y H:i') ?? 'Sin finalizar' }}
                                    </div>

                                    {{-- Dirección --}}
                                    <div class="mb-1">
                                        <strong>Dir. obra:</strong>
                                        {{ $trabajo->dir_obra ?? 'No indicada' }}
                                    </div>

                                    {{-- Total --}}
                                    <div class="mb-1">
                                        <strong>Total presupuesto:</strong>
                                        @if ($presupuesto?->total)
                                            {{ number_format($presupuesto->total, 2, ',', '.') }} €
                                        @else
                                            <span class="text-muted">No indicado</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Acciones en columna, ancho completo --}}
                                <div class="d-grid gap-2">

                                    {{-- Ver modal --}}
                                    <button type="button" class="btn btn-info btn-sm"
                                        @click="openTrabajoProModal({{ $trabajo->id }})">
                                        Ver
                                    </button>

                                    {{-- PREVISTO: Empezar / Cancelar --}}
                                    @if ($trabajo->estado === 'previsto' && is_null($trabajo->fecha_ini))
                                        <form action="{{ route('profesional.trabajos.empezar', $trabajo) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm w-100">
                                                Empezar
                                            </button>
                                        </form>

                                        <x-profesional.trabajos.btn_cancelar :trabajo="$trabajo" contexto="mobile" />

                                        {{-- EN CURSO: Finalizar --}}
                                    @elseif ($trabajo->estado === 'en_curso')
                                        <form action="{{ route('profesional.trabajos.finalizar', $trabajo) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                                Finalizar
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Ver presupuesto PDF --}}
                                    @if ($presupuesto?->docu_pdf)
                                        <a href="{{ route('presupuestos.ver_pdf', $presupuesto) }}" target="_blank"
                                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 fw-semibold text-dark px-2 py-1 rounded">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                            Ver PDF
                                        </a>
                                    @else
                                        <span class="text-muted small">Sin PDF</span>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="mt-3">
                    {{ $trabajos->links('pagination::bootstrap-5') }}
                </div>
            @endif

            {{-- Modal Vue para ver trabajo como profesional --}}
            <trabajo-pro-modal ref="trabajoProModal"></trabajo-pro-modal>

        </div>
    </div>
@endsection

<x-alertas_sweet />
