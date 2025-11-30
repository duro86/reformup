@props(['solicitud'])

@php
    $formId = 'form-cancelar-solicitud-' . $solicitud->id;
    $fnName = 'confirmCancelarSolicitud' . $solicitud->id;
@endphp

<form id="{{ $formId }}"
      action="{{ route('admin.solicitudes.cancelar', $solicitud) }}"
      method="POST"
      class="d-inline">
    @csrf
    @method('PATCH')

    <button type="button"
            class="btn btn-outline-danger btn-sm mt-2"
            onclick="{{ $fnName }}()">
        <i class="bi bi-x-circle"></i>
        Cancelar
    </button>
</form>

@push('scripts')
    <script>
        function {{ $fnName }}() {
            Swal.fire({
                title: 'Cancelar solicitud',
                text: 'Si cancelas esta solicitud, se actualizarán el presupuesto y el trabajo asociados (si existen). Esta acción no se puede deshacer.',
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
