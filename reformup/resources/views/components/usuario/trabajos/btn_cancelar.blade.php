@props([
    'trabajo',
    // para diferenciar desktop / mobile y no duplicar IDs
    'context' => null,
])

@php
    // ID único por trabajo + contexto
    $suffix = $context ? $trabajo->id . '-' . $context : $trabajo->id;
@endphp

<form id="form-cancelar-trabajo-{{ $suffix }}"
      action="{{ route('usuario.trabajos.cancelar', $trabajo) }}"
      method="POST"
      class="d-inline w-100">
    @csrf
    @method('PATCH')

    <input type="hidden" name="motivo" id="motivo-cancelar-trabajo-{{ $suffix }}">

    <button type="button"
            id="btn-cancelar-trabajo-{{ $suffix }}"
            class="btn btn-outline-danger btn-sm px-2 py-1 w-100">
        Cancelar trabajo
    </button>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnId   = 'btn-cancelar-trabajo-{{ $suffix }}';
    const formId  = 'form-cancelar-trabajo-{{ $suffix }}';
    const inputId = 'motivo-cancelar-trabajo-{{ $suffix }}';

    const btn   = document.getElementById(btnId);
    const form  = document.getElementById(formId);
    const input = document.getElementById(inputId);

    if (!btn || !form || !input) return;

    // Si no existe SweetAlert, envío normal del form
    if (typeof Swal === 'undefined') {
        btn.addEventListener('click', function () {
            form.submit();
        });
        return;
    }

    btn.addEventListener('click', function (e) {
        e.preventDefault();

        Swal.fire({
            title: '¿Cancelar trabajo?',
            text: 'Si cancelas este trabajo, el profesional será notificado.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No, mantener',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
            input: 'textarea',
            inputLabel: 'Motivo de la cancelación (opcional)',
            inputPlaceholder: 'Escribe aquí el motivo (opcional)...',
            inputAttributes: {
                'aria-label': 'Motivo de la cancelación'
            },
            // si lo quieres obligatorio, mete validación aquí
            inputValidator: (value) => {
                // if (!value) return 'Por favor, indica un motivo.';
                return;
            }
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
