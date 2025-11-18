@props(['presupuesto'])

@php
    $id = $presupuesto->id;
    $formId = "form-aceptar-presupuesto-$id";
    $dirObra = optional($presupuesto->solicitud)->dir_cliente; // puede ser null
@endphp

<form id="{{ $formId }}" action="{{ route('usuario.presupuestos.aceptar', $presupuesto) }}" method="POST"
    style="display:none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="direccion_obra" value="">
</form>

<button type="button" class="btn btn-success btn-sm px-2 py-1 mx-1" id="btn-aceptar-presu-{{ $id }}">
    Aceptar
</button>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('btn-aceptar-presu-{{ $id }}');
            if (!btn) return;

            const tieneDirObra = @json((bool) $dirObra);
            const dirObraTexto = @json($dirObra);

            btn.addEventListener('click', function() {

                // Caso 1: la solicitud ya tiene dir_cliente → solo informamos
                if (tieneDirObra) {
                    Swal.fire({
                        title: 'Aceptar presupuesto',
                        html: `¿Seguro que deseas aceptar este presupuesto?<br><br>
                       <span class="fw-semibold">Se utilizará esta dirección de obra:</span><br>
                       <span class="text-muted">{{ $dirObra }}</span>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Aceptar presupuesto',
                        cancelButtonText: 'Cancelar',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('{{ $formId }}');
                            if (!form) return;

                            // No necesitamos rellenar direccion_obra:
                            // el backend usará la dir_cliente de la solicitud.
                            form.submit();
                        }
                    });

                    return;
                }

                // Caso 2: NO hay dirección en la solicitud → pedirla por input
                Swal.fire({
                    title: 'Aceptar presupuesto',
                    text: '¿Seguro que deseas aceptar este presupuesto? Se creará un trabajo asociado.',
                    input: 'text',
                    inputLabel: 'Dirección de la obra',
                    inputPlaceholder: 'Ej: Calle Ejemplo 123, 1ºB, Sevilla',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Debes indicar la dirección de la obra';
                        }
                        if (value.length > 255) {
                            return 'La dirección es demasiado larga (máx. 255 caracteres)';
                        }
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar presupuesto',
                    cancelButtonText: 'Cancelar',
                    icon: 'question'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('{{ $formId }}');
                        if (!form) return;

                        form.querySelector('input[name="direccion_obra"]').value = result.value;
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
