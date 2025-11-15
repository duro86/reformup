@extends('layouts.main')
@section('content')
    <div class="d-flex" style="gap: 1rem;">

        {{-- Sidebar --}}
        <div style="width: 220px;">
            <x-admin_sidebar />
        </div>

        {{-- Contenido principal --}}
        <div class="flex-grow-1">
            <x-user_bienvenido />
            <div id="app">
                <user-modal ref="userModal"></user-modal>
                <div class="container p-3">
                    <h1 class="text-center d-flex align-items-center justify-content-center gap-3">
                        Listado de Usuarios
                        <a href="{{ route('admin.admin.form.registrar.cliente') }}" class="btn btn-sm"
                            style="background-color: #718355; color: white;">
                            <i class="bi bi-plus-lg"></i> Añadir usuario
                        </a>

                        <a href="#" class="btn btn-sm" style="background-color: #B5C99A; color: black;">
                            Exportar PDF usuario
                        </a>
                    </h1>

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
                                        <button class="btn btn-info btn-sm px-2 py-1"
                                            @click="openUserModal({{ $usuario->id }})">
                                            Ver
                                        </button>

                                        <a href="{{ route('admin.usuarios.editar', $usuario->id) }}"
                                            class="btn btn-warning btn-sm px-2 py-1">
                                            Editar
                                        </a>

                                        {{-- Elimanr usuarios --}}
                                        <form id="delete-user-{{ $usuario->id }}"
                                            action="{{ route('admin.usuarios.eliminar', $usuario->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <delete-user-button form-id="delete-user-{{ $usuario->id }}"
                                                user-nombre="{{ $usuario->nombre }} {{ $usuario->apellidos }}"
                                                user-email="{{ $usuario->email }}"
                                                :tiene-perfil="{{ $usuario->perfil_Profesional ? 'true' : 'false' }}"></delete-user-button>
                                        </form>

                                    </td>
                                    {{-- Mostramos roles separados por una , --}}
                                    <td>{{ $usuario->getRoleNames()->implode(', ') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $usuarios->links('pagination::bootstrap-5') }}
                </div>


            </div>

        </div>
    </div>
@endsection

<x-alertas_sweet />
