@props([
    'presupuesto',
    'contexto' => 'desktop',      // 'desktop' o 'mobile'
    'tieneDireccion' => false,    // bool: ¿la solicitud ya tiene dir_cliente?
    'direccionObra' => null,      // string: texto de la dirección (si existe)
])

@php
    // Sufijo para diferenciar desktop / mobile y evitar IDs duplicados
    $suffix = $contexto === 'mobile' ? '-m' : '';
    $formId = 'form-aceptar-' . $presupuesto->id . $suffix;
    $btnId  = 'btn-aceptar-presu-' . $presupuesto->id . $suffix;
@endphp

<form id="{{ $formId }}" method="POST" action="{{ route('usuario.presupuestos.aceptar', $presupuesto) }}">
    @csrf

    {{-- Campo oculto que rellenaremos si el user escribe la dirección --}}
    <input type="hidden" name="direccion_obra" value="">

    <button
        type="button"
        id="{{ $btnId }}"
        class="btn btn-success btn-sm w-100"
    >
        Aceptar
    </button>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn  = document.getElementById('{{ $btnId }}');
            const form = document.getElementById('{{ $formId }}');
            if (!btn || !form) return;

            const tieneDirObra = @json((bool) $tieneDireccion);
            const dirObraTexto = @json($direccionObra);

            btn.addEventListener('click', function () {

                // CASO 1: la solicitud YA tiene dirección -> solo confirmamos
                if (tieneDirObra) {
                    Swal.fire({
                        title: 'Aceptar presupuesto',
                        html: `¿Seguro que deseas aceptar este presupuesto?<br><br>
                               <span class="fw-semibold">Se utilizará esta dirección de obra:</span><br>
                               <span class="text-muted">${dirObraTexto ?? ''}</span>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Aceptar presupuesto',
                        cancelButtonText: 'Cancelar',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });

                    return;
                }

                // CASO 2: no hay dirección -> pedimos la dirección al usuario
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
                        const inputDir = form.querySelector('input[name="direccion_obra"]');
                        if (inputDir) {
                            inputDir.value = result.value;
                        }
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
