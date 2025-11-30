@props(['solicitud'])

@php
    $formId = 'form-eliminar-solicitud-admin-' . $solicitud->id;
    $fnName = 'confirmEliminarSolicitudAdmin' . $solicitud->id;

    // Mensaje base según estado de la solicitud
    $mensaje = match ($solicitud->estado) {
        'abierta'
            => 'Se eliminará esta solicitud abierta. No hay trabajos iniciados, pero se perderá toda la información asociada. ¿Deseas continuar?',
        'en_revision'
            => 'Se eliminará esta solicitud en revisión y el presupuesto asociado, si existe. Si hubiese trabajos pendientes o cancelados se eliminarán también. ¿Deseas continuar?',
        'cancelada'
            => 'Se eliminará definitivamente esta solicitud cancelada junto con su presupuesto y trabajo asociados, si existen. ¿Deseas continuar?',
        default
            => 'Se eliminará esta solicitud. Si tiene presupuesto, trabajo o comentarios asociados, también se eliminarán según su estado. ¿Deseas continuar?',
    };
@endphp

<form id="{{ $formId }}" action="{{ route('admin.solicitudes.eliminar_admin', $solicitud) }}" method="POST"
    class="d-inline">
    @csrf
    @method('DELETE')

    <button type="button" class="btn btn-danger btn-sm w-100 px-2 py-1 d-inline-flex align-items-center gap-1 justify-content-center"
        onclick="{{ $fnName }}()">
        <i class="bi bi-trash"></i>
        Eliminar
    </button>
</form>

@push('scripts')
    <script>
        function {{ $fnName }}() {
            Swal.fire({
                title: 'Eliminar solicitud',
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
