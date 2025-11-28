@extends('layouts.main')

@section('title', 'Cómo funciona - Paso a paso - ReformUp')

@section('content')
    <x-navbar />

    <div class="container py-5">

        {{-- TÍTULO PRINCIPAL --}}
        <div class="text-center mb-5">
            <h1 class="fw-bold mb-3">Cómo funciona ReformUp</h1>
            <p class="text-muted">
                Sigue estos pasos y deja que la plataforma haga el trabajo duro.
                Tú solo te preocupas de tu reforma.
            </p>
        </div>

        {{-- ============================== --}}
        {{-- BLOQUE: SI ERES CLIENTE        --}}
        {{-- ============================== --}}
        <section class="py-5 paso-cliente">
            <div class="container">
                <h2 class="h4 fw-bold text-center mb-3">
                    Si eres <span class="text-primary">CLIENTE</span>
                </h2>
                <p class="text-center text-muted mb-4">
                    Solo sigue estos pasos y deja que ReformUp se encargue del resto.
                </p>

                <div class="row g-4">
                    {{-- Paso 1 --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card step-card step-card-appear h-100 shadow-sm" data-delay="1">
                            <div class="card-body text-center">
                                <div class="step-badge bg-primary text-white mb-3">
                                    1
                                </div>
                                <h3 class="h6 fw-bold mb-2">
                                    <i class="bi bi-person-plus text-primary me-1"></i>
                                    Regístrate como usuario
                                    <span class="arrow-animated-inline">
                                        <i class="bi bi-arrow-right"></i>
                                    </span>
                                </h3>
                                <p class="small text-muted mb-0">
                                    Crea tu cuenta para poder crear solicitudes,
                                    ver presupuestos y hacer el seguimiento de tus trabajos.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Paso 2 --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card step-card step-card-appear h-100 shadow-sm" data-delay="2">
                            <div class="card-body text-center">
                                <div class="step-badge bg-primary text-white mb-3">
                                    2
                                </div>
                                <h3 class="h6 fw-bold mb-2">
                                    <i class="bi bi-search text-primary me-1"></i>
                                    Busca profesionales
                                    <span class="arrow-animated-inline">
                                        <i class="bi bi-arrow-right"></i>
                                    </span>
                                </h3>
                                <p class="small text-muted mb-0">
                                    Usa el buscador para filtrar por ciudad, provincia y valoración.
                                    Revisa perfiles, trabajos realizados y opiniones de otros clientes.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Paso 3 --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card step-card step-card-appear h-100 shadow-sm" data-delay="3">
                            <div class="card-body text-center">
                                <div class="step-badge bg-primary text-white mb-3">
                                    3
                                </div>
                                <h3 class="h6 fw-bold mb-2">
                                    <i class="bi bi-file-text text-primary me-1"></i>
                                    Crea una solicitud
                                    <span class="arrow-animated-inline">
                                        <i class="bi bi-arrow-right"></i>
                                    </span>
                                </h3>
                                <p class="small text-muted mb-0">
                                    Explica qué reforma necesitas, dónde y en qué plazos.
                                    El profesional recibirá toda la información desde la plataforma.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Paso 4 --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card step-card step-card-appear h-100 shadow-sm" data-delay="4">
                            <div class="card-body text-center">
                                <div class="step-badge bg-primary text-white mb-3">
                                    4
                                </div>
                                <h3 class="h6 fw-bold mb-2">
                                    <i class="bi bi-receipt text-primary me-1"></i>
                                    Recibe presupuesto
                                    <span class="arrow-animated-inline">
                                        <i class="bi bi-arrow-right"></i>
                                    </span>
                                </h3>
                                <p class="small text-muted mb-0">
                                    El profesional te envía el presupuesto desde su panel.
                                    Lo ves en tu zona privada sin llamadas ni correos interminables.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Paso 5 --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card step-card step-card-appear h-100 shadow-sm" data-delay="5">
                            <div class="card-body text-center">
                                <div class="step-badge bg-primary text-white mb-3">
                                    5
                                </div>
                                <h3 class="h6 fw-bold mb-2">
                                    <i class="bi bi-filetype-pdf"></i>
                                    Acepta el presupuesto
                                    <span class="arrow-animated-inline">
                                        <i class="bi bi-arrow-right"></i>
                                    </span>
                                </h3>
                                <p class="small text-muted mb-0">
                                    Si encaja contigo, lo aceptas con un clic.
                                    Automáticamente se crea el trabajo asociado.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Paso 6 --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card step-card step-card-appear h-100 shadow-sm" data-delay="6">
                            <div class="card-body text-center">
                                <div class="step-badge bg-success text-white mb-3">
                                    6
                                </div>
                                <h3 class="h6 fw-bold mb-2">
                                    <i class="bi bi-star-fill text-warning me-1"></i>
                                    Se realiza el trabajo y valoras
                                </h3>
                                <p class="small text-muted mb-0">
                                    El profesional finaliza el trabajo y tú dejas tu valoración.
                                    Eso ayuda a otros clientes y da visibilidad a los mejores profesionales.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Aquí ya puedes dejar debajo el bloque de “Progreso de cada paso” con los estados --}}
            </div>
        </section>

        <hr class="my-5">


        {{-- ============================== --}}
        {{-- BLOQUE: SI ERES PROFESIONAL    --}}
        {{-- ============================== --}}
        <section class="py-5">
            <div class="container">
                <h2 class="h4 fw-bold text-center mb-3">
                    Si eres <span class="text-primary">PROFESIONAL</span>
                </h2>
                <p class="text-center text-muted mb-4">
                    Da de alta tu empresa, gestiona solicitudes y convierte reseñas en tu mejor carta de presentación.
                </p>

                {{-- --------------------  Versión ESCRITORIO: serpentina ----------- ------------ --}}
                <div class="d-none d-lg-block">
                    <div class="steps-prof-grid">
                        {{-- Paso 1 (arriba izquierda) --}}
                        <div class="pro-step-card step-card-appear step-prof-1 card shadow-sm" data-delay="1">
                            <div class="card-body text-center">
                                <div class="step-badge bg-primary text-white mb-3">1</div>
                                <h3 class="h6 fw-bold mb-2">
                                    <i class="bi bi-person-gear text-primary me-1"></i>
                                    Regístrate como usuario
                                </h3>
                                <p class="small text-muted mb-0">
                                    Crea tu cuenta y accede al panel desde el que gestionarás
                                    toda tu actividad como profesional.
                                </p>
                                {{-- Flecha a la derecha (hacia el paso 2) --}}
                                <div class="pro-arrow-horizontal">
                                    <i class="bi bi-arrow-right-circle"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Paso 2 (arriba centro) --}}
                        <div class="pro-step-card step-card-appear step-prof-2 card shadow-sm" data-delay="2">
                            <div class="card-body text-center">
                                <div class="step-badge bg-primary text-white mb-3">2</div>
                                <h3 class="h6 fw-bold mb-2">
                                    <i class="bi bi-building text-primary me-1"></i>
                                    Registra tu empresa
                                </h3>
                                <p class="small text-muted mb-0">
                                    Completa tu perfil: datos de empresa, oficios, zona de trabajo,
                                    foto, web y formas de contacto.
                                </p>
                                {{-- Flecha a la derecha (hacia el paso 3) --}}
                                <div class="pro-arrow-horizontal">
                                    <i class="bi bi-arrow-right-circle"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Paso 3 (arriba derecha) --}}
                        <div class="pro-step-card step-card-appear step-prof-3 card shadow-sm" data-delay="3">
                            <div class="card-body text-center">
                                <div class="step-badge bg-primary text-white mb-3">3</div>
                                <h3 class="h6 fw-bold mb-2">
                                    <i class="bi bi-inbox text-primary me-1"></i>
                                    Recibe solicitudes
                                </h3>
                                <p class="small text-muted mb-0">
                                    Los clientes te encontrarán en el buscador y te enviarán
                                    solicitudes con la información de la reforma.
                                </p>
                                {{-- Flecha hacia abajo (hacia el paso 4) --}}
                                <div class="pro-arrow-vertical">
                                    <i class="bi bi-arrow-down-circle"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Paso 4 (abajo derecha, bajo 3) --}}
                        <div class="pro-step-card step-card-appear step-prof-4 card shadow-sm" data-delay="4">
                            <div class="card-body text-center">
                                <div class="step-badge bg-primary text-white mb-3">4</div>
                                <h3 class="h6 fw-bold mb-2">
                                    <i class="bi bi-file-earmark-text text-primary me-1"></i>
                                    Crea presupuestos
                                </h3>
                                <p class="small text-muted mb-0">
                                    Desde tu panel generas presupuestos claros y los envías
                                    directamente al cliente desde la plataforma.
                                </p>
                                {{-- Flecha a la izquierda (hacia el paso 5) --}}
                                <div class="pro-arrow-horizontal">
                                    <i class="bi bi-arrow-left-circle"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Paso 5 (abajo centro, bajo 2) --}}
                        <div class="pro-step-card step-card-appear step-prof-5 card shadow-sm" data-delay="5">
                            <div class="card-body text-center">
                                <div class="step-badge bg-primary text-white mb-3">5</div>
                                <h3 class="h6 fw-bold mb-2">
                                    <i class="bi bi-play-circle text-primary me-1"></i>
                                    Inicia y gestiona el trabajo
                                </h3>
                                <p class="small text-muted mb-0">
                                    Cuando el cliente acepta, se crea el trabajo. Cambia su estado
                                    a <em>en curso</em> y gestiona el avance desde tu panel.
                                </p>
                                {{-- Flecha a la izquierda (hacia el paso 6) --}}
                                <div class="pro-arrow-horizontal">
                                    <i class="bi bi-arrow-left-circle"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Paso 6 (abajo izquierda, bajo 1) --}}
                        <div class="pro-step-card step-card-appear step-prof-6 card shadow-sm" data-delay="6">
                            <div class="card-body text-center">
                                <div class="step-badge bg-success text-white mb-3">6</div>
                                <h3 class="h6 fw-bold mb-2">
                                    <i class="bi bi-chat-left-quote text-primary me-1"></i>
                                    Finaliza y lee tus reseñas
                                </h3>
                                <p class="small text-muted mb-0">
                                    Al cerrar el trabajo el cliente deja su valoración.
                                    Cuantas más reseñas positivas tengas, más visibilidad tendrás.
                                </p>
                                {{-- Opcional: flecha hacia arriba para cerrar el “bucle” visual --}}
                                <div class="pro-arrow-vertical">
                                    <i class="bi bi-hand-thumbs-up"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Versión MÓVIL/TABLET: lineal y clara --}}
                <div class="d-block d-lg-none">
                    <div class="row g-3">

                        {{-- Paso 1 --}}
                        <div class="col-12">
                            <div class="card pro-step-card step-card-appear shadow-sm" data-delay="1">
                                <div class="card-body text-center">
                                    <div class="step-badge bg-primary text-white mb-3">1</div>
                                    <h3 class="h6 fw-bold mb-2">
                                        <i class="bi bi-person-gear text-primary me-1"></i>
                                        Regístrate como usuario
                                    </h3>
                                    <p class="small text-muted mb-0">
                                        Crea tu cuenta y accede al panel profesional desde el que gestionarás tu actividad.
                                    </p>
                                    <div class="pro-arrow-vertical">
                                        <i class="bi bi-arrow-down-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Paso 2 --}}
                        <div class="col-12">
                            <div class="card pro-step-card step-card-appear shadow-sm" data-delay="2">
                                <div class="card-body text-center">
                                    <div class="step-badge bg-primary text-white mb-3">2</div>
                                    <h3 class="h6 fw-bold mb-2">
                                        <i class="bi bi-building text-primary me-1"></i>
                                        Registra tu empresa
                                    </h3>
                                    <p class="small text-muted mb-0">
                                        Completa tu perfil profesional con datos de empresa, oficios, zona de trabajo y
                                        contacto.
                                    </p>
                                    <div class="pro-arrow-vertical">
                                        <i class="bi bi-arrow-down-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Paso 3 --}}
                        <div class="col-12">
                            <div class="card pro-step-card step-card-appear shadow-sm" data-delay="3">
                                <div class="card-body text-center">
                                    <div class="step-badge bg-primary text-white mb-3">3</div>
                                    <h3 class="h6 fw-bold mb-2">
                                        <i class="bi bi-inbox text-primary me-1"></i>
                                        Recibe solicitudes
                                    </h3>
                                    <p class="small text-muted mb-0">
                                        Los clientes te encuentran en el buscador y te envían solicitudes de reforma con
                                        todos los datos.
                                    </p>
                                    <div class="pro-arrow-vertical">
                                        <i class="bi bi-arrow-down-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Paso 4 --}}
                        <div class="col-12">
                            <div class="card pro-step-card step-card-appear shadow-sm" data-delay="4">
                                <div class="card-body text-center">
                                    <div class="step-badge bg-primary text-white mb-3">4</div>
                                    <h3 class="h6 fw-bold mb-2">
                                        <i class="bi bi-file-earmark-text text-primary me-1"></i>
                                        Crea presupuestos
                                    </h3>
                                    <p class="small text-muted mb-0">
                                        Generas y envías presupuestos desde la propia plataforma, sin correos ni documentos
                                        sueltos.
                                    </p>
                                    <div class="pro-arrow-vertical">
                                        <i class="bi bi-arrow-down-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Paso 5 --}}
                        <div class="col-12">
                            <div class="card pro-step-card step-card-appear shadow-sm" data-delay="5">
                                <div class="card-body text-center">
                                    <div class="step-badge bg-primary text-white mb-3">5</div>
                                    <h3 class="h6 fw-bold mb-2">
                                        <i class="bi bi-play-circle text-primary me-1"></i>
                                        Inicia y gestiona el trabajo
                                    </h3>
                                    <p class="small text-muted mb-0">
                                        Al aceptar el cliente el presupuesto, se crea el trabajo. Cambias su estado y haces
                                        el seguimiento.
                                    </p>
                                    <div class="pro-arrow-vertical">
                                        <i class="bi bi-arrow-down-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Paso 6 (último, sin flecha abajo) --}}
                        <div class="col-12">
                            <div class="card pro-step-card step-card-appear shadow-sm" data-delay="6">
                                <div class="card-body text-center">
                                    <div class="step-badge bg-success text-white mb-3">6</div>
                                    <h3 class="h6 fw-bold mb-2">
                                        <i class="bi bi-chat-left-quote text-primary me-1"></i>
                                        Finaliza y lee tus reseñas
                                    </h3>
                                    <p class="small text-muted mb-0">
                                        Cierras el trabajo y el cliente deja su valoración. Las buenas reseñas impulsan tu
                                        perfil.
                                    </p>
                                    {{-- Aquí ya no ponemos flecha, es el final del flujo --}}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>

        <hr class="my-5">

        {{-- ==================================== --}}
        {{-- BLOQUE: ESTADOS / PROGRESO DETALLADO --}}
        {{-- ==================================== --}}
        <section>
            <h2 class="h4 fw-bold text-center mb-1">Progreso de cada paso</h2>
            <p class="text-center text-muted mb-4">
                Así se comportan los estados internos de la plataforma para solicitudes, presupuestos,
                trabajos y comentarios.
            </p>

            <div class="row g-4">
                {{-- Solicitud --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h3 class="h6 fw-bold mb-3">
                                <i class="bi bi-file-text me-1 text-primary"></i>
                                Solicitud
                            </h3>
                            <ul class="small text-muted ps-3 mb-0">
                                @foreach ($estadosSolicitud ?? [] as $estado)
                                    <li>{{ ucfirst(str_replace('_', ' ', $estado)) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Presupuesto --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h3 class="h6 fw-bold mb-3">
                                <i class="bi bi-receipt me-1 text-primary"></i>
                                Presupuesto
                            </h3>
                            <ul class="small text-muted ps-3 mb-0">
                                @foreach ($estadosPresupuesto ?? [] as $estado)
                                    <li>{{ ucfirst(str_replace('_', ' ', $estado)) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Trabajo --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h3 class="h6 fw-bold mb-3">
                                <i class="bi bi-hammer me-1 text-primary"></i>
                                Trabajo
                            </h3>
                            <ul class="small text-muted ps-3 mb-0">
                                @foreach ($estadosTrabajo ?? [] as $estado)
                                    <li>{{ ucfirst(str_replace('_', ' ', $estado)) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Comentario / Valoración --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h3 class="h6 fw-bold mb-3">
                                <i class="bi bi-chat-left-quote me-1 text-primary"></i>
                                Valoración / Comentario
                            </h3>
                            <ul class="small text-muted ps-3 mb-0">
                                @foreach ($estadosComentario ?? [] as $estado)
                                    <li>{{ ucfirst(str_replace('_', ' ', $estado)) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-center">
                <p class="text-muted fst-italic mb-1">
                    «Clientes y profesionales solo tienen que seguir estos pasos;
                    <span class="fw-semibold text-dark">ReformUp</span> se encarga del resto:
                    estados, flujos y notificaciones.»
                </p>
                <small class="text-primary">
                    Tu plataforma para gestionar reformas sin complicarte la vida.
                </small>
            </div>
        </section>
    </div>
    {{-- Footer  --}}
    <x-footer />
@endsection
<x-alertas_sweet />
{{-- Gestionamos la aparicion de las cartas cliente  --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.step-card-appear');

        if (!('IntersectionObserver' in window)) {
            // Si el navegador es muy viejo, las mostramos todas sin animación
            cards.forEach(card => card.classList.add('is-visible'));
            return;
        }

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    // Deja de observar esa card una vez se ha mostrado
                    obs.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.5 // cuando el 50% de la card esté visible
        });

        cards.forEach(card => observer.observe(card));
    });
</script>
