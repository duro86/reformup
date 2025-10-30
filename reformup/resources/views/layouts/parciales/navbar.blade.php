<nav class="navbar navbar-expand-lg custom-navbar shadow-sm sticky-top">
  <div class="container">
    <a class="navbar navbar-expand-lg navbar-light bg-light fixed-top" href="{{ route('home') }}">
      <img src="/img/logo-reformup.svg" alt="ReformUp" height="28" class="me-2"> ReformUp
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
        <li class="nav-item"><a class="nav-link" href="#">Buscar Profesionales</a></li>
        <li class="nav-item"><a class="nav-link" href="#como-funciona">Cómo funciona</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Destacados</a></li>
        <li class="nav-item"><a class="btn btn-outline-primary me-2" href="#">Entrar</a></li>
        <li class="nav-item dropdown">
          <a class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">Registrarse</a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">Soy cliente</a></li>
            <li><a class="dropdown-item" href="#">Soy profesional</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
