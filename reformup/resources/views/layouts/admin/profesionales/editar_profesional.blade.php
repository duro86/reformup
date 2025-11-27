@extends('layouts.main')
@section('title', 'Editar Profesional - ReformUp')

@section('content')

    <x-navbar />

    {{-- Botón volver --}}
    <div class="d-flex justify-content-start mx-5 my-1">
        {{-- Volver a listado profesionales --}}
        <a href="{{ route('admin.profesionales') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-2">

            <i class="bi bi-arrow-left"></i> Volver a la lista Profesionales
        </a>
    </div>

    {{-- Contenedor principal --}}
    <div class="container my-5">
        <div class="row g-0 shadow rounded overflow-hidden">

            {{-- Panel izquierdo decorativo --}}
            <div class="col-lg-5 d-none d-lg-block" style="background:#E9F5DB;">
                <div class="h-100 p-5">
                    <h2 class="mb-3 text-success">Editar profesional</h2>
                    <p class="text-muted">
                        Modifica los datos del perfil profesional. Recuerda guardar los cambios.
                    </p>

                    {{-- Info básica del usuario asociado (si existe) --}}
                    @if ($perfil->user)
                        <div class="mt-3 p-3 bg-white rounded shadow-sm small">
                            <p class="mb-1"><strong>Usuario asociado:</strong></p>
                            <p class="mb-0">
                                {{ $perfil->user->nombre }} {{ $perfil->user->apellidos }}<br>
                                <span class="text-muted">{{ $perfil->user->email }}</span>
                            </p>
                            <p class="mb-0 mt-2">
                                <a href="{{ route('admin.usuarios.editar', $perfil->user->id) }}"
                                    class="btn btn-outline-primary btn-sm">
                                    Editar usuario
                                </a>
                            </p>
                        </div>
                    @else
                        <div class="mt-3 p-3 bg-white rounded shadow-sm small text-danger">
                            Este perfil profesional no tiene un usuario asociado.
                        </div>
                    @endif

                    {{-- Imagen panel izquierdo --}}
                    <div class="text-center mt-4">
                        <img src="{{ asset('img/User/panel_registro/panel_registro_user.png') }}" alt="Reformas"
                            class="img-fluid rounded mx-auto d-block" style="max-width:85%; height:auto;">
                    </div>
                </div>
            </div>

            {{-- Formulario --}}
            <div class="col-lg-7 bg-white">
                <div class="p-4 p-lg-5">
                    <h1 class="h4 mb-4">
                        <i class="bi bi-briefcase me-2"></i>
                        Editar perfil profesional
                    </h1>

                    {{-- Errores --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            Revisa los campos marcados en rojo.
                        </div>
                    @endif

                    {{-- Formulario editar profesional --}}
                    <form method="POST"
                        action="{{ route('admin.profesionales.actualizar', [$perfil->id, 'page' => request('page', 1)]) }}">
                        @csrf
                        @method('PUT')

                        {{-- Empresa + CIF --}}
                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="form-label">Nombre de la empresa<span class="text-danger">*</span></label>
                                <input type="text" name="empresa" value="{{ old('empresa', $perfil->empresa) }}"
                                    class="form-control @error('empresa') is-invalid @enderror">
                                @error('empresa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-5 mb-3">
                                <label class="form-label">CIF<span class="text-danger">*</span></label>
                                <input type="text" name="cif" value="{{ old('cif', $perfil->cif) }}"
                                    class="form-control @error('cif') is-invalid @enderror">
                                @error('cif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Email y teléfono empresa --}}
                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="form-label">Email empresa<span class="text-danger">*</span></label>
                                <input type="email" name="email_empresa"
                                    value="{{ old('email_empresa', $perfil->email_empresa) }}"
                                    class="form-control @error('email_empresa') is-invalid @enderror">
                                @error('email_empresa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-5 mb-3">
                                <label class="form-label">Teléfono empresa<span class="text-danger">*</span></label>
                                <input type="text" name="telefono_empresa"
                                    value="{{ old('telefono_empresa', $perfil->telefono_empresa) }}"
                                    class="form-control @error('telefono_empresa') is-invalid @enderror">
                                @error('telefono_empresa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Ciudad / Provincia --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" value="{{ old('ciudad', $perfil->ciudad) }}"
                                    class="form-control @error('ciudad') is-invalid @enderror">
                                @error('ciudad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Provincia</label>
                                <input type="text" name="provincia" value="{{ old('provincia', $perfil->provincia) }}"
                                    class="form-control @error('provincia') is-invalid @enderror">
                                @error('provincia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Dirección empresa --}}
                        <div class="mb-3">
                            <label class="form-label">Dirección empresa</label>
                            <input type="text" name="dir_empresa" value="{{ old('dir_empresa', $perfil->dir_empresa) }}"
                                class="form-control @error('dir_empresa') is-invalid @enderror">
                            @error('dir_empresa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Web y Bio --}}
                        <div class="mb-3">
                            <label class="form-label">Web</label>
                            <input type="url" name="web" value="{{ old('web', $perfil->web) }}"
                                class="form-control @error('web') is-invalid @enderror">
                            @error('web')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bio / Descripción</label>
                            <textarea name="bio" rows="3" style="resize: none;" class="form-control @error('bio') is-invalid @enderror">{{ old('bio', $perfil->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Puntuación / Trabajos realizados --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Puntuación media</label>
                                <input type="number" step="0.1" min="0" max="5"
                                    name="puntuacion_media"
                                    value="{{ old('puntuacion_media', $perfil->puntuacion_media) }}"
                                    class="form-control @error('puntuacion_media') is-invalid @enderror">
                                @error('puntuacion_media')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trabajos realizados</label>
                                <input type="number" min="0" name="trabajos_realizados"
                                    value="{{ old('trabajos_realizados', $perfil->trabajos_realizados) }}"
                                    class="form-control @error('trabajos_realizados') is-invalid @enderror">
                                @error('trabajos_realizados')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Visible --}}
                        <div class="mb-3">
                            <label class="form-label d-block">Visible en la plataforma</label>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="visible" id="visible_si"
                                    value="1" {{ old('visible', $perfil->visible) ? 'checked' : '' }}>
                                <label class="form-check-label" for="visible_si">
                                    Sí
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="visible" id="visible_no"
                                    value="0" {{ old('visible', $perfil->visible) == 0 ? 'checked' : '' }}>
                                <label class="form-check-label" for="visible_no">
                                    No
                                </label>
                            </div>

                            @error('visible')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Avatar empresa / logo profesional --}}
                        <div class="mb-3">
                            <label class="form-label">Avatar / Logo (imagen)</label>

                            <div class="d-flex align-items-center mb-2">
                                @if ($perfil->avatar)
                                    <img src="{{ Storage::url($perfil->avatar) }}" alt="avatar profesional"
                                        class="rounded-circle me-3" style="width:40px;height:40px;object-fit:cover">
                                @else
                                    <i class="bi bi-building me-2" style="font-size: 2rem;"></i>
                                @endif
                                <span class="text-muted small">Logo / avatar actual</span>
                            </div>

                            <input type="file" name="avatar" accept="image/*"
                                class="form-control @error('avatar') is-invalid @enderror">
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Si no seleccionas nada, se mantendrá la imagen actual.
                            </small>
                        </div>

                        {{-- Oficios --}}
                        <div class="mb-3">
                            <label class="form-label">Oficios (mínimo 1) <span class="text-danger">*</span></label>
                            <div class="card shadow-sm p-3" style="max-height: 280px; overflow-y:auto;">
                                @foreach ($oficios as $oficio)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input @error('oficios') is-invalid @enderror"
                                            type="checkbox" name="oficios[]" value="{{ $oficio->id }}"
                                            id="oficio{{ $oficio->id }}"
                                            {{ in_array($oficio->id, old('oficios', $oficiosSeleccionados)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="oficio{{ $oficio->id }}">
                                            {{ ucfirst(str_replace('_', ' ', $oficio->nombre)) }}
                                        </label>
                                    </div>
                                @endforeach

                                @error('oficios')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="d-flex gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">
                                Guardar cambios
                            </button>

                            <a href="{{ route('admin.profesionales', ['page' => request('page', 1)]) }}"
                                class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-footer />
@endsection

<x-alertas_sweet />
