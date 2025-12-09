@extends('layouts.main')

@section('title', 'Gestión de oficios - Admin - ReformUp')

@section('content')

    <x-navbar />
    <x-admin.admin_sidebar />

    <div class="container-fluid main-content-with-sidebar">

        {{-- NAV MÓVIL ADMIN --}}
        <x-admin.nav_movil active="oficios" />

        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3 mb-0">Gestión de oficios</h1>
            </div>

            {{-- Mensajes flash --}}
            <x-alertas.alertasFlash />

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Nuevo oficio</h5>
                            <form action="{{ route('admin.oficios.guardar') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" name="nombre" id="nombre"
                                        class="form-control @error('nombre') is-invalid @enderror"
                                        value="{{ old('nombre') }}" required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción (opcional)</label>
                                    <textarea name="descripcion" id="descripcion" rows="3"
                                        class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-success w-100 bg-primary text-white">
                                    <i class="bi bi-plus-circle"></i> Crear oficio
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Listado de oficios</h5>

                            @if ($oficios->isEmpty())
                                <p class="text-muted">No hay oficios registrados todavía.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Slug</th>
                                                <th>Descripción</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($oficios as $oficio)
                                                <tr>
                                                    <td>{{ $oficio->nombre }}</td>
                                                    <td class="text-muted">{{ $oficio->slug }}</td>
                                                    <td>{{ Str::limit($oficio->descripcion, 80) }}</td>
                                                    <td class="text-end">
                                                        {{-- Editar: puedes llevar a página aparte o usar modal --}}
                                                        <a href="{{ route('admin.oficios.editar', $oficio) }}"
                                                            class="btn btn-sm w-100 btn-outline-primary">
                                                            <i class="bi bi-pencil">Editar</i>
                                                        </a>

                                                        {{-- Botón eliminar con confirmación --}}
                                                        <x-admin.oficios.btn_eliminar :oficio="$oficio" />
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{ $oficios->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
