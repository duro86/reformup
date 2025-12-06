{{-- resources/views/components/usuario/solicitudes/btn_cancelar.blade.php --}}
@props([
    'solicitud',
    'contexto' => 'desktop', // 'desktop' o 'mobile'
])

@php
    $idBase = $solicitud->id;
    $suffix = $contexto === 'mobile' ? '-m' : '';
    $formId = "form-cancelar-solicitud-{$idBase}{$suffix}";
    $btnId  = "btn-cancelar-solicitud-{$idBase}{$suffix}";
@endphp

<form id="{{ $formId }}"
      action="{{ route('usuario.solicitudes.cancelar', $solicitud) }}"
      method="POST"
      style="display:none;">
    @csrf
    @method('PATCH')

    {{-- Motivo obligatorio --}}
    <input type="hidden" name="motivo_cancelacion" value="">
</form>

<button type="button"
        id="{{ $btnId }}"
        class="btn btn-outline-danger btn-sm w-auto">
    Cancelar
</button>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn  = document.getElementById('{{ $btnId }}');
    const form = document.getElementById('{{ $formId }}');
    if (!btn || !form) return;

    btn.addEventListener('click', function () {
        Swal.fire({
            title: 'Cancelar solicitud',
            text: 'Solo puedes cancelar solicitudes en estado "abierta".',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Continuar',
            cancelButtonText: 'Volver',
            input: 'textarea',
            inputLabel: 'Motivo de la cancelación',
            inputPlaceholder: 'Explica por qué quieres cancelar esta solicitud...',
            inputValidator: (value) => {
                if (!value || !value.trim()) {
                    return 'Por favor, indica un motivo para la cancelación.';
                }
                return null;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const hidden = form.querySelector('input[name="motivo_cancelacion"]');
                if (hidden) {
                    hidden.value = result.value.trim();
                }
                form.submit();
            }
        });
    });
});
</script>
@endpush
