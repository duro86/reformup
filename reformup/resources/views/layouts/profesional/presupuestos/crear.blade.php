@extends('layouts.main')

@section('title', 'Enviar presupuesto - ReformUp')

@section('content')

    <x-navbar />
    <x-profesional.profesional_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-2">

            <a href="{{ route('profesional.solicitudes.index') }}" class="btn btn-secondary btn-sm mb-3">
                Volver a solicitudes
            </a>

            <h1 class="h4 mb-3">Enviar presupuesto</h1>

            {{-- Info rápida de la solicitud --}}
            <div class="card mb-2">
                <div class="card-body">
                    <h2 class="h5 mb-2">{{ $solicitud->titulo }}</h2>

                    <p class="mb-1 text-muted">
                        Cliente: {{ $solicitud->cliente->nombre ?? 'Cliente' }}
                        {{ $solicitud->cliente?->apellidos }}
                    </p>

                    <p class="mb-1 text-muted">
                        Ubicación: {{ $solicitud->ciudad }}
                        {{ $solicitud->provincia ? ' - ' . $solicitud->provincia : '' }}
                    </p>

                    <p class="mb-0">
                        <strong>Descripción:</strong><br>
                        {!! $solicitud->descripcion !!}
                    </p>
                </div>
            </div>

            {{-- Formulario presupuesto --}}
            <div class="card">
                <div class="card-body">

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
                            <label class="form-label d-block fw-semibold">Modo de presupuesto</label>

                            @php $modo = old('modo', 'lineas'); @endphp

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="modo" id="modo_lineas"
                                    value="lineas" {{ $modo === 'lineas' ? 'checked' : '' }}>
                                <label class="form-check-label" for="modo_lineas">
                                    Crear desde líneas
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="modo" id="modo_archivo"
                                    value="archivo" {{ $modo === 'archivo' ? 'checked' : '' }}>
                                <label class="form-check-label" for="modo_archivo">
                                    Adjuntar PDF / Word
                                </label>
                            </div>
                        </div>

                        {{-- ===================== --}}
                        {{-- BLOQUE LÍNEAS --}}
                        {{-- ===================== --}}
                        <div id="bloque-lineas">

                            <h2 class="h6 mb-3">Líneas de presupuesto</h2>

                            @php
                                $oldConceptos  = old('concepto', []);
                                $oldCantidades = old('cantidad', []);
                                $oldPrecios    = old('precio_unitario', []);
                                $numLineas = max(1, count($oldConceptos));
                            @endphp

                            <div id="lineas-presupuesto">
                                @for ($i = 0; $i < $numLineas; $i++)
                                    <div class="row g-2 mb-2 linea-item">
                                        <div class="col-md-6">
                                            <input type="text" name="concepto[]"
                                                value="{{ $oldConceptos[$i] ?? '' }}"
                                                class="form-control"
                                                placeholder="Concepto">
                                        </div>

                                        <div class="col-md-2">
                                            <input type="number" name="cantidad[]"
                                                value="{{ $oldCantidades[$i] ?? '' }}"
                                                class="form-control"
                                                placeholder="Cant.">
                                        </div>

                                        <div class="col-md-3">
                                            <input type="number" name="precio_unitario[]"
                                                value="{{ $oldPrecios[$i] ?? '' }}"
                                                class="form-control"
                                                placeholder="€/u">
                                        </div>

                                        <div class="col-md-1 d-flex justify-content-end">
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm btn-remove-linea">
                                                &times;
                                            </button>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <button type="button"
                                class="btn btn-sm btn-outline-primary mb-3"
                                id="btn-add-linea">
                                Añadir línea
                            </button>

                            <hr>
                        </div>

                        {{-- ===================== --}}
                        {{-- BLOQUE ARCHIVO --}}
                        {{-- ===================== --}}
                        <div id="bloque-archivo">

                            <div class="mb-3">
                                <label class="form-label">Importe total</label>
                                <input type="number"
                                    name="total"
                                    step="0.01"
                                    value="{{ old('total') }}"
                                    class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Adjuntar presupuesto (PDF / Word)</label>
                                <input type="file"
                                    name="docu_pdf"
                                    accept=".pdf,.doc,.docx"
                                    class="form-control">
                            </div>

                            <hr>
                        </div>

                        {{-- ===================== --}}
                        {{-- NOTAS (ÚNICO CAMPO) --}}
                        {{-- ===================== --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notas del presupuesto</label>

                            <textarea id="notas"
                                name="notas"
                                rows="4"
                                class="form-control @error('notas') is-invalid @enderror">
                                {{ old('notas') }}
                            </textarea>

                            @error('notas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <x-ckeditor.ckeditor_descripcion for="notas" />

                        {{-- BOTONES --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('profesional.solicitudes.index') }}"
                                class="btn btn-outline-secondary">
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

    {{-- TOGGLE DE BLOQUES --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const radios = document.querySelectorAll('input[name="modo"]');
                const bloqueLineas = document.getElementById('bloque-lineas');
                const bloqueArchivo = document.getElementById('bloque-archivo');

                function actualizarVista() {
                    const modo = document.querySelector('input[name="modo"]:checked')?.value || 'lineas';

                    if (modo === 'lineas') {
                        bloqueLineas.classList.remove('d-none');
                        bloqueArchivo.classList.add('d-none');
                    } else {
                        bloqueLineas.classList.add('d-none');
                        bloqueArchivo.classList.remove('d-none');
                    }
                }

                radios.forEach(r => r.addEventListener('change', actualizarVista));
                actualizarVista();
            });
        </script>
    @endpush

@endsection

<x-profesional.presupuestos.anadir_eliminar_lineas />
<x-alertas_sweet />
