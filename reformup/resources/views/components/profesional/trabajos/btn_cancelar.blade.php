@props(['trabajo', 'context' => 'desktop'])

@php
    $btnWidthClass =
        $context === 'mobile'
            ? 'w-100' // en móvil, ancho completo
            : 'w-auto'; // en escritorio, solo lo que ocupe el contenido
@endphp

@php
    // Sufijo único por trabajo + contexto
    $suffix = $context ? $trabajo->id . '-' . $context : $trabajo->id;
@endphp

<form id="form-cancelar-trabajo-pro-{{ $suffix }}" action="{{ route('profesional.trabajos.cancelar', $trabajo) }}"
    method="POST" class="d-inline w-100">
    @csrf
    @method('PATCH')

    <input type="hidden" name="motivo" id="motivo-cancelar-trabajo-pro-{{ $suffix }}">

    <button type="button" id="btn-cancelar-trabajo-pro-{{ $suffix ?? $trabajo->id }}"
        class="btn btn-outline-danger btn-sm px-2 py-1 {{ $btnWidthClass }}">
        Cancelar
    </button>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnId = 'btn-cancelar-trabajo-pro-{{ $suffix }}';
            const formId = 'form-cancelar-trabajo-pro-{{ $suffix }}';
            const inputId = 'motivo-cancelar-trabajo-pro-{{ $suffix }}';

            const btn = document.getElementById(btnId);
            const form = document.getElementById(formId);
            const input = document.getElementById(inputId);

            if (!btn || !form || !input) return;

            // Fallback si no hay SweetAlert cargado
            if (typeof Swal === 'undefined') {
                btn.addEventListener('click', function() {
                    form.submit();
                });
                return;
            }

            btn.addEventListener('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: '¿Cancelar trabajo?',
                    text: 'Si cancelas este trabajo, el cliente será notificado.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cancelar',
                    cancelButtonText: 'No, mantener',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                    input: 'textarea',
                    inputLabel: 'Motivo de la cancelación (opcional)',
                    inputPlaceholder: 'Escribe aquí el motivo para el cliente...',
                }).then((result) => {
                    if (result.isConfirmed) {
                        input.value = result.value || '';
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
