@props([
    'solicitud',
])

<form
    id="form-cancelar-solicitud-{{ $solicitud->id }}"
    action="{{ route('profesional.solicitudes.cancelar', $solicitud) }}"
    method="POST"
    class="d-inline"
>
    @csrf
    @method('PATCH')

    <button
        type="button"
        id="btn-cancelar-solicitud-{{ $solicitud->id }}"
        class="btn btn-danger btn-sm px-2 py-1"
    >
        Cancelar
    </button>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn  = document.getElementById('btn-cancelar-solicitud-{{ $solicitud->id }}');
    const form = document.getElementById('form-cancelar-solicitud-{{ $solicitud->id }}');

    if (!btn || !form) return;

    btn.addEventListener('click', function (e) {
        e.preventDefault();

        Swal.fire({
            title: '¿Cancelar solicitud?',
            text: 'Si cancelas esta solicitud, no podrás enviar ni recibir presupuestos para ella.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No, mantener',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
