@props([
  // opcional: resalta el enlace activo => 'buscar'|'pasos'|'valoraciones'|''
  'active' => '',
  // URL del logo (por si cambiamos desde layout)
  'logo' => asset('img/logoPNGReformupNuevo.svg'),
])


<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom small shadow-sm">
  <div class="container">
        {{-- Marca (logo + texto accesible) --}}
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}" aria-label="Inicio ReformUp">
        <img src="{{ $logo }}" alt="ReformUp" height="60" class="d-inline-block align-text-top">
        </a>

        {{-- Botón hamburguesa Icono desplegable--}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Alternar navegación">
        <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Menú de navegación --}}
        <div class="collapse navbar-collapse" id="mainNav">

            {{-- IZQUIERDA: enlaces principales --}}
            <ul class="navbar-nav justify-content-end flex-grow-1 me-5">
                <li class="nav-item">
                    <a class="nav-link @if($active==='buscar') active fw-semibold @endif"
                        href="{{ route('home') }}#buscar-profesionales">
                        Buscar profesionales
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($active==='pasos') active fw-semibold @endif"
                        href="{{ route('home') }}#como-funciona">
                        Paso a paso
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($active==='valoraciones') active fw-semibold @endif"
                        href="{{ route('home') }}#valoraciones">
                        Valoraciones
                    </a>
                </li>
            </ul>

            {{-- DERECHA: entrar + registro --}}
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item mb-sm-3 mb-lg-0">
                    <a class="nav-link" href="{{ route('login') }}" >
                        Entrar
                    </a>
                </li>

                {{-- Dropdown Registro --}}
                <li class="nav-item dropdown">
                    <a class="btn btn-primary dropdown-toggle px-3 py-2" href="#" id="registroMenu" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Registro
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="registroMenu">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('registrar.cliente') }}">
                                <i class="bi bi-person-check"></i>
                                Soy cliente
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('registrar.profesional.opciones') }}">
                                <i class="bi bi-tools"></i>
                                Soy profesional
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
  </div>
</nav>
