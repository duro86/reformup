@extends('layouts.main')

@section('title', 'Enviar presupuesto - ReformUp')

@section('content')

    <x-navbar />
    <x-profesional.profesional_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">

            <a href="{{ route('profesional.solicitudes.index') }}" class="btn btn-secondary btn-sm mb-3">
                Volver a solicitudes
            </a>

            <h1 class="h4 mb-3">
                Enviar presupuesto
            </h1>

            {{-- Info rápida de la solicitud --}}
            <div class="card mb-4">
                <div class="card-body">

                    <h2 class="h5 mb-2">{{ $solicitud->titulo }}</h2>
                    <p class="mb-1 text-muted">
                        Cliente: {{ $solicitud->cliente->nombre ?? 'Cliente' }}
                        @if ($solicitud->cliente && $solicitud->cliente->apellidos)
                            {{ $solicitud->cliente->apellidos }}
                        @endif
                    </p>
                    <p class="mb-1 text-muted">
                        Ubicación: {{ $solicitud->ciudad }}
                        {{ $solicitud->provincia ? ' - ' . $solicitud->provincia : '' }}
                    </p>
                    <p class="mb-0">
                        <strong>Descripción:</strong><br>
                        {{ $solicitud->descripcion }}
                    </p>
                </div>
            </div>

            {{-- Formulario presupuesto --}}
            <div class="card">
                <div class="card-body">

                    {{-- Mensaje de error general (el primero) --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST"
                        action="{{ route('profesional.presupuestos.guardar_desde_solicitud', $solicitud) }}"
                        enctype="multipart/form-data">
                        @csrf

                        {{-- MODO DE PRESUPUESTO --}}
                        <div class="mb-3">
                            <h5 class="bg-secondary mx-1"
                                style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 0.25rem;">
                                Debes elegir el tipo de Presupuesto que vas a enviar<i
                                    class="bi bi-arrow-down-circle mx-2"></i>
                            </h5></br>
                            <label class="form-label d-block">Modo de presupuesto</label>
                            @php
                                $modo = old('modo', 'lineas');
                            @endphp

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="modo" id="modo_lineas"
                                    value="lineas" {{ $modo === 'lineas' ? 'checked' : '' }}>
                                <label class="form-check-label" for="modo_lineas">
                                    Crear desde líneas (materiales, unidades, etc.)
                                </label>
                            </div>
                            <i class="bi bi-card-checklist mr-3 text-primary fs-5 me-4"></i>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="modo" id="modo_archivo"
                                    value="archivo" {{ $modo === 'archivo' ? 'checked' : '' }}>
                                <label class="form-check-label" for="modo_archivo">
                                    Adjuntar presupuesto ya generado (PDF / Word)
                                </label>
                            </div>
                            <i class="bi bi-file-earmark-pdf text-secondary fs-5"></i>
                        </div>

                        {{-- LÍNEAS (concepto / cantidad / precio) --}}
                        <h2 class="h6 mb-3">Líneas de presupuesto</h2>

                        @php
                            $hasConceptoError = $errors->has('concepto') || $errors->has('concepto.*');
                            $hasCantidadError = $errors->has('cantidad') || $errors->has('cantidad.*');
                            $hasPrecioError = $errors->has('precio_unitario') || $errors->has('precio_unitario.*');

                            $oldConceptos = old('concepto', []);
                            $oldCantidades = old('cantidad', []);
                            $oldPrecios = old('precio_unitario', []);

                            // Si no hay old(), pintamos una única línea vacía
                            $numLineas = max(1, count($oldConceptos));
                        @endphp

                        <div id="lineas-presupuesto">

                            @for ($i = 0; $i < $numLineas; $i++)
                                <div class="row g-2 mb-2 linea-item">
                                    <div class="col-md-6">
                                        <input type="text" name="concepto[]" value="{{ $oldConceptos[$i] ?? '' }}"
                                            class="form-control {{ $hasConceptoError ? 'is-invalid' : '' }}"
                                            placeholder="Concepto (ej: Mano de obra, Materiales, etc.)">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="cantidad[]" step="1" min="1"
                                            value="{{ $oldCantidades[$i] ?? '' }}"
                                            class="form-control {{ $hasCantidadError ? 'is-invalid' : '' }}"
                                            placeholder="Cant.">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="precio_unitario[]" step="0.01" min="0"
                                            value="{{ $oldPrecios[$i] ?? '' }}"
                                            class="form-control {{ $hasPrecioError ? 'is-invalid' : '' }}"
                                            placeholder="Precio €/u">
                                    </div>
                                    <div class="col-md-1 d-flex justify-content-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-linea"
                                            title="Eliminar línea">
                                            &times;
                                        </button>
                                    </div>
                                </div>
                            @endfor

                        </div>

                        {{-- Mensajes de error de las líneas --}}
                        @if ($hasConceptoError || $hasCantidadError || $hasPrecioError)
                            <div class="invalid-feedback d-block mb-2">
                                {{ $errors->first('concepto') ??
                                    ($errors->first('concepto.*') ??
                                        ($errors->first('cantidad') ??
                                            ($errors->first('cantidad.*') ??
                                                ($errors->first('precio_unitario') ?? $errors->first('precio_unitario.*'))))) }}
                            </div>
                        @endif

                        {{-- Botón añadir línea --}}
                        <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="btn-add-linea">
                            Añadir línea
                        </button>

                        <hr>

                        {{-- TOTAL (solo obligatorio en modo archivo) --}}
                        <div class="mb-3">
                            <label class="form-label">
                                Importe total del presupuesto
                                <span class="text-muted small">(solo obligatorio si adjuntas un documento)</span>
                            </label>
                            <input type="number" name="total" step="0.01" min="0" value="{{ old('total') }}"
                                class="form-control @error('total') is-invalid @enderror" placeholder="Ej: 1200.00">
                            @error('total')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Si adjuntas un PDF/Word con el presupuesto, usaremos este total.
                                Si no adjuntas nada, calcularemos el total a partir de las líneas de arriba.
                            </small>
                        </div>

                        {{-- Notas / detalle --}}
                        <div class="mb-3">
                            <label class="form-label">Notas para el cliente</label>
                            <textarea name="notas" rows="4" class="form-control @error('notas') is-invalid @enderror" style="resize:none;">{{ old('notas') }}</textarea>
                            @error('notas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Puedes explicar qué incluye el presupuesto, plazos, condiciones, etc.
                            </small>
                        </div>

                        {{-- Adjuntar documento --}}
                        <div class="mb-3">
                            <label class="form-label">Adjuntar presupuesto (PDF / Word)</label>
                            <input type="file" name="docu_pdf" accept=".pdf,.doc,.docx"
                                class="form-control @error('docu_pdf') is-invalid @enderror">
                            @error('docu_pdf')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Opcional, máximo 5 MB.
                            </small>
                        </div>

                        {{-- Botones Guardar --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('profesional.solicitudes.index') }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Guardar presupuesto
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection

{{-- JS para añadir / eliminar líneas --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const contenedor = document.getElementById('lineas-presupuesto');
            const btnAdd = document.getElementById('btn-add-linea');

            if (!contenedor || !btnAdd) return;

            // Añadir nueva línea clonando la primera
            btnAdd.addEventListener('click', () => {
                const firstRow = contenedor.querySelector('.linea-item');
                if (!firstRow) return;

                const row = firstRow.cloneNode(true);

                // Limpiar inputs
                row.querySelectorAll('input').forEach(input => {
                    input.value = '';
                    input.classList.remove('is-invalid');
                });

                contenedor.appendChild(row);
            });

            // Eliminar línea con delegación de eventos
            contenedor.addEventListener('click', (e) => {
                const btn = e.target.closest('.btn-remove-linea');
                if (!btn) return;

                const filas = contenedor.querySelectorAll('.linea-item');
                const fila = btn.closest('.linea-item');

                if (!fila) return;

                if (filas.length > 1) {
                    // Si hay más de 1, se elimina la fila
                    fila.remove();
                } else {
                    // Si es la única, solo limpiamos sus campos (no la quitamos)
                    fila.querySelectorAll('input').forEach(input => {
                        input.value = '';
                        input.classList.remove('is-invalid');
                    });
                }
            });
        });
    </script>
@endpush
