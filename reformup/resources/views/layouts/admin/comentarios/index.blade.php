@extends('layouts.admin.main')

@section('title', 'Comentarios - Admin')

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-3">Comentarios de clientes</h1>

    {{-- Filtro por estado --}}
    <form method="GET" class="mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-0">Estado</label>
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="pendiente" @selected($estado === 'pendiente')>Pendientes</option>
                    <option value="publicado" @selected($estado === 'publicado')>Publicados</option>
                    <option value="rechazado" @selected($estado === 'rechazado')>Rechazados</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary">Filtrar</button>
            </div>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($comentarios->isEmpty())
        <div class="alert alert-info">No hay comentarios.</div>
    @else
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Profesional</th>
                    <th>Puntuación</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comentarios as $comentario)
                    @php
                        $cliente   = $comentario->cliente;
                        $trabajo   = $comentario->trabajo;
                        $presupuesto = $trabajo?->presupuesto;
                        $perfilPro = $presupuesto?->profesional;
                    @endphp
                    <tr>
                        <td>#{{ $comentario->id }}</td>
                        <td>
                            @if($cliente)
                                {{ $cliente->nombre ?? $cliente->name }} {{ $cliente->apellidos ?? '' }}<br>
                                <small class="text-muted">{{ $cliente->email }}</small>
                            @else
                                <span class="text-muted">Sin cliente</span>
                            @endif
                        </td>
                        <td>
                            @if($perfilPro)
                                {{ $perfilPro->empresa }}<br>
                                <small class="text-muted">{{ $perfilPro->email_empresa }}</small>
                            @else
                                <span class="text-muted">Sin profesional</span>
                            @endif
                        </td>
                        <td>
                            @for($i=1; $i<=5; $i++)
                                @if($i <= $comentario->puntuacion)
                                    <i class="bi bi-star-fill text-warning"></i>
                                @else
                                    <i class="bi bi-star text-muted"></i>
                                @endif
                            @endfor
                        </td>
                        <td>
                            @php
                                $badge = match($comentario->estado) {
                                    'pendiente' => 'bg-warning text-dark',
                                    'publicado' => 'bg-success',
                                    'rechazado' => 'bg-secondary',
                                    default     => 'bg-light text-dark',
                                };
                            @endphp
                            <span class="badge {{ $badge }}">
                                {{ ucfirst($comentario->estado) }}
                            </span>
                        </td>
                        <td>
                            {{ optional($comentario->fecha)->format('d/m/Y H:i') ?? $comentario->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="text-end">
                            {{-- Ver (modal simple) --}}
                            <button type="button"
                                    class="btn btn-sm btn-info mb-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#comentarioModal{{ $comentario->id }}">
                                Ver
                            </button>

                            @if($comentario->estado === 'pendiente')
                                <form action="{{ route('admin.comentarios.publicar', $comentario) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success mb-1">
                                        Publicar
                                    </button>
                                </form>

                                <form action="{{ route('admin.comentarios.rechazar', $comentario) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-danger mb-1">
                                        Rechazar
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>

                    {{-- Modal --}}
                    <div class="modal fade" id="comentarioModal{{ $comentario->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">
                                Comentario #{{ $comentario->id }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            <p><strong>Cliente:</strong>
                                @if($cliente)
                                    {{ $cliente->nombre ?? $cliente->name }} {{ $cliente->apellidos ?? '' }}
                                @else
                                    <span class="text-muted">Sin cliente</span>
                                @endif
                            </p>

                            <p><strong>Profesional:</strong>
                                @if($perfilPro)
                                    {{ $perfilPro->empresa }}
                                @else
                                    <span class="text-muted">Sin profesional</span>
                                @endif
                            </p>

                            <p><strong>Puntuación:</strong>
                                {{ $comentario->puntuacion }} / 5
                            </p>

                            <p><strong>Opinión:</strong><br>
                                @if($comentario->opinion)
                                    {{ $comentario->opinion }}
                                @else
                                    <span class="text-muted">Sin texto de opinión.</span>
                                @endif
                            </p>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cerrar
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>

                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $comentarios->links() }}
    </div>
    @endif
</div>
@endsection
