{{-- resources/views/components/usuario/solicitudes/btn_eliminar.blade.php --}}
@props([
    'solicitud',
    'route' => null,
])

@php
    $action = $route ? $route : route('usuario.solicitudes.eliminar', $solicitud);
    $titulo = $solicitud->titulo ?? 'esta solicitud';
@endphp

@if ($solicitud->estado === 'abierta')
    <form action="{{ $action }}" method="POST" class="d-inline form-eliminar-solicitud">
        @csrf
        @method('DELETE')

        <input type="hidden" name="motivo_eliminacion" value="">

        <button type="button"
                class="btn btn-danger btn-sm w-100 d-flex justify-content-center align-items-center gap-1 btn-confirmar-eliminar"
                data-titulo="{{ $titulo }}">
            <i class="bi bi-trash"><span class="d-md-inline">Eliminar</span></i>      
        </button>
    </form>
@else
    <span class="text-muted small d-none d-md-inline"></span>
@endif

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const botones = document.querySelectorAll('.btn-confirmar-eliminar');

                botones.forEach(boton => {
                    boton.addEventListener('click', function() {
                        const form   = this.closest('form');
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
                            input: 'textarea',
                            inputLabel: 'Motivo (opcional)',
                            inputPlaceholder: 'Puedes indicar un motivo para informar al profesional...'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const hidden = form.querySelector('input[name="motivo_eliminacion"]');
                                if (hidden) {
                                    hidden.value = (result.value || '').trim();
                                }
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce
