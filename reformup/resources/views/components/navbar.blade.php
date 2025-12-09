@props([
    // resalta el enlace activo => 'buscar'|'pasos'|'valoraciones'|''
    'active' => '',
    // URL del logo (por si cambiamos desde layout)
    'logo' => asset('img/logoPNGReformupNuevo.svg'),
])

{{-- Roles --}}
{{-- Definimos según el rol, donde apunta cada pertil y panel --}}
@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    $roles = $user ? $user->getRoleNames() : collect();
    $modo = session('modo_panel'); // 'usuario' | 'profesional' | null

    if ($roles->contains('admin')) {
        // ADMIN
        $panelRoute = route('admin.dashboard');
        $perfilRoute = route('admin.perfil');
    } elseif ($modo === 'profesional' && $roles->contains('profesional')) {
        // PROFESIONAL EN MODO PROFESIONAL
        $panelRoute = route('profesional.dashboard');
        $perfilRoute = route('profesional.perfil'); // PERFIL PROFESIONAL
    } elseif ($modo === 'usuario' && $roles->contains('usuario')) {
        // USUARIO EN MODO USUARIO
        $panelRoute = route('usuario.dashboard');
        $perfilRoute = route('usuario.perfil'); // PERFIL USUARIO
    } elseif ($roles->contains('profesional')) {
        // Por defecto entra como profesional
        $panelRoute = route('profesional.dashboard');
        $perfilRoute = route('profesional.perfil'); // PERFIL PROFESIONAL
    } elseif ($roles->contains('usuario')) {
        $panelRoute = route('usuario.dashboard');
        $perfilRoute = route('usuario.perfil');
    } else {
        $panelRoute = route('home');
        $perfilRoute = route('home');
    }
@endphp


<nav class="navbar navbar-expand-lg navbar-light sticky-top border-bottom small shadow-sm">
    <div class="container">
        {{-- Marca (logo + texto accesible) --}}
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}" aria-label="Inicio ReformUp">
            <img src="{{ $logo }}" alt="ReformUp" height="60" class="d-inline-block align-text-top">
        </a>

        {{-- Botón hamburguesa --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
            aria-controls="mainNav" aria-expanded="false" aria-label="Alternar navegación">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Menú de navegación --}}
        <div class="collapse navbar-collapse" id="mainNav">

            {{-- IZQUIERDA: enlaces principales --}}
            <ul class="navbar-nav justify-content-end flex-grow-1 me-5">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('public.profesionales*') ? 'active fw-semibold text-primary' : '' }}"
                        href="{{ route('public.profesionales.index') }}">
                        Buscar profesionales
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('public.paso_a_paso') ? 'active fw-semibold text-primary' : '' }}"
                        href="{{ route('public.paso_a_paso') }}">
                        Paso a paso
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('public.contacto') ? 'active fw-semibold text-primary' : '' }}"
                        href="{{ route('public.contacto') }}">
                        Sobre Nosotros
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active fw-semibold text-primary' : '' }}"
                        href="{{ route('home') }}">
                        Inicio
                    </a>
                </li>
            </ul>
            <div class="vr mx-2 d-none d-lg-block"></div>
            <hr class="d-block d-lg-none w-50">

            {{-- DERECHA: según esté logueado o no --}}
            <ul class="navbar-nav ms-auto align-items-lg-center">

                @guest
                    {{-- Invitados: Entrar + Registro --}}
                    <li class="nav-item mb-sm-3 mx-1 mb-lg-0">
                        <a class="nav-link {{ request()->routeIs('plogin') ? 'active fw-semibold text-primary' : '' }}"
                            href="{{ route('login') }}">
                            Entrar
                        </a>
                    </li>

                    {{-- Dropdown Registro --}}
                    <li class="nav-item dropdown">
                        <a class="btn btn-primary dropdown-toggle btn-navbar-register" href="#" id="registroMenu"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Registro
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="registroMenu">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2"
                                    href="{{ route('registrar.cliente') }}">
                                    <i class="bi bi-person-check"></i>
                                    Soy cliente
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2"
                                    href="{{ route('registrar.profesional.opciones') }}">
                                    <i class="bi bi-tools"></i>
                                    Soy profesional
                                </a>
                            </li>
                        </ul>
                    </li>
                @endguest

                @auth
                    {{-- Logueados: Mi panel + Perfil + Cerrar sesión --}}
                    <li class="nav-item">
                        <a class="nav-link {{ $active === 'panel' ? 'active fw-semibold text-success border-bottom border-success' : '' }}"
                            href="{{ $panelRoute }}">
                            Mi panel
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ $active === 'perfil' ? 'active fw-semibold text-success border-bottom border-success' : '' }}"
                            href="{{ $perfilRoute }}">
                            Mi perfil
                        </a>
                    </li>

                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="nav-link p-0 border-0">
                                Cerrar sesión
                            </button>
                        </form>
                    </li>
                @endauth

            </ul>
        </div>
    </div>
</nav>
