@extends('layouts.main')

@section('title', 'Listado de usuarios - ReformUp')

@section('content')

    {{-- Navbar principal --}}
    <x-navbar />

    {{-- Sidebar ADMIN (escritorio + móvil con overlay) --}}
    <x-admin.admin_sidebar />

    {{-- Bienvenida --}}
    <x-admin.admin_bienvenido />

    {{-- Menú superior móvil --}}
    <x-admin.nav_movil active="usuarios" />

    {{-- Contenido principal que ya respeta el sidebar --}}
    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">

            {{-- CONTENIDO VUE + TABLA --}}
            <div id="app">

                <div class="container p-3">
                    <h1
                        class="text-center d-flex flex-column flex-md-row align-items-center justify-content-center gap-3 mb-4">
                        <span>Listado de Usuarios</span>

                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            <a href="{{ route('admin.admin.form.registrar.cliente') }}" class="btn btn-sm"
                                style="background-color: #718355; color: white;">
                                <i class="bi bi-plus-lg"></i> Añadir usuario
                            </a>

                            <a href="{{ route('admin.usuarios.exportar.pdf') }}" class="btn btn-sm bg-secondary"
                                target="_blank">
                                Exportar PDF todos usuarios
                            </a>
                            <a href="{{ route(
                                'admin.usuarios.exportarPaginaPdf',
                                array_merge(
                                    request()->only('q'), // mantiene el filtro actual si existe
                                    ['page' => $usuarios->currentPage()], // página actual
                                ),
                            ) }}"
                                class="btn btn-sm bg-light" target="_blank">
                                Exportar PDF esta página
                            </a>
                        </div>
                    </h1>

                    {{-- Buscador --}}
                    <form method="GET" action="{{ route('admin.usuarios') }}" class="row g-2 mb-3">
                        <div class="col-12 col-md-6 col-lg-4">
                            <input type="text" name="q" value="{{ request('q') }}"
                                class="form-control form-control-sm" placeholder="Buscar por nombre, email o teléfono...">
                        </div>
                        <div class="col-6 col-md-3 col-lg-2 d-grid">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2 d-grid">
                            @if (request('q'))
                                <a href="{{ route('admin.usuarios') }}" class="btn btn-sm btn-outline-secondary">
                                    Limpiar
                                </a>
                            @endif
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr class="fs-5">
                                    <th class="text-center text-md-start">Usuario</th>
                                    <th class="d-none d-md-table-cell">Email</th>
                                    <th class="d-none d-md-table-cell">Teléfono</th>
                                    <th class="d-none d-md-table-cell">Rol</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($usuarios as $usuario)
                                    <tr>
                                        {{-- Columna USUARIO: avatar + nombre + info extra en móvil --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                {{-- Avatar --}}
                                                @if ($usuario->avatar)
                                                    <img src="{{ Storage::url($usuario->avatar) }}" alt="avatar"
                                                        class="rounded-circle"
                                                        style="width:30px;height:30px;object-fit:cover">
                                                @else
                                                    <i class="bi bi-person-circle" style="font-size: 1.4rem;"></i>
                                                @endif

                                                <div>
                                                    <div class="fw-semibold">
                                                        {{ $usuario->nombre }} {{ $usuario->apellidos }}
                                                    </div>

                                                    {{-- En móvil mostramos aquí email / teléfono / rol --}}
                                                    <div class="small text-muted d-md-none mt-1">
                                                        {{ $usuario->email }} <br>
                                                        @if ($usuario->telefono)
                                                            Tel: {{ $usuario->telefono }} <br>
                                                        @endif
                                                        Rol: {{ $usuario->getRoleNames()->implode(', ') ?: '—' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Email SOLO en escritorio --}}
                                        <td class="d-none d-md-table-cell">
                                            {{ $usuario->email }}
                                        </td>

                                        {{-- Teléfono SOLO en escritorio --}}
                                        <td class="d-none d-md-table-cell">
                                            {{ $usuario->telefono ?: '—' }}
                                        </td>

                                        {{-- Rol SOLO en escritorio --}}
                                        <td class="d-none d-md-table-cell">
                                            {{ $usuario->getRoleNames()->implode(', ') ?: '—' }}
                                        </td>

                                        {{-- Acciones --}}
                                        <td class="text-center">
                                            {{-- Ver modal usuario --}}
                                            <button class="btn btn-info btn-sm px-2 py-1 mb-1 mb-md-0"
                                                @click="openUserModal({{ $usuario->id }})">
                                                Ver
                                            </button>

                                            {{-- Editar --}}
                                            <a href="{{ route('admin.usuarios.editar', $usuario->id) }}"
                                                class="btn btn-warning btn-sm px-2 py-1 mx-1 mb-1 mb-md-0">
                                                Editar
                                            </a>

                                            {{-- Eliminar usuario --}}
                                            <form id="delete-user-{{ $usuario->id }}"
                                                action="{{ route('admin.usuarios.eliminar', $usuario->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')

                                                <delete-user-button form-id="delete-user-{{ $usuario->id }}"
                                                    user-nombre="{{ $usuario->nombre }} {{ $usuario->apellidos }}"
                                                    user-email="{{ $usuario->email }}"
                                                    :tiene-perfil="{{ $usuario->perfil_Profesional ? 'true' : 'false' }}">
                                                </delete-user-button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $usuarios->links('pagination::bootstrap-5') }}
                </div>

                {{-- Modal Vue --}}
                <user-modal ref="userModal"></user-modal>
            </div>

        </div>
    </div>

@endsection

<x-alertas_sweet />
