@props(['solicitud'])

@php
    $formId = 'form-rechazar-solicitud-' . $solicitud->id;
    $fnName = 'confirmRechazarSolicitud' . $solicitud->id;
@endphp

<form id="{{ $formId }}"
      action="{{ route('profesional.solicitudes.cancelar', $solicitud) }}"
      method="POST"
      class="d-inline">
    @csrf
    @method('PATCH')

    {{-- En escritorio: botón normal. En móvil ya lo metes dentro de d-grid y se adapta --}}
    <button type="button"
            class="btn btn-danger btn-sm w-100 d-flex justify-content-center align-items-center gap-1"
            onclick="{{ $fnName }}()">
        <i class="bi bi-x-circle"></i>
        Rechazar
    </button>
</form>

@push('scripts')
    <script>
        function {{ $fnName }}() {
            Swal.fire({
                title: 'Rechazar solicitud',
                text: 'Esta solicitud quedará marcada como rechazada y el cliente verá que no vas a presupuestar este trabajo.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, rechazar',
                cancelButtonText: 'No, mantener',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('{{ $formId }}').submit();
                }
            });
        }
    </script>
@endpush
