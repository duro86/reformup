{{-- resources/views/profesional/perfil.blade.php --}}

@extends('layouts.main')

@section('title', 'Perfil profesional - ReformUp')

@section('content')

    <x-navbar />

    <x-profesional.profesional_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">
            <div class="d-grid d-md-inline-block">
            <a href="{{ route('profesional.dashboard') }}"
                class="btn btn-secondary d-inline-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-arrow-left"></i>
                <span>Volver al panel principal</span>
            </a>
            </div>
        </div>

            <div class="d-flex justify-content-center align-items-center mb-3 text-center">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2 ">
                    <i class="bi bi-building"></i> Mi perfil profesional
                </h1>

            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    Revisa los campos marcados en rojo.
                </div>
            @endif

            <div class="row g-0 shadow rounded bg-white">
                <div class="col-12 p-4 p-lg-5">

                    <form method="POST"
                          action="{{ route('profesional.perfil.actualizar') }}"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @php
                            $perfilProfesional   = $perfil;
                            $oficiosSeleccionados = $oficiosSeleccionados ?? [];
                        @endphp

                        {{-- === A PARTIR DE AQUÍ, TU FORMULARIO QUE YA TENÍAS === --}}

                        <h2 class="h5 mb-3">
                            <i class="bi bi-briefcase me-1"></i>
                            Datos de profesional
                        </h2>

                        @if (!$perfilProfesional)
                            <div class="alert alert-warning small">
                                Tienes rol profesional, pero aún no tienes perfil profesional creado.
                            </div>
                            <a href="{{ route('registrar.profesional.opciones') }}" class="btn btn-sm btn-success">
                                Crear perfil profesional
                            </a>
                        @else

                            {{-- Empresa + CIF --}}
                            <div class="row">
                                <div class="col-md-7 mb-3">
                                    <label class="form-label">Nombre de la empresa<span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="empresa"
                                           value="{{ old('empresa', $perfilProfesional->empresa) }}"
                                           class="form-control @error('empresa') is-invalid @enderror">
                                    @error('empresa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-5 mb-3">
                                    <label class="form-label">CIF<span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="cif"
                                           value="{{ old('cif', $perfilProfesional->cif) }}"
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
                                    <input type="email"
                                           name="email_empresa"
                                           value="{{ old('email_empresa', $perfilProfesional->email_empresa) }}"
                                           class="form-control @error('email_empresa') is-invalid @enderror">
                                    @error('email_empresa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-5 mb-3">
                                    <label class="form-label">Teléfono empresa<span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="telefono_empresa"
                                           value="{{ old('telefono_empresa', $perfilProfesional->telefono_empresa) }}"
                                           class="form-control @error('telefono_empresa') is-invalid @enderror">
                                    @error('telefono_empresa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Ciudad / Provincia / Dirección --}}
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Ciudad</label>
                                    <input type="text"
                                           name="ciudad_empresa"
                                           value="{{ old('ciudad_empresa', $perfilProfesional->ciudad) }}"
                                           class="form-control @error('ciudad_empresa') is-invalid @enderror">
                                    @error('ciudad_empresa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Provincia</label>
                                    <input type="text"
                                           name="provincia_empresa"
                                           value="{{ old('provincia_empresa', $perfilProfesional->provincia) }}"
                                           class="form-control @error('provincia_empresa') is-invalid @enderror">
                                    @error('provincia_empresa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Código postal</label>
                                    <input type="text"
                                           name="cp_empresa"
                                           value="{{ old('cp_empresa') }}"
                                           class="form-control">
                                    {{-- si no tienes columna cp en la tabla, este valor solo será estético
                                         o lo puedes quitar directamente --}}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Dirección empresa</label>
                                <input type="text"
                                       name="direccion_empresa"
                                       value="{{ old('direccion_empresa', $perfilProfesional->dir_empresa) }}"
                                       class="form-control @error('direccion_empresa') is-invalid @enderror">
                                @error('direccion_empresa')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Web y Bio --}}
                            <div class="mb-3">
                                <label class="form-label">Web</label>
                                <input type="url"
                                       name="web"
                                       value="{{ old('web', $perfilProfesional->web) }}"
                                       class="form-control @error('web') is-invalid @enderror">
                                @error('web')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bio / Descripción</label>
                                <textarea name="bio"
                                          rows="3"
                                          style="resize: none;"
                                          class="form-control @error('bio') is-invalid @enderror">{{ old('bio', $perfilProfesional->bio) }}</textarea>
                                @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Avatar profesional --}}
                            <div class="mb-3">
                                <label class="form-label">Avatar / Logo (imagen)</label>

                                <div class="d-flex align-items-center mb-2">
                                    @if ($perfilProfesional->avatar)
                                        <img src="{{ Storage::url($perfilProfesional->avatar) }}"
                                             alt="avatar profesional"
                                             class="rounded-circle me-3"
                                             style="width:40px;height:40px;object-fit:cover">
                                    @else
                                        <i class="bi bi-building me-2" style="font-size: 2rem;"></i>
                                    @endif
                                    <span class="text-muted small">Logo / avatar actual</span>
                                </div>

                                <input type="file"
                                       name="avatar_profesional"
                                       accept="image/*"
                                       class="form-control @error('avatar_profesional') is-invalid @enderror">
                                @error('avatar_profesional')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Si no seleccionas nada, se mantendrá la imagen actual.
                                </small>
                            </div>

                            {{-- Oficios --}}
                            <div class="mb-3">
                                <label class="form-label">Oficios (mínimo 1)</label>
                                <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                    @foreach ($oficios as $oficio)
                                        <div class="form-check mb-1">
                                            <input class="form-check-input @error('oficios') is-invalid @enderror"
                                                   type="checkbox"
                                                   name="oficios[]"
                                                   value="{{ $oficio->id }}"
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
                            <div class="mt-4 d-flex flex-column flex-md-row gap-2">
                                <button type="submit" class="btn btn-primary">
                                    Guardar cambios
                                </button>
                                <a href="{{ route('profesional.dashboard') }}" class="btn btn-outline-secondary">
                                    Cancelar
                                </a>
                            </div>

                        @endif

                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
