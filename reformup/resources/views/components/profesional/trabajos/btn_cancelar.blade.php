@props(['trabajo'])

<form
    id="form-cancelar-trabajo-pro-{{ $trabajo->id }}"
    action="{{ route('profesional.trabajos.cancelar', $trabajo) }}"
    method="POST"
    class="d-inline"
>
    @csrf
    @method('PATCH')

    <input type="hidden" name="motivo" id="motivo-cancelar-trabajo-pro-{{ $trabajo->id }}">

    <button
        type="button"
        id="btn-cancelar-trabajo-pro-{{ $trabajo->id }}"
        class="btn btn-outline-danger btn-sm px-2 py-1 mx-1"
    >
        Cancelar
    </button>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn   = document.getElementById('btn-cancelar-trabajo-pro-{{ $trabajo->id }}');
    const form  = document.getElementById('form-cancelar-trabajo-pro-{{ $trabajo->id }}');
    const input = document.getElementById('motivo-cancelar-trabajo-pro-{{ $trabajo->id }}');

    if (!btn || !form || !input) return;

    btn.addEventListener('click', function (e) {
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
