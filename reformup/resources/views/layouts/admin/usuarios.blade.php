@extends('layouts.main')

@section('title', 'Listado de usuarios - ReformUp')

@section('content')

    {{-- Navbar principal --}}
    <x-navbar />

    {{-- Sidebar ADMIN (escritorio + móvil con overlay) --}}
    <x-admin.admin_sidebar />
    <x-user_bienvenido />
    {{-- Listado usuarios --}}
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

                            <a href="#" class="btn btn-sm" style="background-color: #B5C99A; color: black;">
                                Exportar PDF usuario
                            </a>
                        </div>
                    </h1>

                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr style="font-size: 0.875rem;">
                                    <th>Avatar</th>
                                    <th>Nombre</th>
                                    <th>Apellidos</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Acciones</th>
                                    <th>Rol</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.875rem;">
                                @foreach ($usuarios as $usuario)
                                    <tr>
                                        <td>
                                            @if ($usuario->avatar)
                                                <img src="{{ Storage::url($usuario->avatar) }}" alt="avatar"
                                                    class="rounded-circle" style="width:30px;height:30px;object-fit:cover">
                                            @else
                                                <i class="bi bi-person-circle" style="font-size: 1rem;"></i>
                                            @endif
                                        </td>
                                        <td>{{ $usuario->nombre }}</td>
                                        <td>{{ $usuario->apellidos }}</td>
                                        <td>{{ $usuario->email }}</td>
                                        <td>{{ $usuario->telefono }}</td>
                                        <td>
                                            {{-- Ver modal usuario --}}
                                            <button class="btn btn-info btn-sm px-2 py-1 mb-1 mb-md-0"
                                                @click="openUserModal({{ $usuario->id }})">
                                                Ver
                                            </button>

                                            <a href="{{ route('admin.usuarios.editar', $usuario->id) }}"
                                                class="btn btn-warning btn-sm px-2 py-1 mb-1 mb-md-0">
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
                                        <td>{{ $usuario->getRoleNames()->implode(', ') }}</td>
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
