@props(['usuario'])

<h4 class="mb-2">
    <i class="bi bi-person-bounding-box me-1"></i>
    Datos de usuario
</h4>
{{-- Nombre + Apellidos + Email --}}
<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Nombre *</label>
        <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}"
            class="form-control @error('nombre') is-invalid @enderror">
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Apellidos *</label>
        <input type="text" name="apellidos" value="{{ old('apellidos', $usuario->apellidos) }}"
            class="form-control @error('apellidos') is-invalid @enderror">
        @error('apellidos')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Email *</label>
        <input type="email" name="email" value="{{ old('email', $usuario->email) }}"
            class="form-control @error('email') is-invalid @enderror">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>


{{-- Teléfono + Provincia + Municipio --}}
<div class="row">

    <div class="col-md-4 mb-3">
        <label class="form-label">Teléfono *</label>
        <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}"
            class="form-control @error('telefono') is-invalid @enderror">
        @error('telefono')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Provincia *</label>
        <select name="provincia_user" id="provincia_user"
            class="form-control @error('provincia_user') is-invalid @enderror">
            <option value="">Selecciona una provincia</option>
            <option value="Huelva" {{ old('provincia_user', $usuario->provincia) == 'Huelva' ? 'selected' : '' }}>Huelva
            </option>
            <option value="Sevilla" {{ old('provincia_user', $usuario->provincia) == 'Sevilla' ? 'selected' : '' }}>
                Sevilla</option>
        </select>
        @error('provincia_user')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Municipio</label>
        <select name="ciudad_user" id="ciudad_user" class="form-control @error('ciudad_user') is-invalid @enderror">
            <option value="">Selecciona primero una provincia</option>
        </select>
        @error('ciudad_user')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>


{{-- Contraseña --}}
<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">
            Contraseña actual
        </label>
        <input type="password" name="current_password"
            class="form-control @error('current_password') is-invalid @enderror">
        @error('current_password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">
            Solo si quieres cambiar la contraseña.
        </small>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">
            Nueva contraseña
        </label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Repite nueva contraseña</label>
        <input type="password" name="password_confirmation" class="form-control">
    </div>
</div>



{{-- Dirección + Código Postal --}}
<div class="row">
    <div class="col-md-8 mb-3">
        <label class="form-label">Dirección</label>
        <input type="text" name="direccion" value="{{ old('direccion', $usuario->direccion) }}"
            class="form-control @error('direccion') is-invalid @enderror">
        @error('direccion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Código postal</label>
        <input type="text" name="cp" value="{{ old('cp', $usuario->cp) }}"
            class="form-control @error('cp') is-invalid @enderror">
        @error('cp')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Avatar usuario --}}
<div class="mb-4">
    <label class="form-label">Avatar (imagen)</label>

    <div class="d-flex align-items-center mb-2">
        @if ($usuario->avatar)
            <img src="{{ Storage::url($usuario->avatar) }}" alt="avatar" class="rounded-circle me-3"
                style="width:40px;height:40px;object-fit:cover">
        @else
            <i class="bi bi-person-circle me-2" style="font-size: 2rem;"></i>
        @endif
        <span class="text-muted small">Avatar actual</span>
    </div>

    <input type="file" name="avatar" accept="image/*" class="form-control @error('avatar') is-invalid @enderror">
    @error('avatar')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">
        Si no seleccionas nada, se mantendrá el avatar actual.
    </small>
</div>
{{-- Editar usuario --}}
<x-ciudadProvincia.ciudades_provincias :oldProvincia="old('provincia', $usuario->provincia)" :oldCiudad="old('ciudad', $usuario->ciudad)" />
