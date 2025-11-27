@props(['presupuesto'])

@php
    $formId = 'form-cancelar-presupuesto-admin-' . $presupuesto->id;
    $fnName = 'confirmCancelarPresupuestoAdmin' . $presupuesto->id;
@endphp

<form id="{{ $formId }}" action="{{ route('admin.presupuestos.cancelar', $presupuesto) }}" method="POST"
    class="d-inline">
    @csrf
    @method('PATCH')

    <button type="button" class="btn btn-danger btn-sm w-100 d-flex justify-content-center align-items-center gap-1"
        onclick="{{ $fnName }}()">
        <i class="bi bi-slash-circle"></i>
        Cancelar
    </button>
</form>

@push('scripts')
    <script>
        function {{ $fnName }}() {
            Swal.fire({
                title: 'Cancelar presupuesto',
                text: 'Se cancelará este presupuesto. El cliente ya no podrá aceptarlo y se notificará al cliente y al profesional.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No, mantener',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('{{ $formId }}').submit();
                }
            });
        }
    </script>
@endpush
