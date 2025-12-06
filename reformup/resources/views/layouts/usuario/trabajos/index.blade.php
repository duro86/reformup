@extends('layouts.main')

@section('title', 'Mis trabajos - ReformUp')

@section('content')

    {{-- Navbar superior --}}
    <x-navbar />

    {{-- SIDEBAR FIJO (escritorio) --}}
    <x-usuario.usuario_sidebar />

    {{-- BIENVENIDA (se ve igual en todos los tamaños) --}}
    <x-usuario.user_bienvenido />

    <div class="container-fluid main-content-with-sidebar">
        {{-- Nav móvil, pestaña activa trabajos --}}
        <x-usuario.nav_movil active="trabajos" />

        <div class="container py-4" id="app">

            {{-- Título --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-hammer"></i>
                    Mis trabajos
                </h1>
            </div>

            {{-- Mensajes flash --}}
            <x-alertas.alertasFlash />

            {{-- Buscador combinado: texto + fechas --}}
            <form method="GET" action="{{ route('usuario.trabajos.index') }}" class="row g-2 mb-3">
                {{-- Búsqueda por texto --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                        placeholder="Buscar por título, empresa, ciudad, estado o importe...">
                </div>

                {{-- Rango de fechas reutilizable (inicio del trabajo) --}}
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
                        <a href="{{ route('usuario.trabajos.index') }}" class="btn btn-sm btn-outline-secondary">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>

            {{-- Filtros por estado --}}
            <ul class="nav nav-pills mb-3">
                @php
                    // Conservamos q + fechas al cambiar de estado
                    $paramsBase = request()->except('page', 'estado');
                    $urlTodos = route('usuario.trabajos.index', $paramsBase);
                @endphp

                {{-- Todos --}}
                <li class="nav-item">
                    <a class="nav-link {{ $estado === null ? 'active' : '' }}" href="{{ $urlTodos }}">
                        Todos
                    </a>
                </li>

                {{-- ESTADOS DEL MODELO --}}
                @foreach ($estados as $valor => $texto)
                    @php
                        $paramsEstado = array_merge($paramsBase, ['estado' => $valor]);
                        $urlEstado = route('usuario.trabajos.index', $paramsEstado);
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $estado === $valor ? 'active' : '' }}" href="{{ $urlEstado }}">
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
                {{-- ===================================================== --}}
                {{-- TABLA SOLO ESCRITORIO (lg+)                         --}}
                {{-- ===================================================== --}}
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr class="fs-5">
                                <th>Trabajo / Referencia</th>
                                <th>Empresa</th>
                                <th class="text-center">Estado</th>
                                <th>Fecha inicio</th>
                                <th>Fecha fin</th>
                                <th>Dirección obra</th>
                                <th class="text-center">Total presupuesto</th>
                                <th class="text-center">Acciones</th>
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

                                    $badgeClass = match ($trabajo->estado) {
                                        'previsto' => 'bg-primary',
                                        'en_curso' => 'bg-warning text-dark',
                                        'finalizado' => 'bg-success',
                                        'cancelado' => 'bg-secondary',
                                        default => 'bg-light text-dark',
                                    };
                                @endphp

                                <tr>
                                    {{-- Trabajo / referencia --}}
                                    <td>
                                        <strong>
                                            @if ($solicitud?->titulo)
                                                {{ $solicitud->titulo }}
                                            @else
                                                Trabajo #{{ $trabajo->id }}
                                            @endif
                                        </strong>
                                        <div class="small text-muted">
                                            Ref. trabajo: #{{ $trabajo->id }}
                                            @if ($presupuesto)
                                                · Presupuesto #{{ $presupuesto->id }}
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Empresa (presupuesto / solicitud) --}}
                                    <td>
                                        @if ($profesionalPresu?->empresa || $profesionalSol?->empresa || $solicitud?->empresa)
                                            @if ($profesionalPresu?->empresa)
                                                {{ $profesionalPresu->empresa }}
                                            @endif
                                            @if ($profesionalSol?->empresa && $profesionalSol?->empresa !== ($profesionalPresu->empresa ?? null))
                                                <br>
                                                <span class="text-muted small">
                                                    Sol.: {{ $profesionalSol->empresa }}
                                                </span>
                                            @elseif($solicitud?->empresa && !$profesionalSol)
                                                <br>
                                                <span class="text-muted small">
                                                    Sol.: {{ $solicitud->empresa }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted small">Sin empresa</span>
                                        @endif
                                    </td>

                                    {{-- Estado (centrado) --}}
                                    <td class="text-center">
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $trabajo->estado)) }}
                                        </span>
                                        @if ($trabajo->estado === 'previsto')
                                            <div class="small text-primary mt-1">
                                                EL trabajo esta pendiente de comenzar por parte del profesional
                                            </div>
                                        @endif
                                        @if ($trabajo->estado === 'en_curso')
                                            <div class="small text-primary mt-1">
                                                El trabajo está iniciado, si ha finalizado, el profesional se lo comunicará
                                            </div>
                                        @endif
                                        @if ($trabajo->estado === 'finalizado')
                                            <div class="small text-primary mt-1">
                                                El trabajo se ha finalizado, ¡Deje su reseña en comentarios!
                                            </div>
                                        @endif
                                        @if ($trabajo->estado === 'cancelado')
                                            <div class="small text-primary mt-1">
                                                El trabajo ha sido cancelado y notificado al cliente y profesional
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Fecha inicio --}}
                                    <td>
                                        {{ $trabajo->fecha_ini?->format('d/m/Y H:i') ?? 'Sin iniciar' }}
                                    </td>

                                    {{-- Fecha fin --}}
                                    <td>
                                        {{ $trabajo->fecha_fin?->format('d/m/Y H:i') ?? 'Sin finalizar' }}
                                    </td>

                                    {{-- Dirección obra --}}
                                    <td>
                                        {{ Str::limit($trabajo->dir_obra ?? 'No indicada', 40, '...') }}
                                    </td>

                                    {{-- Total presupuesto --}}
                                    <td class="text-center">
                                        @if ($presupuesto?->total)
                                            {{ number_format($presupuesto->total, 2, ',', '.') }} €
                                        @else
                                            <span class="text-muted small">No indicado</span>
                                        @endif
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-center">
                                        <div class="d-flex flex-row flex-wrap gap-1 justify-content-center mx-2">

                                            {{-- Ver detalle trabajo (modal Vue) --}}
                                            <button type="button"
                                                class="btn btn-info btn-sm px-2 py-1 d-inline-flex align-items-center gap-1"
                                                @click="openTrabajoModal({{ $trabajo->id }})">
                                                Ver
                                            </button>

                                            {{-- Ver presupuesto PDF (usando ruta protegida) --}}
                                            @if ($presupuesto?->docu_pdf)
                                                <a href="{{ route('presupuestos.ver_pdf', $presupuesto) }}"
                                                    class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1"
                                                    target="_blank">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                    Ver presupuesto
                                                </a>
                                            @endif

                                            {{-- Cancelar trabajo (solo si está previsto y no ha empezado) --}}
                                            @if ($trabajo->estado === 'previsto' && is_null($trabajo->fecha_ini))
                                                <x-usuario.trabajos.btn_cancelar :trabajo="$trabajo" context="desktop" />
                                            @endif

                                            {{-- Valorar (solo si finalizado y sin comentario del cliente) --}}
                                            @if ($trabajo->estado === 'finalizado' && !$yaComentado)
                                                <a href="{{ route('usuario.comentarios.crear', $trabajo) }}"
                                                    class="btn btn-sm btn-warning d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-star"></i>
                                                    Valorar
                                                </a>
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
                    @foreach ($trabajos as $trabajo)
                        @php
                            $presupuesto = $trabajo->presupuesto;
                            $solicitud = $presupuesto?->solicitud;
                            $profesionalPresu = $presupuesto?->profesional;
                            $profesionalSol = $solicitud?->profesional;

                            $yaComentado = $trabajo->comentarios->where('cliente_id', $usuario->id)->isNotEmpty();

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

                                {{-- Título / referencia --}}
                                <div class="mb-2">
                                    <div class="fw-semibold">
                                        @if ($solicitud?->titulo)
                                            {{ $solicitud->titulo }}
                                        @else
                                            Trabajo #{{ $trabajo->id }}
                                        @endif
                                    </div>
                                    <div class="small text-muted">
                                        Ref. trabajo: #{{ $trabajo->id }}
                                        @if ($presupuesto)
                                            · Presupuesto #{{ $presupuesto->id }}
                                        @endif
                                    </div>
                                </div>

                                <div class="small text-muted mb-2">

                                    {{-- Empresa --}}
                                    <div class="mb-1">
                                        <strong>Empresa:</strong>
                                        @if ($profesionalPresu?->empresa || $profesionalSol?->empresa || $solicitud?->empresa)
                                            @if ($profesionalPresu?->empresa)
                                                {{ $profesionalPresu->empresa }}
                                            @endif
                                            @if ($profesionalSol?->empresa && $profesionalSol?->empresa !== ($profesionalPresu->empresa ?? null))
                                                <br>
                                                <span class="text-muted">Sol.: {{ $profesionalSol->empresa }}</span>
                                            @elseif($solicitud?->empresa && !$profesionalSol)
                                                <br>
                                                <span class="text-muted">Sol.: {{ $solicitud->empresa }}</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Sin empresa</span>
                                        @endif
                                    </div>

                                    {{-- Estado + Info estados --}}
                                    <div class="mb-1">
                                        <strong>Estado:</strong>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $trabajo->estado)) }}
                                        </span>
                                        @if ($trabajo->estado === 'previsto')
                                            <div class="small text-primary mt-1">
                                                EL trabajo esta pendiente de comenzar por parte del profesional
                                            </div>
                                        @endif
                                        @if ($trabajo->estado === 'en_curso')
                                            <div class="small text-primary mt-1">
                                                El trabajo está iniciado, si ha finalizado, el profesional se lo comunicará
                                            </div>
                                        @endif
                                        @if ($trabajo->estado === 'finalizado')
                                            <div class="small text-primary mt-1">
                                                El trabajo se ha finalizado, ¡Deje su reseña en comentarios!
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

                                    {{-- Dirección obra --}}
                                    <div class="mb-1">
                                        <strong>Dir. obra:</strong>
                                        {{ Str::limit($trabajo->dir_obra ?? 'No indicada', 40, '...') }}
                                    </div>

                                    {{-- Total presupuesto --}}
                                    <div class="mb-1">
                                        <strong>Total:</strong>
                                        @if ($presupuesto?->total)
                                            {{ number_format($presupuesto->total, 2, ',', '.') }} €
                                        @else
                                            <span class="text-muted">No indicado</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Acciones en columna, ancho completo --}}
                                <div class="d-grid gap-2">
                                    {{-- Ver detalle trabajo --}}
                                    <button type="button" class="btn btn-info btn-sm w-100"
                                        @click="openTrabajoModal({{ $trabajo->id }})">
                                        Ver Detalle
                                    </button>

                                    {{-- Ver presupuesto PDF (usando ruta protegida) --}}
                                    @if ($presupuesto?->docu_pdf)
                                        <a href="{{ route('presupuestos.ver_pdf', $presupuesto) }}"
                                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 w-100"
                                            target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                            Ver presupuesto
                                        </a>
                                    @endif

                                    {{-- Cancelar trabajo --}}
                                    @if ($trabajo->estado === 'previsto' && is_null($trabajo->fecha_ini))
                                        <x-usuario.trabajos.btn_cancelar :trabajo="$trabajo" context="mobile" />
                                    @endif


                                    {{-- Valorar --}}
                                    @if ($trabajo->estado === 'finalizado' && !$yaComentado)
                                        <a href="{{ route('usuario.comentarios.crear', $trabajo) }}"
                                            class="btn btn-sm btn-warning d-inline-flex align-items-center justify-content-center gap-1 w-100">
                                            <i class="bi bi-star"></i>
                                            Valorar
                                        </a>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
                {{-- Modal Vue --}}
                <trabajo-modal ref="trabajoModal"></trabajo-modal>
        </div>

        {{-- Paginación --}}
        <div class="mt-3">
            {{ $trabajos->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
@endsection

<x-alertas_sweet />
