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
        {{-- CONTENIDO VUE + TABLA --}}
        <div class="container py-4" id="app">

            <div class="container p-3">
                <h1 class="text-center d-flex flex-column flex-md-row align-items-center justify-content-center gap-3 mb-4">
                    <span>Listado de Profesionales</span>

                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="{{ route('registrar.profesional.opciones') }}" class="btn btn-sm"
                            style="background-color:#718355;color:white">
                            <i class="bi bi-plus-lg"></i> Añadir profesional
                        </a>

                        <a href="{{ route('admin.profesionales.exportar_todos_pdf') }}" class="btn btn-sm bg-secondary"
                            target="_blank">
                            Exportar PDF todos profesionales
                        </a>
                        <a href="{{ route(
                            'admin.profesionales.exportar_pdf_pagina',
                            array_merge(request()->only('q', 'fecha_desde', 'fecha_hasta'), ['page' => $profesionales->currentPage()]),
                        ) }}"
                            class="btn btn-sm bg-light" target="_blank">
                            Exportar PDF esta página
                        </a>

                    </div>
                </h1>

                {{-- Mensajes flash --}}
                <x-alertas.alertasFlash />

                {{-- Buscador combinado: texto + fechas --}}
                <form method="GET" action="{{ route('admin.profesionales') }}" class="row g-2 mb-3">
                    {{-- Búsqueda por texto --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="form-control form-control-sm"
                            placeholder="Buscar por empresa, CIF, email, teléfono o usuario...">
                    </div>

                    {{-- Rango de fechas (creación del perfil profesional) --}}
                    @include('partials.filtros.rango_fechas')

                    {{-- Botón Buscar --}}
                    <div class="col-6 col-md-3 col-lg-2 d-grid">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>

                    {{-- Botón Limpiar --}}
                    <div class="col-6 col-md-3 col-lg-2 d-grid">
                        @if (request('q') || request('fecha_desde') || request('fecha_hasta'))
                            <a href="{{ route('admin.profesionales') }}" class="btn btn-sm btn-outline-secondary">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>


                {{-- ========================= --}}
                {{-- TABLA (solo en lg+)      --}}
                {{-- ========================= --}}
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr class="fs-5">
                                {{-- Siempre visible --}}
                                <th class="text-center text-md-start">ID</th>
                                <th class="text-center text-md-start">Profesional</th>

                                {{-- En tablet (md+) y escritorio --}}
                                <th class="d-none d-md-table-cell">Empresa</th>
                                <th class="d-none d-md-table-cell">Teléfono</th>

                                {{-- Solo en escritorio (lg+) --}}
                                <th class="d-none d-lg-table-cell text-center">CIF</th>
                                <th class="d-none d-lg-table-cell">Email</th>
                                <th class="d-none d-lg-table-cell">Cuenta Usuario</th>
                                <th class="d-none d-lg-table-cell text-center">Rol</th>

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
                                    {{-- COLUMNA PRINCIPAL --}}
                                        <td class="d-none d-md-table-cell">{{ $perfil->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            {{-- Avatar --}}
                                            @if ($perfil->avatar)
                                                <img src="{{ Storage::url($perfil->avatar) }}" class="rounded-circle"
                                                    style="width:32px;height:32px;object-fit:cover">
                                            @else
                                                <i class="bi bi-person-circle fs-4"></i>
                                            @endif

                                            <div>
                                                <div class="fw-semibold">
                                                    {{ $user?->nombre ?? 'Sin usuario' }}
                                                    @if ($user)
                                                        <br><span class="text-muted">{{ $user->email }}</span>
                                                    @else
                                                        <br><span class="text-muted">Sin usuario</span>
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
                                    <td class="d-none d-lg-table-cell text-center">
                                        @if ($user)
                                            {!! $user->getRoleNames()->implode('<br>') !!}
                                        @else
                                            Sin usuario
                                        @endif
                                    </td>

                                    {{-- VISIBLE + TOGGLE (md+) --}}
                                    <td class="d-none d-md-table-cell text-center">
                                        <form action="{{ route('admin.profesionales.toggle_visible', $perfil) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <div class="d-flex flex-column align-items-center">
                                                <div class="form-check form-switch d-flex align-items-center gap-1">
                                                    <input class="form-check-input" type="checkbox"
                                                        onChange="this.form.submit()"
                                                        {{ $perfil->visible ? 'checked' : '' }}>

                                                    <label class="form-check-label small">
                                                        {{ $perfil->visible ? 'Visible' : 'Oculto' }}
                                                    </label>
                                                </div>
                                            </div>
                                        </form>
                                    </td>

                                    {{-- ACCIONES --}}
                                    <td class="text-end">
                                        <div class="d-flex flex-row gap-1 justify-content-end">
                                            {{-- VER --}}
                                            <button class="btn btn-info btn-sm px-2 py-1"
                                                @click="openProfessionalModal({{ $perfil->id }})">
                                                Ver
                                            </button>

                                            {{-- EDITAR (con page) --}}
                                            <a href="{{ route('admin.profesionales.editar', [$perfil->id, 'page' => $profesionales->currentPage()]) }}"
                                                class="btn btn-warning btn-sm px-2 py-1">
                                                Editar
                                            </a>

                                            {{-- ELIMINAR --}}
                                            <form id="delete-prof-{{ $perfil->id }}"
                                                action="{{ route('admin.profesionales.eliminar', $perfil->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')

                                                <delete-professional-button class="btn btn-danger"
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

                {{-- ========================= --}}
                {{-- VISTA CARDS (xs–lg)      --}}
                {{-- ========================= --}}
                <div class="d-block d-lg-none">
                    @foreach ($profesionales as $perfil)
                        @php
                            $user = $perfil->user;
                        @endphp

                        <div class="card mb-3 shadow-sm bg-light">
                            <div class="card-body ">

                                {{-- Cabecera: avatar + nombre --}}
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    @if ($perfil->avatar)
                                        <img src="{{ Storage::url($perfil->avatar) }}" class="rounded-circle"
                                            style="width:40px;height:40px;object-fit:cover">
                                    @else
                                        <i class="bi bi-person-circle fs-3"></i>
                                    @endif

                                    <div>
                                        <div class="fw-semibold">
                                            {{ $user?->nombre ?? 'Sin usuario' }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ $user?->email ?? 'Sin email de usuario' }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Datos empresa --}}
                                <div class="mb-2 small text-muted">
                                    <div><strong>Empresa:</strong> {{ $perfil->empresa }}</div>
                                    <div><strong>CIF:</strong> {{ $perfil->cif }}</div>
                                    <div><strong>Email empresa:</strong> {{ $perfil->email_empresa }}</div>
                                    <div><strong>Tel:</strong> {{ $perfil->telefono_empresa }}</div>

                                    @if ($user)
                                        <div class="mt-1">
                                            <strong>Rol:</strong>
                                            {{ $user->getRoleNames()->implode(', ') ?: '—' }}
                                        </div>
                                    @else
                                        <div class="mt-1">
                                            <strong>Rol:</strong> Sin usuario
                                        </div>
                                    @endif

                                    <div class="mt-1">
                                        <strong>Visible:</strong>
                                        @if ($perfil->visible)
                                            <span class="text-success">Sí</span>
                                        @else
                                            <span class="text-danger">No</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Toggle visible --}}
                                <div class="mb-2">
                                    <form action="{{ route('admin.profesionales.toggle_visible', $perfil) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <div class="form-check form-switch d-flex align-items-center gap-2">
                                            <input class="form-check-input" type="checkbox" onChange="this.form.submit()"
                                                {{ $perfil->visible ? 'checked' : '' }}>
                                            <label class="form-check-label small">
                                                {{ $perfil->visible ? 'Visible' : 'Oculto' }}
                                            </label>
                                        </div>
                                    </form>
                                </div>

                                {{-- Acciones en columna, ancho completo --}}
                                <div class="d-grid gap-2">
                                    <button class="btn btn-info btn-sm"
                                        @click="openProfessionalModal({{ $perfil->id }})">
                                        Ver
                                    </button>

                                    {{-- EDITAR (con page) --}}
                                    <a href="{{ route('admin.profesionales.editar', [$perfil->id, 'page' => $profesionales->currentPage()]) }}"
                                        class="btn btn-warning btn-sm">
                                        Editar
                                    </a>

                                    <form id="delete-prof-mobile-{{ $perfil->id }}"
                                        action="{{ route('admin.profesionales.eliminar', $perfil->id) }}" method="POST"
                                        class="d-grid">
                                        @csrf
                                        @method('DELETE')

                                        <delete-professional-button class="btn btn-danger btn-sm w-100"
                                            form-id="delete-prof-mobile-{{ $perfil->id }}"
                                            empresa="{{ $perfil->empresa }}" :tiene-user="{{ $user ? 'true' : 'false' }}"
                                            user-nombre="{{ $user?->nombre }} {{ $user?->apellidos }}"
                                            user-email="{{ $user?->email }}">
                                        </delete-professional-button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                {{ $profesionales->links('pagination::bootstrap-5') }}
                {{-- Modal Vue profesional --}}
                <professional-modal ref="professionalModal"></professional-modal>
            </div>
        </div>
    </div>
@endsection

<x-alertas_sweet />
