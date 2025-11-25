@extends('layouts.main')

@section('title', $perfil->empresa . ' - ReformUp')

@section('content')
    <x-navbar />

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="text-center mb-4">
                    @if($perfil->avatar)
                        <img src="{{ Storage::url($perfil->avatar) }}"
                             class="rounded-circle mb-3"
                             style="width:80px;height:80px;object-fit:cover;">
                    @else
                        <i class="bi bi-building" style="font-size:3rem;"></i>
                    @endif

                    <h1 class="h4 mb-1">{{ $perfil->empresa }}</h1>
                    <p class="text-muted mb-1">
                        {{ $perfil->ciudad }}
                        @if($perfil->provincia) - {{ $perfil->provincia }} @endif
                    </p>
                    @if(!is_null($perfil->puntuacion_media))
                        <p class="mb-0">
                            ⭐ {{ number_format($perfil->puntuacion_media, 1, ',', '.') }} / 5
                        </p>
                    @endif
                </div>

                @if($perfil->bio)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h2 class="h5 mb-3 text-center">Sobre la empresa</h2>
                            <p class="mb-0">{{ $perfil->bio }}</p>
                        </div>
                    </div>
                @endif

                <div class="card mb-3">
                    <div class="card-body">
                        <h2 class="h5 mb-3">Datos de contacto</h2>

                        @if($perfil->email_empresa)
                            <p class="mb-1">
                                <i class="bi bi-envelope me-1"></i>
                                {{ $perfil->email_empresa }}
                            </p>
                        @endif

                        @if($perfil->telefono_empresa)
                            <p class="mb-1">
                                <i class="bi bi-telephone me-1"></i>
                                {{ $perfil->telefono_empresa }}
                            </p>
                        @endif

                        @if($perfil->web)
                            <p class="mb-1">
                                <i class="bi bi-globe me-1"></i>
                                <a href="{{ $perfil->web }}" target="_blank" rel="noopener noreferrer">
                                    {{ $perfil->web }}
                                </a>
                            </p>
                        @endif

                        @if($perfil->dir_empresa)
                            <p class="mb-0">
                                <i class="bi bi-geo-alt me-1"></i>
                                {{ $perfil->dir_empresa }}
                            </p>
                        @endif
                    </div>
                </div>

                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    Volver
                </a>
            </div>
        </div>
    </div>
@endsection
