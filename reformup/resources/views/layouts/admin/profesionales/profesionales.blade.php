@extends('layouts.main')

@section('title', 'Listado de profesionales - ReformUp')

@section('content')

    {{-- Navbar --}}
    <x-navbar />

    {{-- Sidebar admin --}}
    <x-admin.admin_sidebar />

    {{-- Bienvenida --}}
    <x-admin.admin_bienvenido />

    {{-- Nav móvil --}}
    <x-admin.nav_movil active="profesionales" />

    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-3">

            <div id="app">

                {{-- Modal --}}
                <professional-modal ref="professionalModal"></professional-modal>

                <div class="container p-1">
                    <h1 class="text-center d-flex flex-column flex-md-row align-items-center justify-content-center gap-3 mb-4">
                        <span>Listado de Profesionales</span>

                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            <a href="{{ route('registrar.profesional.opciones') }}" class="btn btn-sm"
                               style="background-color:#718355;color:white">
                                <i class="bi bi-plus-lg"></i> Añadir profesional
                            </a>

                            <a href="#" class="btn btn-sm" style="background-color:#B5C99A;color:black">
                                Exportar PDF
                            </a>
                        </div>
                    </h1>

                    {{-- TABLA RESPONSIVE --}}
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">

                            <thead>
                                <tr style="font-size: .875rem;">
                                    <th>Profesional</th>
                                    <th class="d-none d-md-table-cell">Empresa</th>
                                    <th class="d-none d-md-table-cell">CIF</th>
                                    <th class="d-none d-md-table-cell">Email</th>
                                    <th class="d-none d-md-table-cell">Teléfono</th>
                                    <th class="d-none d-md-table-cell">Rol</th>
                                    <th class="d-none d-md-table-cell text-center">Visible</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>

                            <tbody style="font-size: .875rem;">
                                @foreach ($profesionales as $perfil)
                                    @php $user = $perfil->user; @endphp

                                    <tr>

                                        {{-- COLUMNA PRINCIPAL RESPONSIVE --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-2">

                                                {{-- Avatar --}}
                                                @if ($perfil->avatar)
                                                    <img src="{{ Storage::url($perfil->avatar) }}"
                                                         class="rounded-circle"
                                                         style="width:32px;height:32px;object-fit:cover">
                                                @else
                                                    <i class="bi bi-person-circle fs-4"></i>
                                                @endif

                                                <div>
                                                    {{-- Nombre --}}
                                                    <div class="fw-semibold">
                                                        {{ $user?->nombre ?? 'Sin usuario' }}
                                                    </div>

                                                    {{-- INFO EXTRA SOLO MÓVIL --}}
                                                    <div class="small text-muted d-md-none mt-1">
                                                        {{ $perfil->empresa }} <br>
                                                        CIF: {{ $perfil->cif }} <br>
                                                        {{ $perfil->email_empresa }} <br>
                                                        Tel: {{ $perfil->telefono_empresa }} <br>

                                                        {{-- Rol --}}
                                                        Rol:
                                                        @if ($user)
                                                            {{ $user->getRoleNames()->implode(', ') }}
                                                        @else
                                                            Sin usuario
                                                        @endif
                                                        <br>

                                                        {{-- Visible --}}
                                                        Visible:
                                                        @if ($perfil->visible)
                                                            <span class="text-success">Sí</span>
                                                        @else
                                                            <span class="text-danger">No</span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </td>

                                        {{-- EMPRESA --}}
                                        <td class="d-none d-md-table-cell">{{ $perfil->empresa }}</td>

                                        {{-- CIF --}}
                                        <td class="d-none d-md-table-cell">{{ $perfil->cif }}</td>

                                        {{-- EMAIL --}}
                                        <td class="d-none d-md-table-cell">{{ $perfil->email_empresa }}</td>

                                        {{-- TELÉFONO --}}
                                        <td class="d-none d-md-table-cell">{{ $perfil->telefono_empresa }}</td>

                                        {{-- ROL --}}
                                        <td class="d-none d-md-table-cell">
                                            @if ($user)
                                                {{ $user->getRoleNames()->implode(', ') }}
                                            @else
                                                <span class="text-muted">Sin usuario</span>
                                            @endif
                                        </td>

                                        {{-- VISIBLE DESKTOP --}}
                                        <td class="d-none d-md-table-cell text-center">
                                            @if ($perfil->visible)
                                                <span class="badge bg-success">Sí</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </td>

                                        {{-- ACCIONES --}}
                                        <td class="text-end">

                                            {{-- TOGGLE VISIBLE --}}
                                            {{--<form action="{{ route('admin.profesionales.toggleVisible', $perfil->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf

                                                @if ($perfil->visible)
                                                    <button class="btn btn-outline-danger btn-sm px-2 py-1 mb-1 mb-md-0">
                                                        Ocultar
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline-success btn-sm px-2 py-1 mb-1 mb-md-0">
                                                        Hacer visible
                                                    </button>
                                                @endif
                                            </form>--}}

                                            {{-- VER --}}
                                            <button class="btn btn-info btn-sm px-2 py-1 mb-1 mb-md-0"
                                                    @click="openProfessionalModal({{ $perfil->id }})">
                                                Ver
                                            </button>

                                            {{-- EDITAR --}}
                                            <a href="{{ route('admin.profesionales.editar', $perfil->id) }}"
                                               class="btn btn-warning btn-sm px-2 py-1 mb-1 mb-md-0">
                                                Editar
                                            </a>

                                            {{-- ELIMINAR --}}
                                            <form id="delete-prof-{{ $perfil->id }}"
                                                  action="{{ route('admin.profesionales.eliminar', $perfil->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')

                                                <delete-professional-button
                                                    form-id="delete-prof-{{ $perfil->id }}"
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

<x-alertas_sweet />
