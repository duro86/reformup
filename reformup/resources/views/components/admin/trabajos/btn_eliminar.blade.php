@props(['trabajo'])

@php
    $formId = 'form-eliminar-trabajo-admin-' . $trabajo->id;
    $fnName = 'confirmEliminarTrabajoAdmin' . $trabajo->id;

    $mensaje = "Se eliminará este trabajo. 
    El presupuesto asociado pasará a RECHAZADO y la solicitud a CANCELADA.
    Esta acción no se puede deshacer. ¿Deseas continuar?";
@endphp

<form id="{{ $formId }}"
      action="{{ route('admin.trabajos.eliminar_admin', $trabajo) }}"
      method="POST"
      class="d-inline">
    @csrf
    @method('DELETE')

    <button type="button"
            class="btn btn-danger btn-sm w-100 d-flex justify-content-center align-items-center gap-1 my-1"
            onclick="{{ $fnName }}()">
        <i class="bi bi-trash"></i>
        Eliminar
    </button>
</form>

@push('scripts')
<script>
function {{ $fnName }}() {
    Swal.fire({
        title: 'Eliminar trabajo',
        text: @json($mensaje),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar todo',
        cancelButtonText: 'No, cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('{{ $formId }}').submit();
        }
    });
}
</script>
@endpush
