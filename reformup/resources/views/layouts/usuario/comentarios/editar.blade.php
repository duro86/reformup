@extends('layouts.main')

@section('title', 'Editar comentario - ReformUp')

@section('content')
    <x-navbar />
    <x-usuario.usuario_sidebar />
    <x-usuario.user_bienvenido />

    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">

            <h1 class="h4 mb-3">
                Editar comentario
                @if (!empty($refCliente))
                    #{{ $refCliente }}
                @else
                    #{{ $comentario->id }}
                @endif
            </h1>


            @php
                $trabajo = $comentario->trabajo;
                $presu = $trabajo?->presupuesto;
                $solicitud = $presu?->solicitud;
            @endphp

            <div class="mb-3">
                <div class="small text-muted">
                    Trabajo:
                    @if ($solicitud?->titulo)
                        <strong>{{ $solicitud->titulo }}</strong>
                    @else
                        <strong>Trabajo #{{ $trabajo?->id }}</strong>
                    @endif
                </div>
                <div class="small text-muted">
                    Estado actual del comentario:
                    <strong>{{ ucfirst($comentario->estado) }}</strong>
                </div>
            </div>

            <form action="{{ route('usuario.comentarios.actualizar', $comentario) }}" enctype="multipart/form-data"
                method="POST" class="card p-3">
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

                {{-- CKEDITOR --}}
                <div class="mb-3">
                    <label class="form-label">Opinión (opcional)</label>
                    <textarea id="opinion" name="opinion" rows="5" class="form-control @error('opinion') is-invalid @enderror">{{ old('opinion', $comentario->opinion) }}</textarea>
                    @error('opinion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Inicializar CKEditor sobre #opinion --}}
                <x-ckeditor.ckeditor_descripcion for="opinion" />

                <div class="mb-3">
                    <label class="form-label">
                        <strong>Fotos del trabajo (opcional)</strong>
                        <span class="text-muted small d-block">
                            Puedes subir hasta 3 imágenes (JPG, PNG o WEBP, máx. 2MB cada una).
                        </span>
                    </label>

                    {{-- FOTOS YA GUARDADAS --}}
                    @if ($comentario->imagenes->isNotEmpty())
                        <div class="mb-2">
                            <span class="text-muted small d-block mb-1">
                                Fotos actuales asociadas a este comentario:
                            </span>

                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($comentario->imagenes as $img)
                                    @php
                                        $imgUrl = Storage::url($img->ruta);
                                    @endphp

                                    <div class="border rounded-3 p-1 bg-light" style="width: 110px;">
                                        <a href="#" data-bs-toggle="modal"
                                            data-bs-target="#modalImagenComentario{{ $img->id }}">
                                            <img src="{{ $imgUrl }}"
                                                alt="Foto del comentario #{{ $comentario->id }}"
                                                class="img-fluid rounded-2"
                                                style="height: 90px; object-fit: cover; width: 100%;">
                                        </a>
                                    </div>

                                    {{-- Modal para ver la imagen en grande --}}
                                    <div class="modal fade" id="modalImagenComentario{{ $img->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-body p-0">
                                                    <img src="{{ $imgUrl }}"
                                                        alt="Foto del comentario #{{ $comentario->id }}"
                                                        class="img-fluid w-100"
                                                        style="max-height: 80vh; object-fit: contain;">
                                                </div>
                                                <div class="modal-footer justify-content-between">
                                                    <span class="small text-muted">
                                                        Imagen asociada al comentario #{{ $comentario->id }}
                                                    </span>
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        data-bs-dismiss="modal">
                                                        Cerrar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <span class="text-muted small d-block mt-1">
                                Si no subes nuevas imágenes, estas se mantendrán.
                                Si subes nuevas, se sustituirán por las que elijas ahora.
                            </span>
                        </div>
                    @endif


                    {{-- INPUT PARA SUBIR NUEVAS IMÁGENES --}}
                    <input type="file" name="imagenes[]"
                        class="form-control @error('imagenes') is-invalid @enderror @error('imagenes.*') is-invalid @enderror"
                        multiple accept="image/jpeg,image/png,image/webp">

                    @error('imagenes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @error('imagenes.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="alert alert-info small">
                    Al guardar los cambios, tu comentario volverá a estado
                    <strong>pendiente</strong> hasta que el administrador lo revise.
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('usuario.comentarios.index') }}" class="btn btn-outline-secondary">
                        Volver a mis comentarios
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Guardar cambios
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection

<x-alertas_sweet />
