@extends('layouts.main')

@section('title', 'Comentarios de mis clientes - ReformUp')

@section('content')

    <x-navbar />
    <x-profesional.profesional_sidebar />
    <x-profesional.profesional_bienvenido />

    <div class="container-fluid main-content-with-sidebar">
        <x-profesional.nav_movil active="comentarios" />

        <div class="container py-4" id="app">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-chat-left-quote"></i> Comentarios de mis clientes
                </h1>
            </div>

            {{-- Flash messages --}}
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
                    <a class="nav-link {{ $estado === null ? 'active' : '' }}"
                        href="{{ route('profesional.comentarios.index') }}">
                        Todos
                    </a>
                </li>

                {{-- Estados del modelo --}}
                @foreach ($estados as $valor => $texto)
                    <li class="nav-item">
                        <a class="nav-link {{ $estado === $valor ? 'active' : '' }}"
                            href="{{ route('profesional.comentarios.index', ['estado' => $valor]) }}">
                            {{ $texto }}
                        </a>
                    </li>
                @endforeach
            </ul>

            @if ($comentarios->isEmpty())
                <div class="alert alert-info">
                    No tienes comentarios
                    {{ $estado ? 'con estado ' . $estados[$estado] : 'todavía' }}.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="bg-secondary">Trabajo / Solicitud</th>
                                <th class="d-none d-md-table-cell bg-secondary">Cliente</th>
                                <th class="bg-secondary">Puntuación</th>
                                <th class="bg-secondary">Estado</th>
                                <th class="d-none d-md-table-cell bg-secondary">Fecha</th>
                                <th class="d-none d-lg-table-cell bg-secondary">Opinión</th>
                                <th class="text-center bg-secondary">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($comentarios as $comentario)
                                @php
                                    $trabajo = $comentario->trabajo;
                                    $presupuesto = $trabajo?->presupuesto;
                                    $solicitud = $presupuesto?->solicitud;
                                    $cliente = $solicitud?->cliente;
                                @endphp

                                <tr>
                                    {{-- Trabajo / Solicitud + bloque móvil --}}
                                    <td>
                                        <strong>
                                            @if ($solicitud?->titulo)
                                                {{ $solicitud->titulo }}
                                            @else
                                                Trabajo #{{ $trabajo?->id }}
                                            @endif
                                        </strong>

                                        {{-- Versión móvil: detalles debajo --}}
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

                                            {{-- Opinión (resumen) --}}
                                            @if ($comentario->opinion)
                                                <span class="d-block">
                                                    <span class="fw-semibold">Opinión:</span>
                                                    {{ \Illuminate\Support\Str::limit($comentario->opinion, 80, '...') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Cliente (solo escritorio md+) --}}
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

                                    {{-- Fecha (solo escritorio md+) --}}
                                    <td class="d-none d-md-table-cell">
                                        {{ $comentario->fecha?->format('d/m/Y H:i') ?? $comentario->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- Opinión (solo escritorio lg+) --}}
                                    <td class="d-none d-lg-table-cell">
                                        @if ($comentario->opinion)
                                            {{ \Illuminate\Support\Str::limit($comentario->opinion, 30, '...') }}
                                        @else
                                            <span class="text-muted small">Sin opinión</span>
                                        @endif
                                    </td>

                                    {{-- Boton Mostrar --}}
                                    <td class="text-center">
                                        {{-- Botón ver (abre modal Vue) --}}
                                        <button type="button" class="btn btn-sm btn-outline-primary d-block mt-2"
                                            @click="openComentarioModalPro({{ $comentario->id }})">
                                            Ver detalle
                                        </button>
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
            <comentario-pro-modal ref="ComentarioModalPro"></comentario-pro-modal>
        </div>

    </div>
@endsection


<x-alertas_sweet />
