{{-- resources/views/components/profesional/profesional_sidebar.blade.php --}}

@php
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

<div id="sidebar" class="position-fixed d-none d-lg-flex flex-column p-3 bg-light">
    {{-- Botón para plegar/desplegar --}}
    <button id="sidebar-toggle" type="button" class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-1"
        aria-label="Contraer/expandir menú">
        <i class="bi bi-chevron-left"></i>
    </button>

    {{-- BLOQUE AVATAR + NOMBRE EMPRESA --}}
    @if ($perfilProfesional)
        <div class="text-center mb-3 p-3 rounded" style="background-color: #E9F5DB;">
            <div class="mb-2 d-flex justify-content-center">
                @if ($perfilProfesional->avatar)
                    <img src="{{ asset('storage/' . $perfilProfesional->avatar) }}" alt="Logo empresa"
                        class="rounded-circle" style="width:60px;height:60px;object-fit:cover;">
                @else
                    <i class="bi bi-building rounded-circle" style="font-size: 2.5rem;"></i>
                @endif
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
    @else
        {{-- Añadir Perfil Profesional --}}
        <div class="text-center mb-3 p-3 rounded" style="background-color:#FFF3CD;">
            <div class="mb-2">
                <i class="bi bi-exclamation-triangle" style="font-size:1.8rem;"></i>
            </div>
            <div class="small fw-semibold text-warning">
                Aún no has creado tu perfil profesional
            </div>
            <a href="{{ route('registrar.profesional.opciones') }}" class="btn btn-sm btn-warning mt-2 w-100">
                Crear perfil
            </a>
        </div>
    @endif

    {{-- Texto roles adicionales --}}
    <div class="ms-1 mb-2 small text-muted">
        @if ($isUsuario)
            <div>Rol usuario</div>
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
    <nav class="mt-3 d-flex flex-column" style="flex-grow: 1;">
        <ul class="nav flex-column admin-sidebar" style="flex-grow: 1;">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('profesional.solicitudes.index') }}">
                    <i class="bi bi-file-earmark-text"></i> Solicitudes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('profesional.presupuestos.index') }}">
                    <i class="bi bi-receipt"></i> Presupuestos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('profesional.trabajos.index') }}">
                    <i class="bi bi-briefcase-fill"></i> Trabajos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-chat-left-text"></i> Comentarios
                </a>
            </li>

            <li>
                <hr>
            </li>
            @if ($perfilProfesional->visible)
                <i class="bi bi-hourglass-split text-success" aria-label="Perfil profesional activo"
                    title="Perfil profesional activo"></i> Perfil profesional activo
            @else
                <i class="bi bi-hourglass-split text-warning" aria-label="Perfil profesional inactivo"
                    title="Perfil profesional inactivo"></i> Perfil profesional Inactivo
            @endif

        </ul>

        {{-- Zona inferior --}}
        <ul class="nav flex-column mt-auto admin-sidebar">
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
                    <button type="submit" class="nav-link p-0 text-success" style="background:none; border:none;">
                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div>

<x-usuario.sidebar_usuario_toggle_script />
