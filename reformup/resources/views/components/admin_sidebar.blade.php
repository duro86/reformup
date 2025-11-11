@php
    $user = Auth::user();
    $rol = $user ? $user->getRoleNames()->first() : 'Sin rol';
@endphp
<div class="position-fixed d-flex flex-column p-3 bg-light" 
     style="height: 100vh; width: 200px; max-width: 100%;"
     id="sidebar">
    <h4 class="m-2">{{ $rol }}</h4>

    {{-- Ielementos --}}
    <nav class="mt-3 d-flex flex-column" style="flex-grow: 1;">
        <ul class="nav flex-column admin-sidebar" style="flex-grow: 1;">
            <li class="nav-item"><a class="nav-link" href="{{route('admin.usuarios')}}"><i class="bi bi-people-fill"></i> Usuarios</a></li>
            <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-person-badge"></i> Perfiles Profesionales</a></li>
            <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-file-earmark-text"></i> Solicitudes</a></li>
            <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-receipt"></i> Presupuestos</a></li>
            <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-briefcase-fill"></i> Trabajos</a></li>
            <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-chat-left-text"></i> Comentarios</a></li>
            <li><hr></li> {{-- Separador visual --}}
        </ul>

        {{-- Inicio , Perfil y Cerrar sesion --}}
        <ul class="nav flex-column mt-auto admin-sidebar">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}"><i class="bi bi-house-door"></i> Inicio</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="bi bi-file-person-fill"></i> Perfil</a>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-link p-0" style="color: green;">
                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div>

