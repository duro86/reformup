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

                        {{-- Enlaces utiles --}}
                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            <a href="{{ route('admin.form.registrar.cliente') }}" class="btn btn-sm"
                                style="background-color: #718355; color: white;">
                                <i class="bi bi-plus-lg"></i> Añadir usuario
                            </a>

                            <a href="{{ route('admin.usuarios.exportar.pdf') }}" class="btn btn-sm bg-secondary"
                                target="_blank">
                                Exportar PDF todos usuarios
                            </a>
                            <a href="{{ route(
                                'admin.usuarios.exportarPaginaPdf',
                                array_merge(request()->only('q', 'fecha_desde', 'fecha_hasta'), ['page' => $usuarios->currentPage()]),
                            ) }}"
                                class="btn btn-sm bg-light" target="_blank">
                                Exportar PDF esta página
                            </a>

                        </div>
                    </h1>

                    {{-- Buscador combinado: texto + fechas --}}
                    <form method="GET" action="{{ route('admin.usuarios') }}" class="row g-2 mb-3">
                        {{-- Búsqueda por texto --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <input type="text" name="q" value="{{ request('q') }}"
                                class="form-control form-control-sm"
                                placeholder="Buscar por nombre, apellidos, email o teléfono...">
                        </div>

                        {{-- Rango de fechas (alta de usuario) --}}
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
                                <a href="{{ route('admin.usuarios') }}" class="btn btn-sm btn-outline-secondary">
                                    Limpiar
                                </a>
                            @endif
                        </div>
                    </form>


                    {{-- ====================== --}}
                    {{-- TABLA (solo en lg+)   --}}
                    {{-- ====================== --}}
                    <div class="table-responsive d-none d-lg-block">
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
                                        {{-- Columna USUARIO --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
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

                                                    @if ($usuario->perfil_Profesional)
                                                        <div class="small text-muted">
                                                            Empresa: {{ $usuario->perfil_Profesional->empresa }} <br>
                                                            Email pro: {{ $usuario->perfil_Profesional->email_empresa }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td class="d-none d-md-table-cell">
                                            {{ $usuario->email }}
                                        </td>

                                        <td class="d-none d-md-table-cell">
                                            {{ $usuario->telefono ?: '—' }}
                                        </td>

                                        <td class="d-none d-md-table-cell">
                                            {{ $usuario->getRoleNames()->implode(', ') ?: '—' }}
                                        </td>

                                        <td class="text-center">
                                            <div class="d-flex flex-row gap-1 justify-content-center acciones-usuario">
                                                <button class="btn btn-info btn-sm px-2 py-1"
                                                    @click="openUserModal({{ $usuario->id }})">
                                                    Ver
                                                </button>

                                                <a href="{{ route('admin.usuarios.editar', [$usuario->id, 'page' => $usuarios->currentPage()]) }}"
                                                    class="btn btn-warning btn-sm px-2 py-1">
                                                    Editar
                                                </a>

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
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- ====================== --}}
                    {{-- VISTA CARDS (xs–lg)   --}}
                    {{-- ====================== --}}
                    <div class="d-block d-lg-none ">
                        @foreach ($usuarios as $usuario)
                            <div class="card mb-3 shadow-sm bg-light">
                                <div class="card-body ">

                                    {{-- Cabecera --}}
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        @if ($usuario->avatar)
                                            <img src="{{ Storage::url($usuario->avatar) }}" alt="avatar"
                                                class="rounded-circle" style="width:40px;height:40px;object-fit:cover">
                                        @else
                                            <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                                        @endif

                                        <div>
                                            <div class="fw-semibold">
                                                {{ $usuario->nombre }} {{ $usuario->apellidos }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $usuario->email }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Datos adicionales --}}
                                    <div class="small text-muted mb-2">
                                        @if ($usuario->telefono)
                                            <div><strong>Tel:</strong> {{ $usuario->telefono }}</div>
                                        @endif

                                        @if ($usuario->perfil_Profesional)
                                            <div><strong>Empresa:</strong> {{ $usuario->perfil_Profesional->empresa }}
                                            </div>
                                            <div><strong>Email pro:</strong>
                                                {{ $usuario->perfil_Profesional->email_empresa }}</div>
                                        @endif

                                        <div class="mt-1">
                                            <strong>Rol:</strong>
                                            {{ $usuario->getRoleNames()->implode(', ') ?: '—' }}
                                        </div>
                                    </div>

                                    {{-- Acciones en columna, ancho completo --}}
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-info btn-sm" @click="openUserModal({{ $usuario->id }})">
                                            Ver
                                        </button>

                                        <a href="{{ route('admin.usuarios.editar', [$usuario->id, 'page' => $usuarios->currentPage()]) }}"
                                            class="btn btn-warning btn-sm">
                                            Editar
                                        </a>

                                        <form id="delete-user-mobile-{{ $usuario->id }}"
                                            action="{{ route('admin.usuarios.eliminar', $usuario->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <delete-user-button class="btn btn-danger btn-sm w-100"
                                                form-id="delete-user-mobile-{{ $usuario->id }}"
                                                user-nombre="{{ $usuario->nombre }} {{ $usuario->apellidos }}"
                                                user-email="{{ $usuario->email }}"
                                                :tiene-perfil="{{ $usuario->perfil_Profesional ? 'true' : 'false' }}">
                                            </delete-user-button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Paginación --}}
                    {{ $usuarios->links('pagination::bootstrap-5') }}
                </div>

                {{-- Modal Vue --}}
                <user-modal ref="userModal"></user-modal>
            </div>

        </div>
    </div>

@endsection

<x-alertas_sweet />
