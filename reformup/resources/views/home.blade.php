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
                <a href="#" class="btn btn-warning btn-lg fw-bold">Buscar Profesionales</a>
                <a href="{{ route('registrar.profesional') }}" class="btn btn-outline-light btn-lg">Registrarte como
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

            <div class="row g-4">
                @php
                    $profesionalesPrueba = [
                        [
                            'nombre' => 'Ana Pérez',
                            'rating' => 5.0,
                            'resenas' => 20,
                            'ciudad' => 'Madrid',
                            'oficios' => ['Electricista', 'Fontanero'],
                        ],
                        [
                            'nombre' => 'Luis Gómez',
                            'rating' => 4.8,
                            'resenas' => 15,
                            'ciudad' => 'Barcelona',
                            'oficios' => ['Albañil'],
                        ],
                        [
                            'nombre' => 'Marta Ruiz',
                            'rating' => 4.9,
                            'resenas' => 30,
                            'ciudad' => 'Valencia',
                            'oficios' => ['Cerrajero', 'Jardinero'],
                        ],
                        [
                            'nombre' => 'Carlos Flores',
                            'rating' => 5.0,
                            'resenas' => 12,
                            'ciudad' => 'Sevilla',
                            'oficios' => ['Pintor'],
                        ],
                        [
                            'nombre' => 'Laura Díaz',
                            'rating' => 4.7,
                            'resenas' => 28,
                            'ciudad' => 'Bilbao',
                            'oficios' => ['Carpintero'],
                        ],
                        [
                            'nombre' => 'Javier Sánchez',
                            'rating' => 4.6,
                            'resenas' => 18,
                            'ciudad' => 'Granada',
                            'oficios' => ['Electricista'],
                        ],
                        [
                            'nombre' => 'Sandra Molina',
                            'rating' => 4.9,
                            'resenas' => 24,
                            'ciudad' => 'Zaragoza',
                            'oficios' => ['Fontanero'],
                        ],
                        [
                            'nombre' => 'Pedro López',
                            'rating' => 5.0,
                            'resenas' => 22,
                            'ciudad' => 'Málaga',
                            'oficios' => ['Albañil', 'Pintor'],
                        ],
                    ];
                @endphp

                {{-- Array de Profesionales --}}
                @foreach ($profesionalesPrueba as $p)
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card h-100 shadow-sm card-pro">
                            <div class="d-flex align-items-center p-3">
                                <img class="rounded-circle me-3" src="/img/pro_card.jpg" alt="Foto {{ $p['nombre'] }}"
                                    width="60" height="60">
                                <h5 class="mb-0 flex-grow-1 text-truncate">{{ $p['nombre'] }}</h5>
                                <i class="bi bi-star-fill text-warning" title="Icono que decidirás"></i>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="text-warning me-2">
                                        {{-- Aquí las estrellas dinámicas más adelante --}}
                                        ⭐⭐⭐⭐⭐
                                    </div>
                                    <small class="text-muted">({{ number_format($p['rating'], 1) }}) - {{ $p['resenas'] }}
                                        reseñas</small>
                                </div>
                                <div class="mb-3">
                                    <i class="bi bi-geo-alt-fill text-secondary me-1"></i>
                                    <small class="text-muted">{{ $p['ciudad'] }}</small>
                                </div>
                                <div class="mb-3 d-flex flex-wrap gap-2">
                                    @foreach ($p['oficios'] as $o)
                                        <span
                                            class="badge bg-light text-dark rounded-pill px-3 py-1">{{ $o }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <a href="#" class="btn btn-primary w-100">Ver perfil</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Comentarios/Valoraciones --}}
    {{-- Array Comentarios/Valoraciones --}}
    @php
        $comentarios = [
            [
                'comentario' => 'Excelente trabajo, rápido y profesional. Recomiendo 100% esta plataforma.',
                'nombre' => 'Ana López',
                'ubicacion' => 'Madrid/Educación',
                'foto' => '/img/pro_card.jpg',
                'estrellas' => 5,
            ],
            [
                'comentario' => 'Me ayudaron en una emergencia y todo fue eficiente.',
                'nombre' => 'Pedro Martínez',
                'ubicacion' => 'Barcelona/Autónomo',
                'foto' => '/img/pro_card.jpg',
                'estrellas' => 4.5,
            ],
            [
                'comentario' => 'Profesionales atentos y resultados excelentes.',
                'nombre' => 'Lucía Pérez',
                'ubicacion' => 'Valencia/Arquitecta',
                'foto' => '/img/pro_card.jpg',
                'estrellas' => 5,
            ],
            [
                'comentario' => 'Profesionales atentos y resultados excelentes.',
                'nombre' => 'Lucía Pérez',
                'ubicacion' => 'Valencia/Arquitecta',
                'foto' => '/img/pro_card.jpg',
                'estrellas' => 5,
            ],
            [
                'comentario' => 'Profesionales atentos y resultados excelentes.',
                'nombre' => 'Lucía Pérez',
                'ubicacion' => 'Valencia/Arquitecta',
                'foto' => '/img/pro_card.jpg',
                'estrellas' => 5,
            ],
        ];
    @endphp

    {{-- Titulo Comentarios/Valoraciones --}}
    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-2">Comentarios y valoraciones</h2>
            <p class="text-center text-muted mb-4">Opiniones y valoración de nuestros clientes y profesionales.</p>

            @php
                // Agrupar en slides de 3
                $slides = array_chunk($comentarios, 3);
                // Función para pintar estrellas con medios puntos
                $renderStars = function ($score) {
                    $full = floor($score);
                    $half = $score - $full >= 0.5 ? 1 : 0;
                    $empty = 5 - $full - $half;
                    $html = str_repeat('<i class="bi bi-star-fill text-warning"></i>', $full);
                    $html .= str_repeat('<i class="bi bi-star-half text-warning"></i>', $half);
                    $html .= str_repeat('<i class="bi bi-star text-warning"></i>', $empty);
                    return $html;
                };
            @endphp

            <div id="reviewsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5500">

                {{-- Indicadores --}}
                <div class="carousel-indicators">
                    @foreach ($slides as $i => $_)
                        <button type="button" data-bs-target="#reviewsCarousel" data-bs-slide-to="{{ $i }}"
                            @class(['active' => $i === 0]) aria-current="{{ $i === 0 ? 'true' : 'false' }}"
                            aria-label="Slide {{ $i + 1 }}"></button>
                    @endforeach
                </div>

                {{-- Carrusel --}}
                <div class="carousel-inner">
                    @foreach ($slides as $i => $grupo)
                        <div class="carousel-item @if ($i === 0) active @endif">
                            <div class="row g-4">
                                @foreach ($grupo as $c)
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <article class="card shadow-sm border-0 rounded-4 h-100 review-card">
                                            <div class="card-body p-4">
                                                <div class="text-primary mb-2" style="opacity:.35">
                                                    <i class="bi bi-quote fs-1"></i>
                                                </div>

                                                <p class="mb-4 text-muted">{{ $c['comentario'] }}</p>

                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $c['foto'] }}" class="rounded-circle me-3"
                                                        width="52" height="52" alt="Foto de {{ $c['nombre'] }}">

                                                    <div class="me-auto">
                                                        <div class="fw-bold">{{ $c['nombre'] }}</div>
                                                        <div class="text-muted small">{{ $c['ubicacion'] }}</div>
                                                    </div>

                                                    <div class="text-nowrap">
                                                        {!! $renderStars($c['estrellas']) !!}
                                                        <span
                                                            class="fw-semibold ms-1">{{ number_format($c['estrellas'], 1) }}</span>
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

                {{-- Controles --}}
                <button class="carousel-control-prev" type="button" data-bs-target="#reviewsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#reviewsCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>
        
    </section>

    {{-- Footer  --}}
    <x-footer />

@endsection
<x-alertas_sweet />
