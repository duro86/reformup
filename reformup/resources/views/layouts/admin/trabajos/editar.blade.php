@extends('layouts.main')

@section('title', 'Editar trabajo - Admin - ReformUp')

@section('content')

    <x-navbar />
    <x-admin.admin_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        <x-admin.nav_movil active="trabajos" />

        <div class="container py-4">

            {{-- Cabecera --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-briefcase"></i>
                    Editar trabajo #{{ $trabajo->id }}
                </h1>

                <a href="{{ route('admin.trabajos') }}"
                    class="btn btn-secondary d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-arrow-left"></i>
                    Volver al listado de trabajos
                </a>
            </div>

            {{-- Errores globales --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    Revisa los campos marcados en rojo.
                </div>
            @endif

            {{-- Contexto: solicitud, cliente, profesional --}}
            @php
                $presupuesto = $trabajo->presupuesto;
                $solicitud = $presupuesto?->solicitud;
                $cliente = $solicitud?->cliente;
                $pro = $solicitud?->profesional;
            @endphp

            <div class="row g-0 shadow rounded bg-white mb-4">
                <div class="col-12 p-4">

                    <h5 class="mb-3">Información relacionada</h5>

                    {{-- Solicitud --}}
                    <div class="mb-2">
                        <strong>Solicitud:</strong>
                        @if ($solicitud)
                            #{{ $solicitud->id }} –
                            {{ $solicitud->titulo ?? 'Sin título' }}
                        @else
                            <span class="text-muted">Sin solicitud asociada</span>
                        @endif
                    </div>

                    {{-- Cliente --}}
                    <div class="mb-2">
                        <strong>Cliente:</strong>
                        @if ($cliente)
                            {{ $cliente->nombre ?? $cliente->name }}
                            {{ $cliente->apellidos ?? '' }}
                            @if ($cliente->email)
                                <span class="text-muted">
                                    – {{ $cliente->email }}
                                </span>
                            @endif
                        @else
                            <span class="text-muted">Sin cliente asociado</span>
                        @endif
                    </div>

                    {{-- Profesional --}}
                    <div class="mb-2">
                        <strong>Profesional:</strong>
                        @if ($pro)
                            {{ $pro->empresa }}
                            @if ($pro->email_empresa)
                                <span class="text-muted">
                                    – {{ $pro->email_empresa }}
                                </span>
                            @endif
                        @else
                            <span class="text-muted">Sin profesional asociado</span>
                        @endif
                    </div>

                    {{-- Presupuesto --}}
                    <div class="mb-0">
                        <strong>Presupuesto:</strong>
                        @if ($presupuesto)
                            #{{ $presupuesto->id }}
                            @if (!is_null($presupuesto->total))
                                – Total:
                                {{ number_format($presupuesto->total, 2, ',', '.') }} €
                            @endif
                        @else
                            <span class="text-muted">Sin presupuesto asociado</span>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Formulario edición trabajo --}}
            <div class="row g-0 shadow rounded bg-white">
                <div class="col-12 p-4 p-lg-5">

                    <form method="POST" action="{{ route('admin.trabajos.actualizar', $trabajo) }}">
                        @csrf
                        @method('PUT')

                        {{-- Estado --}}
                        <div class="mb-3">
                            <label class="form-label">Estado del trabajo<span class="text-danger">*</span></label>
                            @php
                                $estados = [
                                    'previsto' => 'Previsto',
                                    'en_curso' => 'En curso',
                                    'finalizado' => 'Finalizado',
                                    'cancelado' => 'Cancelado',
                                ];
                                $estadoActual = old('estado', $trabajo->estado);
                            @endphp
                            <select name="estado" class="form-select @error('estado') is-invalid @enderror">
                                @foreach ($estados as $valor => $texto)
                                    <option value="{{ $valor }}" {{ $estadoActual === $valor ? 'selected' : '' }}>
                                        {{ $texto }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Fechas --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de inicio</label>
                                <input type="datetime-local" name="fecha_ini"
                                    value="{{ old('fecha_ini', $trabajo->fecha_ini ? $trabajo->fecha_ini->format('Y-m-d\TH:i') : '') }}"
                                    class="form-control @error('fecha_ini') is-invalid @enderror">
                                @error('fecha_ini')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de fin</label>
                                <input type="datetime-local" name="fecha_fin"
                                    value="{{ old('fecha_fin', $trabajo->fecha_fin ? $trabajo->fecha_fin->format('Y-m-d\TH:i') : '') }}"
                                    class="form-control @error('fecha_fin') is-invalid @enderror">
                                @error('fecha_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Dirección de la obra --}}
                        <div class="mb-3">
                            <label class="form-label">Dirección de la obra (opcional)</label>
                            <input type="text" name="dir_obra" value="{{ old('dir_obra', $trabajo->dir_obra) }}"
                                class="form-control @error('dir_obra') is-invalid @enderror"
                                placeholder="Calle, número, piso...">
                            @error('dir_obra')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Si la dirección de la obra no coincide con la dirección del cliente, puedes ajustarla aquí.
                            </small>
                        </div>

                        {{-- Botones --}}
                        <div class="mt-4 d-flex flex-column flex-md-row gap-2 justify-content-end">
                            <a href="{{ route('admin.trabajos') }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Guardar cambios
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
