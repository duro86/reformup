@props(['comentario'])

@php
    $formId = 'form-rechazar-comentario-' . $comentario->id;
    $fnName = 'confirmRechazarComentario' . $comentario->id;
@endphp

<form id="{{ $formId }}" action="{{ route('admin.comentarios.rechazar', $comentario) }}" method="POST"
    class="d-inline">
    @csrf
    @method('PATCH')

    <button type="button" class="btn btn-outline-danger btn-sm px-2 py-1 w-100" onclick="{{ $fnName }}()">
        <i class="bi bi-slash-circle"></i>
        Rechazar
    </button>
</form>

@push('scripts')
    <script>
        function {{ $fnName }}() {
            Swal.fire({
                title: 'Rechazar comentario',
                text: 'Se marcará este comentario como rechazado, no será visible en la plataforma y el usuario no podrá editarlo. Se le enviará un correo explicando la situación.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, rechazar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('{{ $formId }}').submit();
                }
            });
        }
    </script>
@endpush
