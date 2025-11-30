@extends('layouts.main')

@section('title', 'Editar comentario de usuario - Admin - ReformUp')

@section('content')
    <x-navbar />
    <x-admin.admin_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        {{-- Nav móvil admin --}}
        <x-admin.nav_movil active="comentarios" />

        <div class="container py-4">
            @php
                $trabajo = $comentario->trabajo;
                $presu = $trabajo?->presupuesto;
                $solicitud = $presu?->solicitud;
                $cliente = $solicitud?->cliente;
                $pro = $presu?->profesional;
            @endphp
            <h1 class="h4 mb-3">
                Editar comentario #{{ $comentario->id }} del usuario {{ $cliente->nombre }} {{ $cliente->apellidos }}
            </h1>

            {{-- Errores de validación --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Revisa los errores del formulario:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Bloque de contexto --}}
            <div class="card mb-3">
                <div class="card-body">
                    <div class="small text-muted mb-1">
                        Trabajo:
                        @if ($solicitud?->titulo)
                            <strong>{{ $solicitud->titulo }}</strong>
                        @else
                            <strong>Trabajo #{{ $trabajo?->id }}</strong>
                        @endif
                    </div>

                    @if ($pro)
                        <div class="small text-muted mb-1">
                            Profesional:
                            <strong>{{ $pro->empresa }}</strong>
                            @if ($pro->ciudad || $pro->provincia)
                                <span> — {{ $pro->ciudad }}{{ $pro->provincia ? ' - ' . $pro->provincia : '' }}</span>
                            @endif
                        </div>
                    @endif

                    @if ($cliente)
                        <div class="small text-muted mb-1">
                            Cliente:
                            <strong>
                                {{ $cliente->nombre ?? $cliente->name }}
                                {{ $cliente->apellidos ?? '' }}
                            </strong>
                            <span> — {{ $cliente->email }}</span>
                        </div>
                    @endif

                    <div class="small text-muted">
                        Estado actual del comentario:
                        <strong>{{ ucfirst($comentario->estado) }}</strong>
                        @if ($comentario->visible)
                            <span class="badge bg-success ms-1">Visible</span>
                        @else
                            <span class="badge bg-secondary ms-1">Oculto</span>
                        @endif
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.comentarios.actualizar', $comentario) }}" method="POST" class="card p-3">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Puntuación</label>
                    <select name="puntuacion" class="form-select @error('puntuacion') is-invalid @enderror" required>
                        <option value="">Elige una puntuación</option>
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" @selected(old('puntuacion', $comentario->puntuacion) == $i)>
                                {{ $i }} {{ Str::plural('estrella', $i) }}
                            </option>
                        @endfor
                    </select>
                    @error('puntuacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                {{-- Opinion ckeditor --}}
                <div class="mb-3">
                    <label class="form-label">
                        Opinión del usuario
                        <span class="text-muted small d-block">
                            Puedes corregir lenguaje inapropiado, faltas ortográficas o ajustar el texto
                            para cumplir las normas de la plataforma.
                        </span>
                    </label>

                    <textarea id="opinion_admin" {{-- ID para CKEditor --}} name="opinion" rows="5" style="resize: none;"
                        class="form-control @error('opinion') is-invalid @enderror">{!! old('opinion', $comentario->opinion) !!}</textarea>

                    @error('opinion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Inicializar CKEditor sobre este textarea --}}
                <x-ckeditor.ckeditor_descripcion for="opinion_admin" />


                <div class="alert alert-info small">
                    Vas a modificar el comentario de un usuario.
                    <br>
                    Al guardar los cambios, le enviaremos un correo avisándole de que su comentario ha sido
                    ajustado para cumplir las normas de uso de ReformUp.
                    <br>
                    El estado (<strong>{{ $comentario->estado }}</strong>) y la visibilidad
                    (<strong>{{ $comentario->visible ? 'visible' : 'oculto' }}</strong>)
                    se siguen gestionando desde el panel de administración (switch de publicar / botón de rechazar).
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.comentarios') }}" class="btn btn-outline-secondary">
                        Volver al listado
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Guardar cambios
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection
