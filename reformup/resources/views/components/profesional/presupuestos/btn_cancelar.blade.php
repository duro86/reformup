@props(['presupuesto'])

@php
    $formId = 'form-cancelar-presu-' . $presupuesto->id;
    $fnName = 'confirmCancelarPresu' . $presupuesto->id;
@endphp

<form id="{{ $formId }}"
      action="{{ route('profesional.presupuestos.cancelar', $presupuesto) }}"
      method="POST"
      class="d-inline">
    @csrf
    @method('PATCH')

    <button type="button"
            class="btn btn-outline-danger btn-sm"
            onclick="{{ $fnName }}()">
        Cancelar
    </button>
</form>

@push('scripts')
    <script>
        function {{ $fnName }}() {
            Swal.fire({
                title: 'Cancelar presupuesto',
                text: 'Si cancelas este presupuesto, el cliente ya no podrá aceptarlo.',
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
