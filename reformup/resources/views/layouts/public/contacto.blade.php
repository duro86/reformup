@extends('layouts.main')

@section('title', 'Sobre nosotros - ReformUp')

@section('content')
    <x-navbar />

    {{-- HERO --}}
    <section class="about-hero py-5 mb-4">
        <div class="container">
            <div class="row align-items-center about-hero-inner">
                <div class="col-12 col-lg-7 mb-3 mb-lg-0 about-fade" data-delay="1">
                    <span class="badge bg-primary-subtle text-primary about-badge mb-2">
                        SOBRE NOSOTROS
                    </span>
                    <h1 class="fw-bold mb-3">
                        ReformUp: conectando clientes y profesionales de reformas desde Huelva
                    </h1>
                    <p class="text-muted mb-0">
                        ReformUp nace como un proyecto web pensado para hacer más fácil algo
                        que siempre ha sido un quebradero de cabeza: encontrar un buen profesional,
                        organizar la reforma y no perderse entre llamadas, papeles y WhatsApps.
                    </p>
                </div>
                <div class="col-12 col-lg-5 about-fade" data-delay="2">
                    {{-- Imagen genérica, cámbiala por la tuya --}}
                    <img src="{{ asset('img/sobreNosotros/sobre_nosotros_conectando.jpg') }}"
                        alt="Ordenador con panel de ReformUp" class="about-image shadow-sm">
                </div>
            </div>
        </div>
    </section>

    <div class="container pb-5">

        {{-- BLOQUE 1: Quiénes somos --}}
        <section class="mb-5 about-fade" data-delay="1">
            <div class="row align-items-center g-4">
                <div class="col-12 col-lg-6">
                    <h2 class="h4 fw-bold mb-3">
                        Un proyecto nacido en Huelva para hacer las reformas más sencillas
                    </h2>
                    <p class="text-muted">
                        ReformUp es una plataforma desarrollada desde Huelva, donde vivo y desde donde
                        he ido dando forma a esta idea con una visión muy clara: acercar la tecnología
                        a las personas que necesitan reformar su hogar o su negocio sin complicaciones.
                    </p>
                    <p class="text-muted mb-0">
                        Cada hora de planificación, cada línea de código y cada prueba se ha hecho aquí,
                        con la calma, la cercanía y la forma de entender las cosas que tiene Huelva:
                        directo, sencillo y pensando en que la aplicación sea fácil por fuera, aunque
                        por dentro lleve mucho trabajo.
                    </p>

                </div>
                <div class="col-12 col-lg-6">
                    <div class="position-relative">
                        <img src="{{ asset('/img/sobreNosotros/sobre_nosotros_colon.jpg') }}" alt="ReformUp en Huelva"
                            class="about-image shadow-sm w-100 rounded-4">

                        <a href="https://www.youtube.com/watch?v=7_Ulp2lXWiA" target="_blank" rel="noopener noreferrer"
                            class="btn btn-primary btn-video position-absolute top-50 start-50 translate-middle px-4 py-2 rounded-pill d-inline-flex align-items-center gap-2">
                            <i class="bi bi-play-circle-fill"></i>
                            Ver vídeo sobre Huelva
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- BLOQUE 2: Cómo funciona la app --}}
        <section class="mb-5 about-fade" data-delay="2">
            <div class="row align-items-center g-4">
                <div class="col-12 col-lg-6 order-lg-2">
                    <h2 class="h4 fw-bold mb-3">
                        Una app pensada para unir a las dos partes
                    </h2>
                    <p class="text-muted">
                        La plataforma está diseñada para que el cliente pueda registrar su
                        reforma, comparar profesionales y seguir el estado de su solicitud, mientras
                        que el profesional recibe las peticiones, genera presupuestos y gestiona
                        sus trabajos desde un panel claro y organizado.
                    </p>
                    <p class="text-muted mb-0">
                        Todo se estructura alrededor de cuatro piezas: solicitudes, presupuestos,
                        trabajos y valoraciones. Así se consigue que el proceso sea transparente
                        para ambas partes, desde el primer clic hasta la reseña final.
                    </p>
                </div>
                <div class="col-12 col-lg-6 order-lg-1">
                    {{-- Imagen tipo ordenador / interfaz --}}
                    <img src="{{ asset('img/sobreNosotros/sobre_nosotros_unir_partes.jpg') }}"
                        alt="Interfaz de la aplicación ReformUp" class="about-image shadow-sm">
                </div>
            </div>
        </section>

        {{-- BLOQUE 3: Tecnología y desarrollo --}}
        <section class="mb-5 about-fade" data-delay="3">
            <div class="row align-items-center g-4">
                <div class="col-12 col-lg-6">
                    <h2 class="h4 fw-bold mb-3">
                        Hecho con tecnologías web modernas
                    </h2>
                    <p class="text-muted">
                        ReformUp se ha desarrollado utilizando un stack moderno basado en
                        <span class="fw-semibold">Laravel</span> en el backend y
                        <span class="fw-semibold">Vue.js</span> en el frontend, con
                        <span class="fw-semibold">Bootstrap</span> para el diseño responsivo
                        y componentes reutilizables.
                    </p>
                    <p class="text-muted mb-0">
                        La aplicación está pensada como proyecto formativo y práctico,
                        demostrando cómo se puede construir una plataforma real para conectar
                        clientes y profesionales, con autenticación, paneles de control y
                        una experiencia de usuario cuidada.
                    </p>
                </div>
                <div class="col-12 col-lg-6">
                    {{-- Imagen tipo código / ordenador --}}
                    <img src="{{ asset('img/sobreNosotros/sobre_nosotros_web.png') }}"
                        alt="Código de la aplicación ReformUp en pantalla" class="about-image shadow-sm">
                </div>
            </div>
        </section>

        {{-- BLOQUE 4: Personas y confianza --}}
        <section class="mb-5 about-fade" data-delay="4">
            <div class="row align-items-center g-4">
                <div class="col-12 col-lg-6 order-lg-2">
                    <h2 class="h4 fw-bold mb-3">
                        Reformas, pero con personas en el centro
                    </h2>
                    <p class="text-muted">
                        Más allá de la tecnología, ReformUp gira en torno a algo simple:
                        confianza. Que un cliente pueda ver el historial de trabajos y
                        opiniones, y que un profesional pueda mostrar su experiencia y su
                        forma de trabajar.
                    </p>
                    <p class="text-muted mb-0">
                        Cada valoración, cada proyecto terminado y cada nueva solicitud
                        suma un punto más a esa confianza. La plataforma solo pone orden;
                        el buen trabajo lo siguen haciendo las personas.
                    </p>
                </div>
                <div class="col-12 col-lg-6 order-lg-1">
                    {{-- Imagen dos personas / reunión --}}
                    <img src="{{ asset('img/sobreNosotros/sobre_nosotros_solucion.jpg') }}"
                        alt="Cliente y profesional hablando sobre una reforma" class="about-image shadow-sm">
                </div>
            </div>
        </section>

        {{-- FRASE FINAL --}}
        <section class="about-fade" data-delay="1">
            <div class="mt-4 text-center mb-5">
                <p class="text-muted fst-italic mb-1">
                    «Clientes y profesionales solo tienen que seguir los pasos;
                    <span class="fw-semibold text-dark">ReformUp</span> se encarga del resto:
                    estados, flujos y notificaciones.»
                </p>
                <small class="text-secondary">
                    Una plataforma pensada para organizar reformas, sin complicarse la vida.
                </small>
            </div>
        </section>

        {{-- BLOQUE CONTACTO DENTRO DE "SOBRE NOSOTROS" --}}
        <section class="about-fade mb-5" data-delay="2">
            <div class="row g-4 align-items-center">
                {{-- Columna info + imagen autor --}}
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <span class="badge bg-success-subtle text-success mb-2">
                                ¿Hablamos?
                            </span>
                            <h2 class="h4 fw-bold mb-3">
                                Contacta con ReformUp
                            </h2>
                            <p class="text-muted mb-3">
                                Si tienes una idea, una mejora para la plataforma o simplemente quieres
                                comentar cómo podríamos adaptar ReformUp a tu empresa, puedes escribirme
                                directamente desde este formulario.
                            </p>

                            <div class="d-flex align-items-center gap-3 mb-3">
                                {{-- Imagen tuya (cámbiala por tu foto) --}}
                                <img src="{{ asset('img/sobreNosotros/alvaro.jpg') }}"
                                    alt="Alvaro Durán Amador - Desarrollador de ReformUp" class="rounded-circle shadow-sm"
                                    style="width:68px;height:68px;object-fit:cover;">
                                <div>
                                    <div class="fw-semibold mb-0">
                                        Álvaro Durán Amador
                                    </div>
                                    <small class="text-muted d-block">
                                        Desarrollador de ReformUp
                                    </small>
                                    <small class="text-muted">
                                        Proyecto web desarrollado desde Huelva
                                    </small>
                                </div>
                            </div>

                            <ul class="list-unstyled small text-muted mb-0">
                                <li class="mb-1">
                                    <i class="bi bi-buildings me-1"></i>
                                    <span>ReformUp · Plataforma de gestión de reformas</span>
                                </li>
                                <li class="mb-1">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    <span>Huelva · Andalucía · España</span>
                                </li>
                                <li class="mb-1">
                                    <i class="bi bi-envelope-at me-1"></i>
                                    <span>admin@reformup.es (ejemplo)</span>
                                </li>
                                <li class="mt-2">
                                    <small class="text-secondary">
                                        Esta web ha sido diseñada y desarrollada por
                                        <span class="fw-semibold">Álvaro Durán Amador</span> como proyecto formativo y
                                        práctico.
                                    </small>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Columna formulario contacto --}}
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h2 class="h5 fw-bold mb-3">
                                Envíanos un mensaje
                            </h2>
                            <p class="text-muted small mb-3">
                                Completa este formulario y el mensaje llegará al administrador de la plataforma.
                                Intentaremos responderte lo antes posible.
                            </p>

                            <form method="POST" action="{{ route('contacto.enviar') }}" novalidate>
                                @csrf

                                {{-- Nombre --}}
                                <div class="mb-3">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre"
                                        class="form-control @error('nombre') is-invalid @enderror"
                                        value="{{ old('nombre', auth()->user()->nombre ?? '') }}"
                                        placeholder="Tu nombre completo">
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="mb-3">
                                    <label class="form-label">Correo electrónico <span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', auth()->user()->email ?? '') }}"
                                        placeholder="tucorreo@ejemplo.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Asunto --}}
                                <div class="mb-3">
                                    <label class="form-label">Asunto <span class="text-danger">*</span></label>
                                    <input type="text" name="asunto"
                                        class="form-control @error('asunto') is-invalid @enderror"
                                        value="{{ old('asunto') }}" placeholder="¿Sobre qué quieres hablar?">
                                    @error('asunto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mensaje --}}
                                <div class="mb-3">
                                    <label class="form-label">Mensaje <span class="text-danger">*</span></label>
                                    <textarea name="mensaje" rows="4" class="form-control @error('mensaje') is-invalid @enderror"
                                        placeholder="Cuéntanos brevemente qué necesitas o qué te gustaría comentar..." style="resize:none;">{{ old('mensaje') }}</textarea>
                                    @error('mensaje')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Privacidad --}}
                                <div class="mb-3 form-check">
                                    <input type="checkbox" name="privacidad" id="privacidad"
                                        class="form-check-input @error('privacidad') is-invalid @enderror"
                                        {{ old('privacidad') ? 'checked' : '' }}>
                                    <label for="privacidad" class="form-check-label small">
                                        He leído y acepto la
                                        <a href="#" class="link-primary" target="_blank">
                                            política de privacidad
                                        </a>.
                                    </label>
                                    @error('privacidad')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Botón enviar --}}
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-1"></i>
                                        Enviar mensaje
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </section>

    </div>

    <x-footer />
@endsection

<x-alertas_sweet />

{{-- Aparición de los bloques "Sobre nosotros" al hacer scroll --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sections = document.querySelectorAll('.about-fade');

        if (!('IntersectionObserver' in window)) {
            sections.forEach(s => s.classList.add('is-visible'));
            return;
        }

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.25
        });

        sections.forEach(section => observer.observe(section));
    });
</script>

<x-alertas_sweet />
{{-- Aparición de los bloques "Sobre nosotros" al hacer scroll --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sections = document.querySelectorAll('.about-fade');

        if (!('IntersectionObserver' in window)) {
            sections.forEach(s => s.classList.add('is-visible'));
            return;
        }

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.25
        });

        sections.forEach(section => observer.observe(section));
    });
</script>
