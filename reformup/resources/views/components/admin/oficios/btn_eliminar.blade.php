@props(['oficio'])

@php
    $formId = 'form-eliminar-oficio-' . $oficio->id;
    $fnName = 'confirmEliminarOficio' . $oficio->id;

    $mensaje = 'Se eliminará el oficio "' . $oficio->nombre . '". '
        . 'Se desasociará de todos los perfiles profesionales que lo tengan asignado. '
        . 'Esta acción no se puede deshacer. ¿Deseas continuar?';
@endphp

<form id="{{ $formId }}"
      action="{{ route('admin.oficios.eliminar', $oficio) }}"
      method="POST"
      class="d-inline">
    @csrf
    @method('DELETE')

    <button type="button"
            class="btn btn-danger btn-sm w-100 px-2 py-1 d-inline-flex align-items-center gap-1 justify-content-center"
            onclick="{{ $fnName }}()">
        <i class="bi bi-trash"></i>
        Eliminar
    </button>
</form>

@push('scripts')
    <script>
        function {{ $fnName }}() {
            Swal.fire({
                title: 'Eliminar oficio',
                text: @json($mensaje),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'No, cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('{{ $formId }}').submit();
                }
            });
        }
    </script>
@endpush
