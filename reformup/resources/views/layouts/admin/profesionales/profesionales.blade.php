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
                                    {{-- Siempre visible --}}
                                    <th>Profesional</th>

                                    {{-- En tablet (md+) y escritorio --}}
                                    <th class="d-none d-md-table-cell">Empresa</th>
                                    <th class="d-none d-md-table-cell">Teléfono</th>

                                    {{-- Solo en escritorio (lg+) --}}
                                    <th class="d-none d-lg-table-cell">CIF</th>
                                    <th class="d-none d-lg-table-cell">Email</th>
                                    <th class="d-none d-lg-table-cell">Cuenta Usuario</th>
                                    <th class="d-none d-lg-table-cell">Rol</th>

                                    {{-- Visible (md+) --}}
                                    <th class="d-none d-md-table-cell text-center">Visible</th>

                                    {{-- Acciones siempre visibles --}}
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>

                            <tbody style="font-size: .875rem;">
                                @foreach ($profesionales as $perfil)
                                    @php
                                        $user = $perfil->user;
                                    @endphp

                                    <tr>
                                        {{-- COLUMNA PRINCIPAL RESPONSIVE (xs + md + lg) --}}
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
                                                    {{-- Nombre + email usuario --}}
                                                    <div class="fw-semibold">
                                                        {{ $user?->nombre ?? 'Sin usuario' }}
                                                        @if ($user)
                                                            <br><span class="text-muted">{{ $user->email }}</span>
                                                        @else
                                                            <br><span class="text-muted">Sin usuario</span>
                                                        @endif
                                                    </div>

                                                    {{-- INFO EXTRA SOLO EN MÓVIL (xs) --}}
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

                                        {{-- EMPRESA (md+) --}}
                                        <td class="d-none d-md-table-cell">{{ $perfil->empresa }}</td>

                                        {{-- TELÉFONO (md+) --}}
                                        <td class="d-none d-md-table-cell">{{ $perfil->telefono_empresa }}</td>

                                        {{-- CIF (lg+) --}}
                                        <td class="d-none d-lg-table-cell">{{ $perfil->cif }}</td>

                                        {{-- EMAIL EMPRESA (lg+) --}}
                                        <td class="d-none d-lg-table-cell">{{ $perfil->email_empresa }}</td>

                                        {{-- CUENTA USUARIO (lg+) --}}
                                        <td class="d-none d-lg-table-cell">
                                            @if ($user)
                                                <div>{{ $user->nombre }} {{ $user->apellidos }}</div>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            @else
                                                <span class="text-muted small">Sin usuario</span>
                                            @endif
                                        </td>

                                        {{-- ROL (lg+) --}}
                                        <td class="d-none d-lg-table-cell">
                                            @if ($user)
                                                {{ $user->getRoleNames()->implode(', ') }}
                                            @else
                                                <span class="text-muted">Sin usuario</span>
                                            @endif
                                        </td>

                                        {{-- VISIBLE + TOGGLE (md+) --}}
                                        <td class="d-none d-md-table-cell text-center">
                                            <form action="{{ route('admin.profesionales.toggle_visible', $perfil) }}"
                                                  method="POST"
                                                  class="d-inline">
                                                @csrf
                                                @method('PATCH')

                                                <div class="d-flex flex-column align-items-center">
                                                    <small class="text-muted mb-1">
                                                        Visibilidad
                                                    </small>

                                                    <div class="form-check form-switch d-flex align-items-center gap-1">
                                                        <input class="form-check-input"
                                                               type="checkbox"
                                                               onChange="this.form.submit()"
                                                               {{ $perfil->visible ? 'checked' : '' }}>

                                                        <label class="form-check-label small">
                                                            {{ $perfil->visible ? 'Visible' : 'Oculto' }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>

                                        {{-- ACCIONES (xs + md + lg) --}}
                                        <td class="text-end">
                                            {{-- En móvil: botones en columna y a ancho completo; en md+ en fila --}}
                                            <div class="d-flex flex-column flex-md-row gap-1 justify-content-end">

                                                {{-- VER --}}
                                                <button
                                                    class="btn btn-info btn-sm px-2 py-1 w-100 w-md-auto"
                                                    @click="openProfessionalModal({{ $perfil->id }})">
                                                    Ver
                                                </button>

                                                {{-- EDITAR --}}
                                                <a href="{{ route('admin.profesionales.editar', $perfil->id) }}"
                                                   class="btn btn-warning btn-sm px-2 py-1 w-100 w-md-auto">
                                                    Editar
                                                </a>

                                                {{-- ELIMINAR --}}
                                                <form id="delete-prof-{{ $perfil->id }}"
                                                      action="{{ route('admin.profesionales.eliminar', $perfil->id) }}"
                                                      method="POST"
                                                      class="d-inline w-100 w-md-auto">
                                                    @csrf
                                                    @method('DELETE')

                                                    <delete-professional-button
                                                        class="w-100 w-md-auto"
                                                        form-id="delete-prof-{{ $perfil->id }}"
                                                        empresa="{{ $perfil->empresa }}"
                                                        :tiene-user="{{ $user ? 'true' : 'false' }}"
                                                        user-nombre="{{ $user?->nombre }} {{ $user?->apellidos }}"
                                                        user-email="{{ $user?->email }}">
                                                    </delete-professional-button>
                                                </form>
                                            </div>
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
