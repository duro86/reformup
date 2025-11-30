@extends('layouts.main')

@section('title', 'Editar presupuesto - Admin - ReformUp')

@section('content')

    <x-navbar />
    <x-admin.admin_sidebar />

    <div class="container-fluid main-content-with-sidebar">
        <x-admin.nav_movil active="presupuestos" />

        <div class="container py-4">

            <a href="{{ route('admin.presupuestos') }}" class="btn btn-secondary btn-sm mb-3">
                Volver a presupuestos
            </a>

            <h1 class="h4 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square"></i>
                Editar presupuesto #{{ $presupuesto->id }}
            </h1>

            {{-- Info de la solicitud --}}
            <div class="card mb-4">
                <div class="card-body">

                    <h2 class="h6 mb-2">Solicitud relacionada</h2>

                    <p class="mb-1"><strong>Título:</strong> {{ $presupuesto->solicitud->titulo }}</p>

                    @php
                        $cliente = $presupuesto->solicitud->cliente;
                        $pro = $presupuesto->solicitud->profesional;
                    @endphp

                    <p class="mb-1">
                        <strong>Cliente:</strong>
                        @if ($cliente)
                            {{ $cliente->nombre }} {{ $cliente->apellidos }}
                            ({{ $cliente->email }})
                        @else
                            <span class="text-muted">Sin cliente</span>
                        @endif
                    </p>

                    <p class="mb-1">
                        <strong>Profesional:</strong>
                        @if ($pro)
                            {{ $pro->empresa }} — {{ $pro->email_empresa }}
                        @else
                            <span class="text-muted">Sin profesional</span>
                        @endif
                    </p>

                    <p class="mb-0">
                        <strong>Ubicación:</strong>
                        {{ $presupuesto->solicitud->ciudad }}
                        {{ $presupuesto->solicitud->provincia ? ' - ' . $presupuesto->solicitud->provincia : '' }}
                    </p>
                </div>
            </div>

            {{-- Formulario edición --}}
            <div class="card">
                <div class="card-body">

                    {{-- Mensajes flash --}}
                    <x-alertas.alertasFlash />

                    <form method="POST" action="{{ route('admin.presupuestos.actualizar', $presupuesto) }}">
                        @csrf
                        @method('PUT')

                        {{-- Total --}}
                        <div class="mb-3">
                            <label class="form-label">Importe total (€)</label>
                            <input type="number" step="0.01" min="0" name="total"
                                value="{{ old('total', $presupuesto->total) }}"
                                class="form-control @error('total') is-invalid @enderror">
                            @error('total')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Estado --}}
                        <div class="mb-3">
                            <label class="form-label">Estado del presupuesto</label>
                            <select name="estado" class="form-select @error('estado') is-invalid @enderror">
                                @foreach (['enviado', 'aceptado', 'rechazado', 'caducado'] as $estado)
                                    <option value="{{ $estado }}"
                                        {{ old('estado', $presupuesto->estado) === $estado ? 'selected' : '' }}>
                                        {{ ucfirst($estado) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Notas --}}
                        <div class="mb-3">
                            <label class="form-label">Notas internas / Notas para el cliente</label>
                            <textarea name="notas" rows="4" class="form-control @error('notas') is-invalid @enderror" style="resize:none;">{{ old('notas', $presupuesto->notas) }}</textarea>
                            @error('notas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.presupuestos') }}" class="btn btn-outline-secondary">
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
