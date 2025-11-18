@props(['presupuesto'])

@php
    $id = $presupuesto->id;
    $formId = "form-rechazar-presupuesto-$id";
@endphp

<form id="{{ $formId }}"
      action="{{ route('usuario.presupuestos.rechazar', $presupuesto) }}"
      method="POST"
      style="display:none;">
    @csrf
    @method('PATCH')
</form>

<button type="button"
        class="btn btn-outline-danger btn-sm px-2 py-1 mx-1"
        id="btn-rechazar-presu-{{ $id }}">
    Rechazar
</button>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btn-rechazar-presu-{{ $id }}');
    if (!btn) return;

    btn.addEventListener('click', function () {
        Swal.fire({
            title: 'Rechazar presupuesto',
            text: '¿Seguro que deseas rechazar este presupuesto?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, rechazar',
            cancelButtonText: 'No, volver',
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('{{ $formId }}');
                if (form) form.submit();
            }
        });
    });
});
</script>
@endpush
