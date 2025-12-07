{{-- resources/views/components/admin_sidebar.blade.php --}}

@php
    $user = Auth::user();
    $roles = $user ? $user->getRoleNames() : collect();

    $isAdmin = $roles->contains('admin');
    $isUsuario = $roles->contains('usuario');
    $isProfesional = $roles->contains('profesional');
@endphp

<div id="sidebar" class="position-fixed d-none d-lg-flex flex-column p-3 bg-admin-bg">
    {{-- Botón para plegar/desplegar --}}
    <button id="sidebar-toggle" type="button" class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-1"
        aria-label="Contraer/expandir menú">
        <i class="bi bi-chevron-left"></i>
    </button>

    {{-- Título Sidebar Roles --}}
    @if ($isAdmin)
        <h4 class="mt-4 fs-3">Administrador</h4>

        <div class="ms-3 mt-1" style="color: #B5C99A;">
            @if ($isUsuario)
                <div>Usuario</div>
            @endif
            @if ($isProfesional)
                <div>Profesional</div>
            @endif
        </div>
    @else
        @if ($isUsuario)
            <h4 class="m-2" style="color: #718355;">Usuario</h4>
        @endif
        @if ($isProfesional)
            <h4 class="m-2 text-secondary" style="color: #B5C99A;">Profesional</h4>
        @endif
    @endif

    {{-- Elementos --}}
    <nav class="mt-3 d-flex flex-column flex-grow-1">
        <ul class="nav flex-column admin-sidebar" style="flex-grow: 1;">
            <li class="nav-item">
                {{-- Listado Usuarios --}}
                <a class="nav-link {{ request()->routeIs('admin.usuarios*') ? 'active' : '' }}"
                    href="{{ route('admin.usuarios') }}">
                    <i class="bi bi-people-fill"></i> Usuarios
                </a>
            </li>

            <li class="nav-item">
                {{-- Listado Profesionales --}}
                <a class="nav-link {{ request()->routeIs('admin.profesionales*') ? 'active' : '' }}"
                    href="{{ route('admin.profesionales') }}">
                    <i class="bi bi-person-badge"></i> Perfiles Profesionales
                </a>
            </li>

            <li class="nav-item">
                {{-- Listado Solicitudes --}}
                <a class="nav-link {{ request()->routeIs('admin.solicitudes*') ? 'active' : '' }}"
                    href="{{ route('admin.solicitudes') }}">
                    <i class="bi bi-file-earmark-text"></i> Solicitudes
                </a>
            </li>

            <li class="nav-item">
                {{-- Listado Presupuestos --}}
                <a class="nav-link {{ request()->routeIs('admin.presupuestos*') ? 'active' : '' }}"
                    href="{{ route('admin.presupuestos') }}">
                    <i class="bi bi-receipt"></i> Presupuestos
                </a>
            </li>

            <li class="nav-item">
                {{-- Listado Trabajos --}}
                <a class="nav-link {{ request()->routeIs('admin.trabajos*') ? 'active' : '' }}"
                    href="{{ route('admin.trabajos') }}">
                    <i class="bi bi-briefcase-fill"></i> Trabajos
                </a>
            </li>

            <li class="nav-item">
                {{-- Listado Comentarios --}}
                <a class="nav-link {{ request()->routeIs('admin.comentarios*') ? 'active' : '' }}"
                    href="{{ route('admin.comentarios') }}">
                    <i class="bi bi-chat-left-text"></i> Comentarios
                </a>
            </li>
            <li class="nav-item">
                {{-- Listado Oficios --}}
                <a class="nav-link {{ request()->routeIs('admin.oficios*') ? 'active' : '' }}"
                    href="{{ route('admin.oficios') }}">
                    <i class="bi bi-tools"></i> Oficios
                </a>
            </li>

            <li>
                <hr>
            </li>
        </ul>

        {{-- Inicio, Perfil y Cerrar sesión --}}
        <ul class="nav flex-column mt-auto admin-sidebar">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                    <i class="bi bi-house-door"></i> Inicio
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.perfil') ? 'active' : '' }}"
                    href="{{ route('admin.perfil') }}">
                    <i class="bi bi-file-person-fill"></i> Perfil
                </a>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-link p-2">
                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div>

<x-usuario.sidebar_usuario_toggle_script />
