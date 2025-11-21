@extends('layouts.main')

@section('title', 'Mis comentarios - ReformUp')

@section('content')
    <x-navbar />
    <x-usuario.usuario_sidebar />
    <x-usuario.user_bienvenido />

    <div class="container-fluid main-content-with-sidebar">
        <x-usuario.nav_movil active="comentarios" />
        <div class="container py-4">
            <h1 class="h4 mb-3">
                Mis comentarios
            </h1>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($comentarios->isEmpty())
                <div class="alert alert-info">
                    Todavía no has dejado ningún comentario.
                </div>
            @else
                <div class="table-responsive-md">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th class="bg-secondary">Trabajo</th>
                                <th class="d-none d-md-table-cell bg-secondary">Profesional</th>
                                <th class="bg-secondary">Puntuación</th>
                                <th class="d-none d-md-table-cell bg-secondary">Estado</th>
                                <th class="d-none d-md-table-cell bg-secondary">Fecha</th>
                                <th class="text-end bg-secondary">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($comentarios as $comentario)
                                @php
                                    $trabajo = $comentario->trabajo;
                                    $presu = $trabajo?->presupuesto;
                                    $solicitud = $presu?->solicitud;
                                    $pro = $presu?->profesional;
                                @endphp
                                <tr>
                                    <td>
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


                                        {{-- Versión móvil: estado/fecha --}}
                                        <div class="d-md-none mt-1 small text-muted">
                                            @php
                                                $badge = match ($comentario->estado) {
                                                    'pendiente' => 'bg-warning text-dark',
                                                    'publicado' => 'bg-success',
                                                    'rechazado' => 'bg-secondary',
                                                    default => 'bg-light text-dark',
                                                };
                                            @endphp
                                            @if ($pro)
                                                <span
                                                    class="text-dark bg-secondary rounded px-1">{{ $pro->empresa }}</span><br>
                                                <small>{{ $pro->email_empresa }}</small>
                                            @else
                                                <span class="text-dark">Sin profesional</span>
                                            @endif
                                            <br>
                                            <span class="badge {{ $badge }}">
                                                {{ ucfirst($comentario->estado) }}
                                            </span><br>
                                            <span class="ms-1">
                                                {{ optional($comentario->fecha)->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="d-none d-md-table-cell">
                                        @if ($pro)
                                            {{ $pro->empresa }}<br>
                                            <small class="text-muted">{{ $pro->ciudad }}
                                                {{ $pro->provincia ? ' - ' . $pro->provincia : '' }}</small>
                                        @else
                                            <span class="text-muted small">Sin profesional</span>
                                        @endif
                                    </td>

                                    <td>
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $comentario->puntuacion)
                                                <i class="bi bi-star-fill text-warning"></i>
                                            @else
                                                <i class="bi bi-star text-muted"></i>
                                            @endif
                                        @endfor
                                    </td>

                                    <td class="d-none d-md-table-cell">
                                        <span class="badge {{ $badge }}">
                                            {{ ucfirst($comentario->estado) }}
                                        </span>
                                    </td>

                                    <td class="d-none d-md-table-cell">
                                        {{ optional($comentario->fecha)->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="text-end">
                                        {{-- Ver (simplemente mostrar en modal o ir a una vista si quieres) --}}
                                        @if ($comentario->opinion)
                                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#comentarioUserModal{{ $comentario->id }}">
                                                Ver
                                            </button>
                                        @endif

                                        {{-- Editar solo si está pendiente --}}
                                        @if ($comentario->estado === 'pendiente')
                                            <a href="{{ route('usuario.comentarios.editar', $comentario) }}"
                                                class="btn btn-sm btn-primary mb-1">
                                                Editar
                                            </a>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Modal ver opinión --}}
                                @if ($comentario->opinion)
                                    <div class="modal fade" id="comentarioUserModal{{ $comentario->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        Comentario #{{ $comentario->id }}
                                                    </h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Puntuación:</strong> {{ $comentario->puntuacion }} / 5</p>
                                                    <p><strong>Estado:</strong> {{ ucfirst($comentario->estado) }}</p>
                                                    <p><strong>Opinión:</strong><br>{{ $comentario->opinion }}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cerrar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $comentarios->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

<x-alertas_sweet />
