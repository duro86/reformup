@props(['trabajo'])

@php
    $formId = 'form-cancelar-trabajo-' . $trabajo->id;
    $fnName = 'confirmCancelarTrabajo' . $trabajo->id;
@endphp

<form id="{{ $formId }}"
      action="{{ route('admin.trabajos.cancelar', $trabajo) }}"
      method="POST"
      class="d-inline">
    @csrf
    @method('PATCH')

    <button type="button"
            class="btn btn-outline-danger btn-sm px-2 py-1 w-100"
            onclick="{{ $fnName }}()">
        <i class="bi bi-x-circle"></i> Cancelar
    </button>
</form>

@push('scripts')
    <script>
        function {{ $fnName }}() {
            Swal.fire({
                title: 'Cancelar trabajo',
                text: 'Vas a marcar este trabajo como cancelado. El presupuesto asociado mantendrá su estado actual, pero se avisará al cliente y al profesional.',
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
