@extends('layouts.main')
@section('title', 'Editar Usuario - ReformUp')

@section('content')

    <x-navbar />
    <div class="d-flex justify-content-start mx-5 my-1">
        <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Volver a la lista Usuarios
        </a>
    </div>
    <div class="container my-5">
        <div class="row g-0 shadow rounded overflow-hidden">

            {{-- Panel izquierdo decorativo (puedes dejar el mismo que en crear) --}}
            <div class="col-lg-5 d-none d-lg-block" style="background:#E9F5DB;">

                <div class="h-100 p-5">

                    <h2 class="mb-3 text-success">Editar usuario</h2>
                    <p class="text-muted">
                        Modifica los datos del usuario. Recuerda guardar los cambios.
                    </p>

                    {{-- Bloque perfil profesional asociado (solo en escritorio) --}}
                    <div class="mt-3">
                        @if ($usuario->perfil_Profesional)
                            <div class="p-3 bg-white rounded shadow-sm small">
                                <p class="mb-1"><strong>Perfil profesional asociado:</strong></p>

                                <p class="mb-0">
                                    {{ $usuario->perfil_Profesional->empresa ?? 'Sin nombre de empresa' }}<br>
                                    <span class="text-muted">
                                        {{ $usuario->perfil_Profesional->ciudad }}
                                        @if ($usuario->perfil_Profesional->provincia)
                                            ({{ $usuario->perfil_Profesional->provincia }})
                                        @endif
                                    </span>
                                </p>

                                <p class="mb-0 mt-2">
                                    <a href="{{ route('admin.profesionales.editar', $usuario->perfil_Profesional->id) }}"
                                        class="btn btn-outline-primary btn-sm">
                                        Editar perfil profesional
                                    </a>
                                </p>
                            </div>
                        @else
                            <div class="p-3 bg-white rounded shadow-sm small text-muted">
                                Este usuario no tiene un perfil profesional asociado.
                            </div>
                        @endif

                    </div>

                    <div class="text-center mt-3">
                        <img src="{{ asset('img/User/panel_registro/panel_registro_user.png') }}" alt="Reformas"
                            class="img-fluid rounded mx-auto d-block" style="max-width:85%; height:auto;">
                    </div>
                </div>
            </div>

            {{-- CONTENEDOR PRINCIPAL --}}
            <div class="col-lg-7 bg-white">
                <div class="p-4 p-lg-5">
                    {{-- Bloque perfil profesional asociado (solo en móvil / tablets pequeñas) --}}
                    <div class="mb-3 d-block d-lg-none">
                        @if ($usuario->perfil_Profesional)
                            <div class="p-3 bg-white rounded shadow-sm small">
                                <p class="mb-1"><strong>Perfil profesional asociado:</strong></p>

                                <p class="mb-0">
                                    {{ $usuario->perfil_Profesional->empresa ?? 'Sin nombre de empresa' }}<br>
                                    <span class="text-muted">
                                        {{ $usuario->perfil_Profesional->ciudad }}
                                        @if ($usuario->perfil_Profesional->provincia)
                                            ({{ $usuario->perfil_Profesional->provincia }})
                                        @endif
                                    </span>
                                </p>

                                <p class="mb-0 mt-2">
                                    <a href="{{ route('admin.profesionales.editar', $usuario->perfil_Profesional->id) }}"
                                        class="btn btn-outline-primary btn-sm">
                                        Editar perfil profesional
                                    </a>
                                </p>
                            </div>
                        @else
                            <div class="p-3 bg-white rounded shadow-sm small text-muted">
                                Este usuario no tiene un perfil profesional asociado.
                            </div>
                        @endif
                    </div>
                    <h1 class="h4 mb-4">
                        <i class="bi-person-bounding-box me-2"></i>
                        Editar cuenta <span class="text-primary">(Cliente)</span>
                    </h1>


                    @if ($errors->any())
                        <div class="alert alert-danger">
                            Revisa los campos marcados en rojo.
                        </div>
                    @endif

                    {{-- FORMULARIO ACTUALIZAR USUARIO --}}
                    <form method="POST" action="{{ route('admin.usuarios.actualizar', $usuario->id) }}" novalidate
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')


                        {{-- mantener la página actual de la paginación --}}
                        <input type="hidden" name="page" value="{{ request('page', 1) }}">

                        {{-- Nombre y Apellidos --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre<span class="text-danger">*</span></label>
                                <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}"
                                    class="form-control @error('nombre') is-invalid @enderror">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Apellidos<span class="text-danger">*</span></label>
                                <input type="text" name="apellidos" value="{{ old('apellidos', $usuario->apellidos) }}"
                                    class="form-control @error('apellidos') is-invalid @enderror">
                                @error('apellidos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label">Email<span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $usuario->email) }}"
                                class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Password --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Contraseña
                                    <small class="text-muted">(en blanco si no quieres cambiarla)</small>
                                </label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Repite contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>

                        {{-- Telefono --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono<span class="text-danger">*</span></label>
                                <input type="text" name="telefono" placeholder="612345678"
                                    value="{{ old('telefono', $usuario->telefono) }}"
                                    class="form-control @error('telefono') is-invalid @enderror">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Provincia --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Provincia<span class="text-danger">*</span></label>
                                <select name="provincia" id="provincia"
                                    class="form-control @error('provincia') is-invalid @enderror">
                                    <option value="">Selecciona una provincia</option>
                                    <option value="Huelva"
                                        {{ old('provincia', $usuario->provincia) == 'Huelva' ? 'selected' : '' }}>Huelva
                                    </option>
                                    <option value="Sevilla"
                                        {{ old('provincia', $usuario->provincia) == 'Sevilla' ? 'selected' : '' }}>Sevilla
                                    </option>
                                </select>
                                @error('provincia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Ciudad / Municipio --}}
                        <div class="row">
                            {{-- Ciudad / Municipio --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Municipio</label>
                                <select name="ciudad" id="ciudad"
                                    class="form-control @error('ciudad') is-invalid @enderror">
                                    <option value="">Selecciona primero una provincia</option>
                                </select>
                                @error('ciudad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CP --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Código postal</label>
                                <input type="text" name="cp" placeholder="21004"
                                    value="{{ old('cp', $usuario->cp) }}"
                                    class="form-control @error('cp') is-invalid @enderror" maxlength="5">
                                @error('cp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Direccion --}}
                        <div class="mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion" placeholder="Avd/Cabezo de la Joya 3, escalera 3, 4ºA"
                                value="{{ old('direccion', $usuario->direccion) }}"
                                class="form-control @error('direccion') is-invalid @enderror">
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Avatar actual + subir nuevo --}}
                        <div class="mb-3">
                            <label class="form-label">Avatar (imagen)</label>

                            <div class="d-flex align-items-center mb-2">
                                @if ($usuario->avatar)
                                    <img src="{{ Storage::url($usuario->avatar) }}" alt="avatar"
                                        class="rounded-circle me-3" style="width:40px;height:40px;object-fit:cover">
                                @else
                                    <i class="bi bi-person-circle me-2" style="font-size: 2rem;"></i>
                                @endif
                                <span class="text-muted small">Avatar actual</span>
                            </div>

                            <input type="file" name="avatar" accept="image/*"
                                class="form-control @error('avatar') is-invalid @enderror">
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Si no seleccionas nada, se mantendrá el avatar actual.
                            </small>
                        </div>

                        {{-- Rol --}}
                        @foreach ($allRoles as $roleName)
                            @continue($roleName === 'usuario')

                            <div class="form-check form-check-inline">
                                <input class="form-check-input rol-input" type="checkbox" name="roles[]"
                                    id="role_{{ $roleName }}" value="{{ $roleName }}"
                                    {{ in_array($roleName, old('roles', $currentRoles)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $roleName }}">
                                    {{ ucfirst($roleName) }}
                                </label>
                            </div>
                        @endforeach

                        {{-- Error principal del campo roles --}}
                        @error('roles')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                        {{-- Rol --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                Guardar cambios
                            </button>
                            <a href="{{ route('admin.usuarios', ['page' => request('page', 1)]) }}"
                                class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <x-ciudadProvincia.ciudades_provincias :oldProvincia="old('provincia')" :oldCiudad="old('ciudad')" />

    <x-footer />
@endsection
<x-alertas_sweet />
