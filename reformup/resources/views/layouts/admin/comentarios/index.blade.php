@extends('layouts.main')

@section('title', 'Gestión de comentarios - Admin - ReformUp')

@section('content')

    <x-navbar />
    {{-- Sidebar admin fija --}}
    <x-admin.admin_sidebar />
    {{-- Bienvenida --}}
    <x-admin.admin_bienvenido />

    <div class="container-fluid main-content-with-sidebar">

        {{-- NAV SUPERIOR SOLO MÓVIL/TABLET --}}
        <x-admin.nav_movil active="comentarios" />

        <div class="container py-4" id="app">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-chat-left-text"></i> Comentarios de usuarios
                </h1>
            </div>

            {{-- Mensajes flash --}}
            <x-alertas.alertasFlash />


            {{-- Buscador combinado: texto + fechas + puntuación --}}
            <form method="GET" action="{{ route('admin.comentarios') }}" class="row g-2 mb-3">
                {{-- Búsqueda por texto --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                        placeholder="Buscar por título, cliente, profesional u opinión...">
                </div>

                {{-- Puntuación mínima --}}
                <div class="col-6 col-md-3 col-lg-2">
                    <select name="puntuacion_min" class="form-select form-select-sm">
                        <option value="">Puntuación mín.</option>
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" @selected(request('puntuacion_min') == $i)>
                                {{ $i }} ★ o más
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Rango de fechas reutilizable --}}
                @include('partials.filtros.rango_fechas')

                {{-- Botón Buscar --}}
                <div class="col-6 col-md-3 col-lg-2 d-grid">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>

                {{-- Botón Limpiar --}}
                <div class="col-6 col-md-3 col-lg-2 d-grid">
                    @if (request('q') || request('estado') || request('fecha_desde') || request('fecha_hasta') || request('puntuacion_min'))
                        <a href="{{ route('admin.comentarios') }}" class="btn btn-sm btn-outline-secondary">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>

            {{-- Filtros por estado --}}
            @if (isset($estados) && is_array($estados))
                <ul class="nav nav-pills mb-3">
                    {{-- Tab "Todos" --}}
                    <li class="nav-item">
                        @php
                            $urlTodos = route(
                                'admin.comentarios',
                                array_filter([
                                    'q' => request('q'),
                                    'puntuacion_min' => request('puntuacion_min'),
                                    'fecha_desde' => request('fecha_desde'),
                                    'fecha_hasta' => request('fecha_hasta'),
                                    // OJO: sin 'estado'
                                ]),
                            );
                        @endphp

                        <a class="nav-link {{ $estado === null || $estado === '' ? 'active' : '' }}"
                            href="{{ $urlTodos }}">
                            Todos
                        </a>
                    </li>

                    {{-- Tabs por cada estado del modelo --}}
                    @foreach ($estados as $valor => $texto)
                        @php
                            $urlEstado = route(
                                'admin.comentarios',
                                array_filter([
                                    'estado' => $valor,
                                    'q' => request('q'),
                                    'puntuacion_min' => request('puntuacion_min'),
                                    'fecha_desde' => request('fecha_desde'),
                                    'fecha_hasta' => request('fecha_hasta'),
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
            @endif


            @if ($comentarios->isEmpty())
                <div class="alert alert-info">
                    No hay comentarios
                    {{ $estado ? 'con estado ' . str_replace('_', ' ', $estado) : 'registrados todavía.' }}
                </div>
            @else
                {{-- ========================== --}}
                {{-- TABLA (solo en lg+)       --}}
                {{-- ========================== --}}
                <div class="table-responsive d-none d-lg-block">
                    <table class="table align-middle">
                        <thead>
                            <tr class="fs-5">
                                {{-- Siempre visible --}}
                                <th class="text-center text-md-start">Trabajo / Solicitud</th>

                                {{-- En tablet (md+) y escritorio --}}
                                <th class="d-none d-md-table-cell">Profesional</th>
                                <th class="d-none d-md-table-cell">Cliente</th>
                                <th class="d-none d-md-table-cell">Puntuación</th>
                                <th class="d-none d-md-table-cell">Estado</th>
                                <th class="d-none d-md-table-cell">Fecha</th>
                                <th class="d-none d-md-table-cell">Opinión</th>

                                {{-- Acciones siempre visibles --}}
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($comentarios as $comentario)
                                @php
                                    $trabajo = $comentario->trabajo;
                                    $presupuesto = $trabajo?->presupuesto;
                                    $solicitud = $presupuesto?->solicitud;
                                    $cliente = $solicitud?->cliente;
                                    $perfilPro = $presupuesto?->profesional;

                                    $badgeClass = match ($comentario->estado) {
                                        'pendiente' => 'bg-warning text-dark',
                                        'publicado' => 'bg-success',
                                        'rechazado' => 'bg-secondary',
                                        default => 'bg-light text-dark',
                                    };
                                @endphp

                                <tr>
                                    {{-- Trabajo / Solicitud --}}
                                    <td>
                                        <strong>
                                            @if ($solicitud?->titulo)
                                                {{ $solicitud->titulo }}
                                            @else
                                                Trabajo #{{ $trabajo?->id }}
                                            @endif
                                        </strong>
                                        <div class="small text-muted">
                                            Comentario #{{ $comentario->id }}
                                        </div>
                                    </td>

                                    {{-- Profesional --}}
                                    <td>
                                        @if ($perfilPro)
                                            {{ $perfilPro->empresa }}<br>
                                            <small class="text-muted">
                                                {{ $perfilPro->ciudad }}{{ $perfilPro->provincia ? ' - ' . $perfilPro->provincia : '' }}
                                            </small>
                                        @else
                                            <span class="text-muted small">Sin profesional</span>
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

                                    {{-- Puntuación --}}
                                    <td class="text-center">
                                        {{ $comentario->puntuacion }} / 5
                                    </td>

                                    {{-- Estado --}}
                                    <td>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($comentario->estado) }}
                                        </span>
                                    </td>

                                    {{-- Fecha --}}
                                    <td>
                                        {{ $comentario->fecha?->format('d/m/Y H:i') ?? $comentario->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="d-none d-lg-table-cell">
                                        @if ($comentario->opinion)
                                            {!! \Illuminate\Support\Str::limit($comentario->opinion, 60, '...') !!}
                                        @else
                                            <span class="text-muted small">Sin opinión</span>
                                        @endif
                                    </td>


                                    {{-- Acciones --}}
                                    <td class="text-center">
                                        <div class="d-flex flex-row flex-wrap justify-content-center gap-2">

                                            {{-- Ver modal Vue --}}
                                            <button type="button"
                                                class="btn btn-sm btn-info d-inline-flex align-items-center gap-1"
                                                @click="openComentarioAdminModal({{ $comentario->id }})">
                                                Ver
                                            </button>

                                            {{-- Editar --}}
                                            <a href="{{ route('admin.comentarios.editar', $comentario) }}"
                                                class="btn btn-sm btn-warning d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>

                                            {{-- Publicar / ocultar --}}
                                            <form action="{{ route('admin.comentarios.toggle_publicado', $comentario) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')

                                                <div class="d-flex flex-column align-items-center">
                                                    <small class="text-muted mb-1">
                                                        Publicación
                                                    </small>

                                                    <div class="form-check form-switch d-flex align-items-center gap-1">
                                                        <input class="form-check-input" type="checkbox"
                                                            onChange="this.form.submit()"
                                                            {{ $comentario->estado === 'publicado' && $comentario->visible ? 'checked' : '' }}>
                                                        <label class="form-check-label small">
                                                            {{ $comentario->estado === 'publicado' && $comentario->visible ? 'Publicado' : 'Oculto' }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </form>

                                            {{-- Rechazar / banear --}}
                                            @if ($comentario->estado !== 'rechazado')
                                                <x-admin.comentarios.btn_rechazar :comentario="$comentario" />
                                            @endif
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ========================== --}}
                {{-- VISTA CARDS (xs–lg)       --}}
                {{-- ========================== --}}
                <div class="d-block d-lg-none">
                    @foreach ($comentarios as $comentario)
                        @php
                            $trabajo = $comentario->trabajo;
                            $presupuesto = $trabajo?->presupuesto;
                            $solicitud = $presupuesto?->solicitud;
                            $cliente = $solicitud?->cliente;
                            $perfilPro = $presupuesto?->profesional;

                            $badgeClass = match ($comentario->estado) {
                                'pendiente' => 'bg-warning text-dark',
                                'publicado' => 'bg-success',
                                'rechazado' => 'bg-secondary',
                                default => 'bg-light text-dark',
                            };
                        @endphp

                        <div class="card mb-3 shadow-sm bg-light">
                            <div class="card-body ">

                                {{-- Cabecera: trabajo / solicitud --}}
                                <div class="mb-2">
                                    <div class="fw-semibold">
                                        @if ($solicitud?->titulo)
                                            {{ $solicitud->titulo }}
                                        @else
                                            Trabajo #{{ $trabajo?->id }}
                                        @endif
                                    </div>
                                    <div class="small text-muted">
                                        Comentario #{{ $comentario->id }}
                                    </div>
                                </div>

                                {{-- Datos principales --}}
                                <div class="small text-muted mb-2">
                                    {{-- Profesional --}}
                                    <div class="mb-1">
                                        <strong>Profesional:</strong>
                                        @if ($perfilPro)
                                            {{ $perfilPro->empresa }}
                                        @else
                                            <span class="text-muted">Sin profesional</span>
                                        @endif
                                    </div>

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

                                    {{-- Puntuación --}}
                                    <div class="mb-1">
                                        <strong>Puntuación:</strong>
                                        {{ $comentario->puntuacion }} / 5
                                    </div>

                                    {{-- Estado --}}
                                    <div class="mb-1">
                                        <strong>Estado:</strong>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($comentario->estado) }}
                                        </span>
                                    </div>

                                    {{-- Fecha --}}
                                    <div class="mb-1">
                                        <strong>Fecha:</strong>
                                        {{ $comentario->fecha?->format('d/m/Y H:i') ?? $comentario->created_at?->format('d/m/Y H:i') }}
                                    </div>

                                    <td class="d-none d-lg-table-cell">
                                        @if ($comentario->opinion)
                                            {{ Str::limit(strip_tags($comentario->opinion), 60, '...') }}
                                        @else
                                            <span class="text-muted small">Sin opinión</span>
                                        @endif
                                    </td>

                                </div>

                                {{-- Acciones --}}
                                <div class="d-grid gap-2">
                                    {{-- Ver modal --}}
                                    <button type="button"
                                        class="btn btn-sm btn-info d-inline-flex align-items-center justify-content-center gap-1"
                                        @click="openComentarioAdminModal({{ $comentario->id }})">
                                        Ver
                                    </button>

                                    {{-- Editar --}}
                                    <a href="{{ route('admin.comentarios.editar', $comentario) }}"
                                        class="btn btn-sm btn-warning d-inline-flex align-items-center justify-content-center gap-1">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>

                                    {{-- Publicar / ocultar --}}
                                    <form action="{{ route('admin.comentarios.toggle_publicado', $comentario) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <div class="form-check form-switch d-flex align-items-center gap-2">
                                            <input class="form-check-input" type="checkbox" onChange="this.form.submit()"
                                                {{ $comentario->estado === 'publicado' && $comentario->visible ? 'checked' : '' }}>
                                            <label class="form-check-label small">
                                                {{ $comentario->estado === 'publicado' && $comentario->visible ? 'Publicado' : 'Oculto' }}
                                            </label>
                                        </div>
                                    </form>

                                    {{-- Rechazar / banear --}}
                                    @if ($comentario->estado !== 'rechazado')
                                        <x-admin.comentarios.btn_rechazar :comentario="$comentario" />
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="mt-3">
                    {{ $comentarios->links('pagination::bootstrap-5') }}
                </div>
            @endif

            {{-- Modal Vue para ver comentario --}}
            <comentario-admin-modal ref="comentarioAdminModal"></comentario-admin-modal>
        </div>
    </div>
@endsection
