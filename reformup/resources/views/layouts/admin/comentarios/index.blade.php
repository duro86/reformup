@extends('layouts.main')

@section('title', 'Gestión de comentarios - Admin - ReformUp')

@section('content')

    <x-navbar />
    {{-- Sidebar admin fija --}}
    <x-admin.admin_sidebar />

    <div class="container-fluid main-content-with-sidebar">

        {{-- NAV SUPERIOR SOLO MÓVIL/TABLET --}}
        <x-admin.nav_movil active="comentarios" />

        <div class="container py-4" id="app">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-chat-left-text"></i> Comentarios de usuarios
                </h1>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($comentarios->isEmpty())
                <div class="alert alert-info">
                    No hay comentarios registrados todavía.
                </div>
            @else
                <style>
                    /* Para que el switch al publicarse sea verde */
                    .form-switch .form-check-input:checked {
                        background-color: #198754;
                        border-color: #198754;
                    }
                </style>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th class="bg-secondary text-white">Trabajo / Solicitud</th>
                                <th class="d-none d-lg-table-cell bg-secondary text-white">Profesional</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Cliente</th>
                                <th class="bg-secondary text-white">Puntuación</th>
                                <th class="bg-secondary text-white">Estado</th>
                                <th class="d-none d-md-table-cell bg-secondary text-white">Fecha</th>
                                <th class="d-none d-lg-table-cell bg-secondary text-white">Opinión</th>
                                <th class="text-center bg-secondary text-white">Acciones</th>
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
                                @endphp

                                <tr>
                                    {{-- Trabajo / Solicitud + vista móvil --}}
                                    <td>
                                        <strong>
                                            @if ($solicitud?->titulo)
                                                {{ $solicitud->titulo }}
                                            @else
                                                Trabajo #{{ $trabajo?->id }}
                                            @endif
                                        </strong>

                                        <div class="small text-muted d-block d-md-none mt-1">
                                            {{-- Profesional --}}
                                            @if ($perfilPro)
                                                <span class="d-block">
                                                    <span class="fw-semibold">Profesional:</span>
                                                    {{ $perfilPro->empresa }}
                                                </span>
                                            @endif

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

                                            {{-- Puntuación --}}
                                            <span class="d-block">
                                                <span class="fw-semibold">Puntuación:</span>
                                                {{ $comentario->puntuacion }} / 5
                                            </span>

                                            {{-- Estado --}}
                                            @php
                                                $badgeClass = match ($comentario->estado) {
                                                    'pendiente' => 'bg-warning text-dark',
                                                    'publicado' => 'bg-success',
                                                    'rechazado' => 'bg-secondary',
                                                    default => 'bg-light text-dark',
                                                };
                                            @endphp
                                            <span class="d-block">
                                                <span class="fw-semibold">Estado:</span>
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ ucfirst($comentario->estado) }}
                                                </span>
                                            </span>

                                            {{-- Fecha --}}
                                            <span class="d-block">
                                                <span class="fw-semibold">Fecha:</span>
                                                {{ $comentario->fecha?->format('d/m/Y H:i') ?? $comentario->created_at?->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Profesional (escritorio lg) --}}
                                    <td class="d-none d-lg-table-cell">
                                        @if ($perfilPro)
                                            {{ $perfilPro->empresa }}<br>
                                            <small class="text-muted">
                                                {{ $perfilPro->ciudad }}{{ $perfilPro->provincia ? ' - ' . $perfilPro->provincia : '' }}
                                            </small>
                                        @else
                                            <span class="text-muted small">Sin profesional</span>
                                        @endif
                                    </td>

                                    {{-- Cliente (md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        @if ($cliente)
                                            {{ $cliente->nombre ?? $cliente->name }}
                                            {{ $cliente->apellidos ?? '' }}<br>
                                            <small class="text-muted">{{ $cliente->email }}</small>
                                        @else
                                            <span class="text-muted small">Sin cliente</span>
                                        @endif
                                    </td>

                                    {{-- Puntuación --}}
                                    <td>
                                        {{ $comentario->puntuacion }} / 5
                                    </td>

                                    {{-- Estado --}}
                                    <td>
                                        @php
                                            $badgeClass = match ($comentario->estado) {
                                                'pendiente' => 'bg-warning text-dark',
                                                'publicado' => 'bg-success',
                                                'rechazado' => 'bg-secondary',
                                                default => 'bg-light text-dark',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($comentario->estado) }}
                                        </span>
                                    </td>

                                    {{-- Fecha (md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        {{ $comentario->fecha?->format('d/m/Y H:i') ?? $comentario->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- Opinión (lg+) --}}
                                    <td class="d-none d-lg-table-cell">
                                        @if ($comentario->opinion)
                                            {{ \Illuminate\Support\Str::limit($comentario->opinion, 60, '...') }}
                                        @else
                                            <span class="text-muted small">Sin opinión</span>
                                        @endif
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-center">
                                        <div class="d-flex flex-column flex-md-row flex-wrap justify-content-center gap-2">

                                            {{-- Ver (modal Vue) --}}
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1"
                                                @click="openComentarioAdminModal({{ $comentario->id }})">
                                                <i class="bi bi-eye"></i> Ver
                                            </button>

                                            {{-- Editar --}}
                                            <a href="{{ route('admin.comentarios.editar', $comentario) }}"
                                                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>

                                            {{-- Switch publicar / despublicar --}}
                                            <form action="{{ route('admin.comentarios.toggle_publicado', $comentario) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')

                                                <div class="d-flex flex-column align-items-center">
                                                    {{-- Etiqueta encima en pantallas grandes --}}
                                                    <small class="text-muted d-none d-md-block mb-1">
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

                <div class="mt-3">
                    {{ $comentarios->links() }}
                </div>
            @endif

            {{-- Modal Vue para ver comentario --}}
            <comentario-admin-modal ref="comentarioAdminModal"></comentario-admin-modal>
        </div>
    </div>
@endsection
