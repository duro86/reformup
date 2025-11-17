{{-- resources/views/components/admin_sidebar.blade.php --}}

@php
    $user = Auth::user();
    $roles = $user ? $user->getRoleNames() : collect();

    $isAdmin       = $roles->contains('admin');
    $isUsuario     = $roles->contains('usuario');
    $isProfesional = $roles->contains('profesional');
@endphp

<div id="sidebar" class="position-fixed d-flex flex-column p-3 bg-light">
    {{-- Botón para plegar/desplegar --}}
    <button
        id="sidebar-toggle"
        type="button"
        class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-1"
        aria-label="Contraer/expandir menú"
    >
        <i class="bi bi-chevron-left"></i>
    </button>

    {{-- Título Sidebar Roles --}}
    @if ($isAdmin)
        <h4 class="m-2" style="color: #000000;">Administrador</h4>

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
    <nav class="mt-3 d-flex flex-column" style="flex-grow: 1;">
        <ul class="nav flex-column admin-sidebar" style="flex-grow: 1;">
            <li class="nav-item">
                {{-- Listado Usuarios --}}
                <a class="nav-link" href="{{ route('admin.usuarios') }}">
                    <i class="bi bi-people-fill"></i> Usuarios
                </a>
            </li>
            <li class="nav-item">
                {{-- Listado Profesionales --}}
                <a class="nav-link" href="{{ route('admin.profesionales') }}">
                    <i class="bi bi-person-badge"></i> Perfiles Profesionales
                </a>
            </li>
            <li class="nav-item">
                {{-- Listado Solicitudes --}}
                <a class="nav-link" href="#">
                    <i class="bi bi-file-earmark-text"></i> Solicitudes
                </a>
            </li>
            <li class="nav-item">
                {{-- Listado Presupuestos --}}
                <a class="nav-link" href="#">
                    <i class="bi bi-receipt"></i> Presupuestos
                </a>
            </li>
            <li class="nav-item">
                {{-- Listado Trabajos --}}
                <a class="nav-link" href="#">
                    <i class="bi bi-briefcase-fill"></i> Trabajos
                </a>
            </li>
            <li class="nav-item">
                {{-- Listado Comentarios --}}
                <a class="nav-link" href="#">
                    <i class="bi bi-chat-left-text"></i> Comentarios
                </a>
            </li>
            <li>
                <hr>
            </li> {{-- Separador visual --}}
        </ul>

        {{-- Inicio, Perfil y Cerrar sesión --}}
        <ul class="nav flex-column mt-auto admin-sidebar">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="bi bi-house-door"></i> Inicio
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.perfil') }}">
                    <i class="bi bi-file-person-fill"></i> Perfil
                </a>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-link p-0 text-success" style="background:none; border:none;">
                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div>

{{-- Script de toggle compartido con usuario/profesional --}}
<x-usuario.sidebar_usuario_toggle_script />
