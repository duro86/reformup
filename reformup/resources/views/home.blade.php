@extends('layouts.main')
@section('title', 'ReformUp')

@section('content')

    {{-- NAVBAR simple --}}
    <x-navbar />

    {{-- HERO con imagen de prueba --}}
    <section class="hero text-center">
        <div class="overlay"></div>
        <div class="container hero-content text-center">
            <h1 class="display-5 fw-bold text-white">Encuentra Profesionales de Confianza<br>para tu <span
                    class="text-warning">Reforma</span></h1>
            <p class="lead mt-3 mb-4">Conecta con especialistas verificados. Rápido, claro y con valoraciones reales.</p>

            {{-- Botones header --}}
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('public.profesionales.index') }}" class="btn btn-warning btn-lg fw-bold">Buscar
                    Profesionales</a>
                <a href="{{ route('registrar.profesional.opciones') }}" class="btn btn-outline-light btn-lg">Registrarte como
                    Profesional</a>
            </div>

            {{-- Iconos informativos --}}
            <div class="d-flex justify-content-center align-items-center gap-4 text-white mt-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-patch-check-fill text-primary"></i>
                    <span>Verificado</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-star-fill text-warning"></i>
                    <span>Valoraciones reales</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-info"></i>
                    <span>+ 500 clientes</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Paso a Paso / Como Funciona --}}
    <section class="container my-5 justify-center text-center">
        <h1 class="mb-3 destacado">Tu reforma, paso a paso</h1>
        <p>Regístrate, elige tu perfil (cliente o profesional) y cuéntanos qué necesitas.
        <p class="mb-5"><b>Acepta la mejor propuesta para crear el trabajo y, al finalizar, deja tu
                valoración.</b><br>Transparencia, rapidez y profesionales verificados.</p>

        {{-- Iconos informativos --}}
        <div class="row text-center mb-5 justify-between">
            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                <i class="bi bi-person-fill-add display-1 text-primary mb-3"></i>
                <h5>Regístrate</h5>
                <p>Podrás obtener información privilegiada sobre profesionales</p>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                <i class="bi bi-search display-1 text-primary mb-3"></i>
                <h5>Busca Profesionales</h5>
                <p>Busca y filtra por oficio y zona</p>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                <i class="bi bi-file-earmark-ppt display-1 text-primary mb-3"></i>
                <h5>Pide Presupuesto</h5>
                <p>Solicita presupuestos a profesionales verificados</p>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                <i class="bi bi-chat-dots-fill display-1 text-primary mb-3"></i>
                <h5>Deja Valoración</h5>
                <p>Comparte tu experiencia y ayuda a otros a elegir mejor.</p>
            </div>
        </div>
    </section>

    {{-- Profesionales --}}
    <section class="py-5 bg-light" id="profesionales-destacados">
        <div class="container">
            <h2 class="fw-bold mb-3 text-center">Profesionales destacados</h2>
            <p class="text-center text-muted mb-5">Profesionales mejor valorados del mes <i class="bi bi-trophy-fill"></i>
            </p>

            {{-- Card Profesionales mejor valorados --}}
            <div class="row g-4">
                @forelse ($profesionalesDestacados as $perfil)
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card h-100 shadow-sm card-pro">
                            <div class="d-flex align-items-center p-3">
                                @if ($perfil->avatar)
                                    <img class="rounded-circle me-3" src="{{ Storage::url($perfil->avatar) }}"
                                        alt="Foto {{ $perfil->empresa }}" width="60" height="60"
                                        style="object-fit:cover;">
                                @else
                                    <i class="bi bi-building me-3" style="font-size:2.5rem;"></i>
                                @endif

                                <div class="flex-grow-1" style="min-width:0;">
                                    <h5 class="mb-0 text-truncate d-block">
                                        {{ $perfil->empresa }}
                                    </h5>
                                    <small class="text-muted d-block text-truncate">
                                        {{ $perfil->ciudad }}
                                        @if ($perfil->provincia)
                                            - {{ $perfil->provincia }}
                                        @endif
                                    </small>
                                </div>


                                <i class="bi bi-star-fill text-warning ms-2"></i>
                            </div>

                            <div class="card-body">
                                {{-- Rating --}}
                                @if (!is_null($perfil->puntuacion_media))
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="text-warning me-2">⭐⭐⭐⭐⭐</div>
                                        <small class="text-muted">
                                            ({{ number_format($perfil->puntuacion_media, 1) }})
                                            {{-- si tienes conteo de reseñas lo añades aquí --}}
                                        </small>
                                    </div>
                                @endif

                                {{-- Oficios como badges --}}
                                @if ($perfil->relationLoaded('oficios') && $perfil->oficios->isNotEmpty())
                                    <div class="mb-3 d-flex flex-wrap gap-2">
                                        @foreach ($perfil->oficios as $oficio)
                                            <span class="badge bg-light text-dark rounded-pill px-3 py-1">
                                                {{ $oficio->nombre }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="card-footer bg-transparent border-top-0">
                                <a href="{{ route('public.profesionales.mostrar', $perfil) }}"
                                    class="btn btn-primary w-100">
                                    Ver perfil
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">Aún no hay profesionales destacados.</p>
                @endforelse
            </div>

        </div>
    </section>

    {{-- Sección: Comentarios y Valoraciones --}}
    {{-- Función para renderizar estrellas según la puntuación --}}
    @php
        $renderizarEstrellas = function ($puntuacion) {
            $puntuacion = (float) $puntuacion;
            $llenas = floor($puntuacion); // Número de estrellas llenas
            $media = $puntuacion - $llenas >= 0.5 ? 1 : 0; // Media estrella si es necesario
            $vacias = 5 - $llenas - $media; // Estrellas vacías

            $html = str_repeat('<i class="bi bi-star-fill text-warning"></i>', $llenas);
            $html .= str_repeat('<i class="bi bi-star-half text-warning"></i>', $media);
            $html .= str_repeat('<i class="bi bi-star text-warning"></i>', $vacias);

            return $html;
        };
    @endphp

    <section class="py-5">
        <div class="container" id="comentarios_valoraciones">
            <h2 class="text-center fw-bold mb-2">Comentarios y valoraciones</h2>
            <p class="text-center text-muted mb-4">
                Opiniones y valoración de nuestros clientes y profesionales.
            </p>

            {{-- Si no hay comentarios, muestra mensaje --}}
            @if ($slides->isEmpty())
                <p class="text-center text-muted">Todavía no hay comentarios publicados.</p>
            @else
                {{-- Carrusel de comentarios --}}
                <div id="reviewsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5500">
                    <div class="carousel-inner">
                        @foreach ($slides as $indice => $grupo)
                            <div class="carousel-item @if ($indice === 0) active @endif">
                                <div class="row g-4">
                                    @foreach ($grupo as $comentario)
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <article class="card shadow-sm border-0 rounded-4 h-100 review-card">
                                                <div class="card-body p-4">
                                                    {{-- Icono de comillas --}}
                                                    <div class="text-primary mb-2" style="opacity:.35">
                                                        <i class="bi bi-quote fs-1"></i>
                                                    </div>

                                                    {{-- Opinión del cliente --}}
                                                    <p class="mb-4 text-muted">
                                                        {{ $comentario->opinion }}
                                                    </p>

                                                    <div class="d-flex align-items-center">
                                                        {{-- Avatar del cliente --}}
                                                        <img src="/img/pro_card.jpg" class="rounded-circle me-3"
                                                            width="52" height="52"
                                                            alt="Foto de {{ $comentario->cliente->nombre ?? 'Cliente ReformUp' }}">

                                                        {{-- Nombre y ciudad del cliente --}}
                                                        <div class="me-auto">
                                                            <div class="fw-bold">
                                                                {{ $comentario->cliente->nombre ?? 'Cliente ReformUp' }}
                                                                {{ $comentario->cliente->apellidos ?? '' }}
                                                            </div>
                                                            <div class="text-muted small">
                                                                {{ $comentario->cliente->ciudad ?? '' }}
                                                            </div>
                                                        </div>

                                                        {{-- Puntuación en estrellas --}}
                                                        <div class="text-nowrap">
                                                            {!! $renderizarEstrellas($comentario->puntuacion) !!}
                                                            <span class="fw-semibold ms-1">
                                                                {{ number_format($comentario->puntuacion, 1) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </article>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Controles del carrusel --}}
                    <button class="carousel-control-prev" type="button" data-bs-target="#reviewsCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#reviewsCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
            @endif
        </div>
    </section>

    {{-- Footer  --}}
    <x-footer />

@endsection

<x-alertas_sweet />
