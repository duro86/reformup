@props([
    'presupuesto',
    'contexto' => 'desktop', // 'desktop' o 'mobile'
])

@php
    $idBase = $presupuesto->id;
    $suffix = $contexto === 'mobile' ? '-m' : '';
    $formId = "form-rechazar-presupuesto-{$idBase}{$suffix}";
    $btnId = "btn-rechazar-presu-{$idBase}{$suffix}";
@endphp

<form id="{{ $formId }}" action="{{ route('usuario.presupuestos.rechazar', $presupuesto) }}" method="POST"
    style="display:none;">
    @csrf
    @method('POST')
</form>

<button type="button" id="{{ $btnId }}" class="btn btn-danger btn-sm w-auto">
    Rechazar
</button>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('{{ $btnId }}');
            const form = document.getElementById('{{ $formId }}');

            if (!btn || !form) return;

            btn.addEventListener('click', function() {

                Swal.fire({
                    title: 'Rechazar presupuesto',
                    input: 'textarea',
                    inputLabel: 'Motivo del rechazo (opcional, pero recomendado)',
                    inputPlaceholder: 'Ej: Me ha parecido demasiado caro, he encontrado otra empresa...',
                    inputAttributes: {
                        'aria-label': 'Motivo'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Rechazar presupuesto',
                    cancelButtonText: 'Cancelar',
                    inputValidator: (value) => {
                        if (value && value.length > 500) {
                            return 'El motivo no puede superar los 500 caracteres.';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Creamos un campo oculto para enviar el motivo
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'motivo';
                        hidden.value = result.value || '';
                        form.appendChild(hidden);

                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
