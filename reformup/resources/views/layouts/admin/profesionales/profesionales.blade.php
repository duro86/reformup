@extends('layouts.main')

@section('title', 'Listado de profesionales - ReformUp')

@section('content')

    {{-- Navbar principal --}}
    <x-navbar />

    {{-- Sidebar ADMIN --}}
    <x-admin.admin_sidebar />
    {{-- Listado profesionales --}}
    <x-admin.nav_movil active="profesionales" />

    {{-- Contenido principal respetando el sidebar --}}
    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">

            {{-- NAV SUPERIOR SOLO EN MÓVIL/TABLET --}}
            <div class="d-lg-none mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-2">
                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-success">
                                Panel
                            </a>
                            <a href="{{ route('admin.usuarios') }}" class="btn btn-sm btn-outline-success">
                                Usuarios
                            </a>
                            <a href="{{ route('admin.profesionales') }}" class="btn btn-sm btn-outline-success">
                                Profesionales
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-success">
                                Solicitudes
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-success">
                                Presupuestos
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-success">
                                Trabajos
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-success">
                                Comentarios
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bienvenida / info superior --}}
            <x-user_bienvenido />

            {{-- Zona Vue --}}
            <div id="app">
                {{-- Modal profesional --}}
                <professional-modal ref="professionalModal"></professional-modal>

                {{-- Listado Profesionales --}}
                <div class="container p-3">

                    <h1
                        class="text-center d-flex flex-column flex-md-row align-items-center justify-content-center gap-3 mb-4">
                        <span>Listado de Profesionales</span>

                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            {{-- Añadir Profesional nuevo --}}
                            <a href="{{ route('registrar.profesional.opciones') }}" class="btn btn-sm"
                                style="background-color: #718355; color: white;">
                                <i class="bi bi-plus-lg"></i> Añadir profesional
                            </a>

                            {{-- Exportar PDF profesionales --}}
                            <a href="#" class="btn btn-sm" style="background-color: #B5C99A; color: black;">
                                Exportar PDF profesionales
                            </a>
                        </div>
                    </h1>

                    {{-- Tabla profesionales --}}
                    <div class="table-responsive">
                        <table class="table table-sm">
                            {{-- Encabezados --}}
                            <thead>
                                <tr style="font-size: 0.875rem;">
                                    <th>Avatar</th>
                                    <th>Usuario</th>
                                    <th>Email Usuario</th>
                                    <th>Empresa</th>
                                    <th>CIF</th>
                                    <th>Email empresa</th>
                                    <th>Teléfono empresa</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.875rem;">
                                @foreach ($profesionales as $perfil)
                                    @php
                                        $user = $perfil->user; // puede ser null si no hay usuario asociado
                                    @endphp
                                    <tr>
                                        {{-- Avatar Empresa --}}
                                        <td>
                                            @if ($perfil && $perfil->avatar)
                                                <img src="{{ Storage::url($perfil->avatar) }}" alt="avatar"
                                                    class="rounded-circle" style="width:30px;height:30px;object-fit:cover">
                                            @else
                                                <i class="bi bi-person-circle" style="font-size: 1rem;"></i>
                                            @endif
                                        </td>

                                        {{-- Datos usuario / empresa --}}
                                        <td>{{ $user?->nombre }}</td>
                                        <td>{{ $user?->email }}</td>
                                        <td>{{ $perfil->empresa }}</td>
                                        <td>{{ $perfil->cif }}</td>
                                        <td>{{ $perfil->email_empresa }}</td>
                                        <td>{{ $perfil->telefono_empresa }}</td>

                                        {{-- Roles del usuario --}}
                                        <td>
                                            @if ($user)
                                                {{ $user->getRoleNames()->implode(', ') }}
                                            @else
                                                <span class="text-muted">Sin usuario</span>
                                            @endif
                                        </td>

                                        {{-- Acciones --}}
                                        <td>
                                            {{-- Ver modal profesional --}}
                                            <button class="btn btn-info btn-sm px-2 py-1 mb-1 mb-md-0"
                                                @click="openProfessionalModal({{ $perfil->id }})">
                                                Ver
                                            </button>

                                            {{-- Editar perfil profesional --}}
                                            <a href="{{ route('admin.profesionales.editar', $perfil->id) }}"
                                                class="btn btn-warning btn-sm px-2 py-1 mb-1 mb-md-0">
                                                Editar
                                            </a>

                                            {{-- Eliminar SOLO perfil profesional --}}
                                            <form id="delete-prof-{{ $perfil->id }}"
                                                action="{{ route('admin.profesionales.eliminar', $perfil->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')

                                                <delete-professional-button form-id="delete-prof-{{ $perfil->id }}"
                                                    empresa="{{ $perfil->empresa }}"
                                                    :tiene-user="{{ $user ? 'true' : 'false' }}"
                                                    user-nombre="{{ $user?->nombre }} {{ $user?->apellidos }}"
                                                    user-email="{{ $user?->email }}">
                                                </delete-professional-button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    {{ $profesionales->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>

@endsection

{{-- Alertas --}}
<x-alertas_sweet />
