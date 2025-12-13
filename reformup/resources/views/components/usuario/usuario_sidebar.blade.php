{{-- Gestión roles para mostrar --}}
@php
    $user = Auth::user();
    $roles = $user ? $user->getRoleNames() : collect();

    $isAdmin = $roles->contains('admin');
    $isUsuario = $roles->contains('usuario');
    $isProfesional = $roles->contains('profesional');
    // Vemos si esta visible
    $perfilVisible = $isProfesional && $user->perfilProfesional && $user->perfilProfesional->visible;
@endphp

<div id="sidebar" class="position-fixed d-none d-lg-flex flex-column p-3 bg-user-secondary">

    {{-- Botón para plegar/desplegar --}}
    <button id="sidebar-toggle" type="button" class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-1"
        aria-label="Contraer/expandir menú">
        <i class="bi bi-chevron-left"></i>
    </button>

    {{-- Título Sidebar Roles --}}
    <h4 class="fs-4 text-primary fst-italic">PERFILES</h4>
    @if ($isAdmin)
        <h4 class="m-1 color-black">Administrador</h4>

        <div class="ms-3">
            @if ($isUsuario)
                <h3 class="fs-3">Usuario</h3>
            @endif
            @if ($isProfesional)
                <div>Profesional</div>
            @endif
        </div>
    @else
        @if ($isUsuario)
            <h4 class="m-1 fs-5 color-black">Usuario</h4>
        @endif
        @if ($isProfesional)
            <h4 class="m-1 text-secondary fs-6 text-muted">Profesional</h4>
        @endif
    @endif
    <hr>
    <nav class="d-flex flex-column" id="user-sidebar">
        <ul class="nav flex-column ">
            <li class="nav-item">
                {{-- Listado Solicitudes --}}
                <a class="nav-link {{ request()->routeIs('usuario.solicitudes.*') ? 'active fw-semibold bg-user-primary text-primary' : '' }}"
                    href="{{ route('usuario.solicitudes.index') }}">
                    <i class="bi bi-file-earmark-text"></i> Solicitudes
                </a>

            </li>
            <li class="nav-item">
                {{-- Listado Presupuestos --}}
                <a class="nav-link {{ request()->routeIs('usuario.presupuestos.*') ? 'active fw-semibold bg-user-primary text-primary' : '' }}"
                    href="{{ route('usuario.presupuestos.index') }}">
                    <i class="bi bi-receipt"></i> Presupuestos
                </a>

            </li>
            <li class="nav-item">
                {{-- Listado Trabajos --}}
                <a class="nav-link {{ request()->routeIs('usuario.trabajos.*') ? 'active fw-semibold bg-user-primary text-primary' : '' }}"
                    href="{{ route('usuario.trabajos.index') }}">
                    <i class="bi bi-briefcase-fill"></i> Trabajos
                </a>

            </li>
            <li class="nav-item">
                {{-- Listado Comentarios --}}
                <a class="nav-link {{ request()->routeIs('usuario.comentarios.*') ? 'active fw-semibold bg-user-primary text-primary' : '' }}"
                    href="{{ route('usuario.comentarios.index') }}">
                    <i class="bi bi-chat-left-text"></i> Comentarios
                </a>

            </li>

            {{-- Bloque acceso profesional --}}
            @if ($isProfesional)
                @php
                    $perfilProfesional = $user->perfil_Profesional()->first();
                @endphp

                <li class="nav-item mt-1">
                    @if ($perfilProfesional)
                        <div class="px-2 py-2 small border rounded bg-white fs-7">
                            @if ($perfilProfesional->visible)
                                <i class="bi bi-check-lg">Perfil profesional activo</i> 
                            @else
                                <i class="bi bi-hourglass-split text-warning" aria-label="Perfil profesional inactivo"
                                    title="Perfil profesional inactivo"></i> Perfil profesional Inactivo
                            @endif
                            <p class="mb-1 text-muted">
                                Accede a tu panel como profesional para gestionar solicitudes y trabajos.
                            </p>
                            <a href="{{ route('profesional.dashboard') }}" class="btn btn-primary btn-sm w-100">
                                Ir a panel profesional
                            </a>
                        </div>
                    @else
                        <div class="px-2 py-2 small border rounded bg-white">
                            <div class="fw-semibold mb-1 text-warning">
                                Completa tu perfil profesional
                            </div>
                            <p class="mb-2 text-muted">
                                Debes completar tu perfil de profesional para registrar la empresa.
                            </p>
                            <a href="{{ route('registro.pro.empresa') }}" class="btn btn-sm btn-warning w-100">
                                Crear perfil profesional
                            </a>
                        </div>
                    @endif
                </li>
            @endif

            <li>
                <hr>
            </li>
        </ul>

        {{-- Inicio, Perfil y Cerrar sesión --}}
        <ul class="nav flex-column mt-2 admin-sidebar">
            {{-- Si es usuario y no tiene perfil profesional --}}
            @if ($isUsuario && !$isProfesional)
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('registro.pro.empresa') }}">
                        <i class="bi bi-building"></i> Registrar mi empresa
                    </a>
                </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}"><i class="bi bi-house-door"></i> Inicio</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('usuario.perfil') }}">
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
