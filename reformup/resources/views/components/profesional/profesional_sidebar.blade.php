{{-- resources/views/components/profesional/profesional_sidebar.blade.php --}}
@php
    //AUTENTICAMOS
    $user = Auth::user();
    $roles = $user ? $user->getRoleNames() : collect();

    // Verifica si el usuario tiene el roles
    $isAdmin = $roles->contains('admin');
    $isUsuario = $roles->contains('usuario');
    $isProfesional = $roles->contains('profesional');

    // Define una variable booleana que será true si el usuario tiene rol profesional,
    // posee perfil profesional existente y el campo 'visible' de ese perfil es true (1)
    $perfilProfesional = $user?->perfil_Profesional()->first();
    $perfilVisible = $isProfesional && $user->perfilProfesional && $user->perfilProfesional->visible;
@endphp

<div id="sidebar" class="position-fixed d-none d-lg-flex flex-column p-3 bg-pro-primary">
    {{-- Botón para plegar/desplegar --}}
    <button id="sidebar-toggle" type="button" class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-1"
        aria-label="Contraer/expandir menú">
        <i class="bi bi-chevron-left"></i>
    </button>

    {{-- BLOQUE AVATAR + NOMBRE EMPRESA --}}
    @if ($perfilProfesional)
        <div class="text-center mb-1 p-3 rounded">
            <div class="mb-1 d-flex justify-content-center">
                <i class="bi bi-building rounded-circle fs-2"></i>
            </div>
            <div class="fw-semibold small">
                {{ $perfilProfesional->empresa ?? 'Tu empresa' }}
            </div>
            @if ($perfilProfesional->ciudad || $perfilProfesional->provincia)
                <div class="text-muted small">
                    {{ $perfilProfesional->ciudad }}
                    @if ($perfilProfesional->ciudad && $perfilProfesional->provincia)
                        ·
                    @endif
                    {{ $perfilProfesional->provincia }}
                </div>
            @endif
        </div>
    @endif

    {{-- Texto roles adicionales --}}
    <div class="ms-1 mb-2 small text-muted">
        @if ($isProfesional)
            <div>Rol Profesional</div>
        @endif
        @if ($isAdmin)
            <div>Rol administrador</div>
        @endif
    </div>

    {{-- Si también es usuario: botón volver a panel usuario --}}
    @if ($isUsuario)
        <div class="mt-2 px-1">
            <a href="{{ route('usuario.dashboard') }}"
                class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1">
                <i class="bi bi-arrow-left"></i>
                <span>Panel usuario</span>
            </a>
        </div>
    @endif

    {{-- NAV PRINCIPAL --}}
    <nav class="mt-3 d-flex flex-column" id="pro-sidebar">
        <ul class="nav flex-column admin-sidebar">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profesional.solicitudes.*') ? 'active fw-semibold bg-pro-secondary text-primary' : '' }}"
                    href="{{ route('profesional.solicitudes.index') }}">
                    <i class="bi bi-file-earmark-text"></i> Solicitudes
                </a>

            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profesional.presupuestos.*') ? 'active fw-semibold bg-pro-secondary  text-primary' : '' }}"
                    href="{{ route('profesional.presupuestos.index') }}">
                    <i class="bi bi-receipt"></i> Presupuestos
                </a>

            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profesional.trabajos.*') ? 'active fw-semibold bg-pro-secondary  text-primary' : '' }}"
                    href="{{ route('profesional.trabajos.index') }}">
                    <i class="bi bi-briefcase-fill"></i> Trabajos
                </a>

            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profesional.comentarios.*') ? 'active fw-semibold bg-pro-secondary  text-primary' : '' }}"
                    href="{{ route('profesional.comentarios.index') }}">
                    <i class="bi bi-chat-left-text"></i> Comentarios
                </a>

            </li>

            <li>
                <hr>
            </li>

            {{-- MOSTRAR PERFIL PROFESIONAL ACTIVO O NO --}}
            @if ($perfilProfesional->visible)
                <i class="bi bi-check-lg">Perfil profesional activo</i> 
            @else
                <i class="bi bi-ban">Perfil profesional Inactivo</i> 
            @endif

        </ul>

        {{-- Zona inferior PERFIL Y CERRAR SESION --}}
        <ul class="nav flex-column mt-4 admin-sidebar">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="bi bi-house-door"></i> Inicio
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('profesional.perfil') }}">
                    <i class="bi bi-file-person-fill"></i> Perfil profesional
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

<div class="sidebar-toggle-profesional">
    <x-usuario.sidebar_usuario_toggle_script />
</div>
