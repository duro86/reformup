@props([
    'perfil_profesional' => null,
    'oficios' => [],
    'oficiosSeleccionados' => [],
])

<div class="row g-0 shadow rounded bg-white">
    <div class="col-12 p-4 p-lg-5">

        {{-- Título --}}
        <h2 class="h5 mb-3">
            <i class="bi bi-briefcase me-1"></i>
            Datos de profesional
        </h2>

        @if (!$perfil_profesional)
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
                    <input type="text" name="empresa" value="{{ old('empresa', $perfil_profesional->empresa) }}"
                        class="form-control @error('empresa') is-invalid @enderror">
                    @error('empresa')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-5 mb-3">
                    <label class="form-label">CIF<span class="text-danger">*</span></label>
                    <input type="text" name="cif" value="{{ old('cif', $perfil_profesional->cif) }}"
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
                        value="{{ old('email_empresa', $perfil_profesional->email_empresa) }}"
                        class="form-control @error('email_empresa') is-invalid @enderror">
                    @error('email_empresa')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-5 mb-3">
                    <label class="form-label">Teléfono empresa<span class="text-danger">*</span></label>
                    <input type="text" name="telefono_empresa"
                        value="{{ old('telefono_empresa', $perfil_profesional->telefono_empresa) }}"
                        class="form-control @error('telefono_empresa') is-invalid @enderror">
                    @error('telefono_empresa')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Ciudad / Provincia / CP --}}
            <div class="row">
                {{-- Provincia --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Provincia<span class="text-danger">*</span></label>
                    <select name="provincia" id="provincia"
                        class="form-control @error('provincia') is-invalid @enderror">
                        <option value="">Selecciona una Provincia</option>
                        <option value="Huelva"
                            {{ old('provincia', $perfil_profesional->provincia) == 'Huelva' ? 'selected' : '' }}>
                            Huelva
                        </option>
                        <option value="Sevilla"
                            {{ old('provincia', $perfil_profesional->provincia) == 'Sevilla' ? 'selected' : '' }}>
                            Sevilla
                        </option>
                    </select>
                    @error('provincia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ciudad / Municipio --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Municipio</label>
                    <select name="ciudad" id="ciudad" class="form-control @error('ciudad') is-invalid @enderror">
                        <option value="">Selecciona primero un Municipio</option>
                        {{-- JS mete las opciones --}}
                    </select>
                    @error('ciudad')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Código postal</label>
                    <input type="text" name="cp_empresa"
                        value="{{ old('cp_empresa', $perfil_profesional->cp_empresa ?? '') }}"
                        class="form-control @error('cp_empresa') is-invalid @enderror">
                    @error('cp_empresa')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Dirección empresa</label>
                <input type="text" name="direccion_empresa"
                    value="{{ old('direccion_empresa', $perfil_profesional->dir_empresa) }}"
                    class="form-control @error('direccion_empresa') is-invalid @enderror">
                @error('direccion_empresa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Web y Bio --}}
            <div class="mb-3">
                <label class="form-label">Web</label>
                <input type="url" name="web" value="{{ old('web', $perfil_profesional->web) }}"
                    class="form-control @error('web') is-invalid @enderror">
                @error('web')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Bio / Descripción</label>
                <textarea name="bio" rows="3" style="resize: none;" class="form-control @error('bio') is-invalid @enderror">{{ old('bio', $perfil_profesional->bio) }}</textarea>
                @error('bio')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Avatar profesional --}}
            <div class="mb-3">
                <label class="form-label">Avatar / Logo (imagen)</label>

                <div class="d-flex align-items-center mb-2">
                    @if ($perfil_profesional->avatar)
                        <img src="{{ Storage::url($perfil_profesional->avatar) }}" alt="avatar profesional"
                            class="rounded-circle me-3" style="width:40px;height:40px;object-fit:cover">
                    @else
                        <i class="bi bi-building me-2" style="font-size: 2rem;"></i>
                    @endif
                    <span class="text-muted small">Logo / avatar actual</span>
                </div>

                <input type="file" name="avatar_profesional" accept="image/*"
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
                            <input class="form-check-input @error('oficios') is-invalid @enderror" type="checkbox"
                                name="oficios[]" value="{{ $oficio->id }}" id="oficio{{ $oficio->id }}"
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

            {{-- JS de provincias/ciudades --}}
            <x-ciudadProvincia.ciudades_provincias :oldProvincia="old('provincia', $perfil_profesional->provincia)" :oldCiudad="old('ciudad', $perfil_profesional->ciudad ?? '')" />
        @endif

    </div>
</div>
