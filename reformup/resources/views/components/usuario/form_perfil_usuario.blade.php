@props(['usuario'])

<h2 class="h5 mb-3">
    <i class="bi bi-person-bounding-box me-1"></i>
    Datos de usuario
</h2>

{{-- Nombre y apellidos --}}
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


{{-- Teléfono y ciudad --}}
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Teléfono<span class="text-danger">*</span></label>
        <input type="text" name="telefono" placeholder="612345678" value="{{ old('telefono', $usuario->telefono) }}"
            class="form-control @error('telefono') is-invalid @enderror">
        @error('telefono')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Provincia --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">Provincia<span class="text-danger">*</span></label>
        <select name="provincia" id="provincia" class="form-control @error('provincia') is-invalid @enderror">
            <option value="">Selecciona una Provincia</option>
            <option value="Huelva" {{ old('provincia', $usuario->provincia) == 'Huelva' ? 'selected' : '' }}>Huelva
            </option>
            <option value="Sevilla" {{ old('provincia', $usuario->provincia) == 'Sevilla' ? 'selected' : '' }}>Sevilla
            </option>
        </select>
        @error('provincia')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Provincia y código postal --}}
<div class="row">
    {{-- Ciudad / Municipio --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">Municipio</label>
        <select name="ciudad" id="ciudad" class="form-control @error('ciudad') is-invalid @enderror">
            <option value="">Selecciona primero un Municipio</option>
            {{-- Aquí JS meterá las opciones --}}
        </select>
        @error('ciudad')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Código postal</label>
        <input type="text" name="cp" placeholder="21004" value="{{ old('cp', $usuario->cp) }}"
            class="form-control @error('cp') is-invalid @enderror" maxlength="5">
        @error('cp')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Dirección --}}
<div class="mb-3">
    <label class="form-label">Dirección</label>
    <input type="text" name="direccion" placeholder="Avd/Cabezo de la Joya 3, escalera 3, 4ºA"
        value="{{ old('direccion', $usuario->direccion) }}"
        class="form-control @error('direccion') is-invalid @enderror">
    @error('direccion')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
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

    <input type="file" name="avatar" accept="image/*"
        class="form-control @error('avatar') is-invalid @enderror">
    @error('avatar')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">
        Si no seleccionas nada, se mantendrá el avatar actual.
    </small>
</div>
{{-- Editar usuario --}}
<x-ciudadProvincia.ciudades_provincias
    :oldProvincia="old('provincia', $usuario->provincia)"
    :oldCiudad="old('ciudad', $usuario->ciudad)"
/>
