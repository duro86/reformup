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
                {{-- Modal profesional --}}
                <professional-modal ref="professionalModal"></professional-modal>

                <div class="container p-3">
                    <h1 class="text-center d-flex align-items-center justify-content-center gap-3">
                        Listado de Profesionales

                        {{-- Si más adelante añadir un botón para crear profesional manualmente, lo pones aquí --}}
                        {{-- 
                        <a href="{{ route('admin.profesionales.create') }}" class="btn btn-sm"
                           style="background-color: #718355; color: white;">
                            <i class="bi bi-plus-lg"></i> Añadir profesional
                        </a>

                        <a href="#" class="btn btn-sm" style="background-color: #B5C99A; color: black;">
                            Exportar PDF profesionales
                        </a>
                        --}}
                    </h1>

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
                                    {{-- Avatar Empresa) --}}
                                    <td>
                                        @if ($perfil && $perfil->avatar)
                                            <img src="{{ Storage::url($perfil->avatar) }}" alt="avatar"
                                                 class="rounded-circle"
                                                 style="width:30px;height:30px;object-fit:cover">
                                        @else
                                            <i class="bi bi-person-circle" style="font-size: 1rem;"></i>
                                        @endif
                                    </td>

                                    {{-- Datos empresa --}}
                                    <td>{{ $user->nombre }}</td>
                                    <td>{{ $user->email }}</td>
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
                                        <button class="btn btn-info btn-sm px-2 py-1"
                                                @click="openProfessionalModal({{ $perfil->id }})">
                                            Ver
                                        </button>

                                        {{-- Editar perfil profesional --}}
                                        <a href="{{ route('admin.profesionales.editar', $perfil->id) }}"
                                           class="btn btn-warning btn-sm px-2 py-1">
                                            Editar
                                        </a>

                                        {{-- Eliminar SOLO perfil profesional --}}
                                        <form id="delete-prof-{{ $perfil->id }}"
                                              action="{{ route('admin.profesionales.eliminar', $perfil->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <delete-professional-button
                                                form-id="delete-prof-{{ $perfil->id }}"
                                                empresa="{{ $perfil->empresa }}"
                                                :tiene-user="{{ $user ? 'true' : 'false' }}"
                                                user-nombre="{{ $user?->nombre }} {{ $user?->apellidos }}"
                                                user-email="{{ $user?->email }}"
                                            ></delete-professional-button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $profesionales->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

<x-alertas_sweet />
