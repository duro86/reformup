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
                    <h1
                        class="text-center d-flex flex-column flex-md-row align-items-center justify-content-center gap-3 mb-4">
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
                                    <th class="d-none d-md-table-cell">Email empresa</th>
                                    <th class="d-none d-md-table-cell">Teléfono</th>
                                    <th class="d-none d-md-table-cell">Rol</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>

                            <tbody style="font-size: .875rem;">
                                @foreach ($profesionales as $perfil)
                                    @php $user = $perfil->user; @endphp

                                    <tr>
                                        {{-- COLUMNA PRINCIPAL – MOBILE READY --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                {{-- Avatar --}}
                                                @if ($perfil?->avatar)
                                                    <img src="{{ Storage::url($perfil->avatar) }}" class="rounded-circle"
                                                        style="width:32px;height:32px;object-fit:cover">
                                                @else
                                                    <i class="bi bi-person-circle fs-4"></i>
                                                @endif

                                                <div>
                                                    {{-- Nombre usuario --}}
                                                    <div class="fw-semibold">
                                                        {{ $user?->nombre ?? 'Sin usuario' }}
                                                    </div>

                                                    {{-- INFO EXTRA SOLO EN MÓVIL --}}
                                                    <div class="small text-muted d-md-none mt-1">

                                                        {{-- Empresa --}}
                                                        {{ $perfil->empresa }}<br>

                                                        {{-- CIF --}}
                                                        CIF: {{ $perfil->cif }}<br>

                                                        {{-- Email empresa --}}
                                                        {{ $perfil->email_empresa }}<br>

                                                        {{-- Teléfono --}}
                                                        Tel: {{ $perfil->telefono_empresa }}<br>

                                                        {{-- Rol --}}
                                                        Rol:
                                                        @if ($user)
                                                            {{ $user->getRoleNames()->implode(', ') }}
                                                        @else
                                                            Sin usuario
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- SOLO DESKTOP --}}
                                        <td class="d-none d-md-table-cell">{{ $perfil->empresa }}</td>
                                        <td class="d-none d-md-table-cell">{{ $perfil->cif }}</td>
                                        <td class="d-none d-md-table-cell">{{ $perfil->email_empresa }}</td>
                                        <td class="d-none d-md-table-cell">{{ $perfil->telefono_empresa }}</td>

                                        <td class="d-none d-md-table-cell">
                                            @if ($user)
                                                {{ $user->getRoleNames()->implode(', ') }}
                                            @else
                                                <span class="text-muted">Sin usuario</span>
                                            @endif
                                        </td>

                                        {{-- ACCIONES --}}
                                        <td class="text-end">

                                            {{-- Ver --}}
                                            <button class="btn btn-info btn-sm px-2 py-1 mb-1 mb-md-0"
                                                @click="openProfessionalModal({{ $perfil->id }})">
                                                Ver
                                            </button>

                                            {{-- Editar --}}
                                            <a href="{{ route('admin.profesionales.editar', $perfil->id) }}"
                                                class="btn btn-warning btn-sm px-2 py-1 mb-1 mb-md-0">
                                                Editar
                                            </a>

                                            {{-- Eliminar --}}
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
<x-alertas_sweet />
