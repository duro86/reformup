@props(['presupuesto'])

@php
    $formId = 'form-eliminar-presupuesto-admin-' . $presupuesto->id;
    $fnName = 'confirmEliminarPresupuestoAdmin' . $presupuesto->id;

    $mensaje = match ($presupuesto->estado) {
        'aceptado'
            => 'Se cancelará este presupuesto aceptado y el trabajo asociado (si aún no ha comenzado). La solicitud pasará a estado "en revisión" para poder generar un nuevo presupuesto. ¿Deseas continuar?',
        'rechazado'
            => 'Se eliminará definitivamente este presupuesto rechazado. La solicitud asociada quedará cerrada. ¿Deseas continuar?',
        default => 'Se aplicará la lógica de eliminación según el estado del presupuesto. ¿Deseas continuar?',
    };
@endphp

<form id="{{ $formId }}" action="{{ route('admin.presupuestos.eliminar_admin', $presupuesto) }}" method="POST"
    class="d-inline">
    @csrf
    @method('DELETE')

    <button type="button" class="btn btn-danger btn-sm w-100 d-flex justify-content-center align-items-center gap-1 my-1"
        onclick="{{ $fnName }}()">
        <i class="bi bi-trash"></i>
        Eliminar
    </button>
</form>

@push('scripts')
    <script>
        function {{ $fnName }}() {
            Swal.fire({
                title: 'Eliminar presupuesto',
                text: @json($mensaje),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'No, cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('{{ $formId }}').submit();
                }
            });
        }
    </script>
@endpush
