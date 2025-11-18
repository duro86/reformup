{{-- Gestión roles para mostrar --}}
@php
    $user = Auth::user();
    $roles = $user ? $user->getRoleNames() : collect();

    $isAdmin = $roles->contains('admin');
    $isUsuario = $roles->contains('usuario');
    $isProfesional = $roles->contains('profesional');
@endphp

<div id="sidebar" class="position-fixed d-flex flex-column p-3 bg-light">
    {{-- Botón para plegar/desplegar --}}
    <button id="sidebar-toggle"
        type="button"
        class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-1"
        aria-label="Contraer/expandir menú">
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
            <h4 class="m-2 fs-3" style="color: #718355;">Usuario</h4>
        @endif
        @if ($isProfesional)
            <h4 class="m-2 text-secondary fs-5" style="color: #B5C99A;">Profesional</h4>
        @endif
    @endif

    <nav class="mt-3 d-flex flex-column" style="flex-grow: 1;">
        <ul class="nav flex-column admin-sidebar" style="flex-grow: 1;">
            <li class="nav-item">
                {{-- Listado Solicitudes --}}
                <a class="nav-link" href="{{ route('usuario.solicitudes.index') }}"><i class="bi bi-file-earmark-text"></i> Solicitudes</a>
            </li>
            <li class="nav-item">
                {{-- Listado Presupuestos --}}
                <a class="nav-link" href="{{ route('usuario.presupuestos.index') }}"><i class="bi bi-receipt"></i> Presupuestos</a>
            </li>
            <li class="nav-item">
                {{-- Listado Trabajos --}}
                <a class="nav-link" href="#"><i class="bi bi-briefcase-fill"></i> Trabajos</a>
            </li>
            <li class="nav-item">
                {{-- Listado Comentarios --}}
                <a class="nav-link" href="#"><i class="bi bi-chat-left-text"></i> Comentarios</a>
            </li>

            {{-- Bloque acceso profesional --}}
            @if ($isProfesional)
                @php
                    $perfilProfesional = $user->perfil_Profesional()->first();
                @endphp

                <li class="nav-item mt-2">
                    @if ($perfilProfesional)
                        <div class="px-2 py-2 small border rounded bg-white">
                            <div class="fw-semibold mb-1">Perfil profesional activo</div>
                            <p class="mb-2 text-muted">
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
                                Tienes el rol de profesional, pero aún no has creado tu perfil de empresa.
                            </p>
                            <a href="{{ route('registrar.profesional.opciones') }}" class="btn btn-sm btn-warning w-100">
                                Crear perfil profesional
                            </a>
                        </div>
                    @endif
                </li>
            @endif

            <li><hr></li>
        </ul>

        {{-- Inicio, Perfil y Cerrar sesión --}}
        <ul class="nav flex-column mt-auto admin-sidebar">
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
                    <button type="submit" class="nav-link p-0 text-success" style="background:none; border:none;">
                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div>

<x-usuario.sidebar_usuario_toggle_script />

