@props([
    'solicitud', // instancia de App\Models\Solicitud
    'route' => null, // opcional, para sobreescribir la ruta
])

@php
    $action = $route ? $route : route('usuario.solicitudes.eliminar', $solicitud);

    $titulo = $solicitud->titulo ?? 'esta solicitud'; //si la solicitud está “abierta”.
@endphp

@if ($solicitud->estado === 'abierta')
    <form action="{{ $action }}" method="POST" class="d-inline form-eliminar-solicitud">
        @csrf
        @method('DELETE')

        <button type="button"
            class="btn btn-danger btn-sm w-100 w-md-auto d-flex justify-content-center align-items-center gap-1"
            data-titulo="{{ $titulo }}">
            <i class="bi bi-trash"></i>
            <span class="d-none d-md-inline">Eliminar</span>
        </button>
    </form>
@else
    <span class="text-muted small d-none d-md-inline"></span>
@endif

{{-- @once asegura que el JS de SweetAlert se inyecta una sola vez --}}
@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const botones = document.querySelectorAll('.btn-confirmar-eliminar');

                botones.forEach(boton => {
                    boton.addEventListener('click', function() {
                        const form = this.closest('form');
                        const titulo = this.dataset.titulo || 'esta solicitud';

                        Swal.fire({
                            title: '¿Eliminar solicitud?',
                            html: `Vas a eliminar <strong>${titulo}</strong>.<br><small>Esta acción no se puede deshacer.</small>`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar',
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            focusCancel: true,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce
