@props([
    'presupuesto',
    'contexto' => 'desktop', // 'desktop' o 'mobile'
])

@php
    $idBase = $presupuesto->id;
    $suffix = $contexto === 'mobile' ? '-m' : '';
    $formId = "form-rechazar-presupuesto-{$idBase}{$suffix}";
    $btnId  = "btn-rechazar-presu-{$idBase}{$suffix}";
@endphp

<form id="{{ $formId }}"
      action="{{ route('usuario.presupuestos.rechazar', $presupuesto) }}"
      method="POST"
      style="display:none;">
    @csrf
    @method('PATCH')
</form>

<button type="button"
        id="{{ $btnId }}"
        class="btn btn-danger btn-sm w-100">
    Rechazar
</button>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn  = document.getElementById('{{ $btnId }}');
    const form = document.getElementById('{{ $formId }}');
    if (!btn || !form) return;

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
                form.submit();
            }
        });
    });
});
</script>
@endpush
