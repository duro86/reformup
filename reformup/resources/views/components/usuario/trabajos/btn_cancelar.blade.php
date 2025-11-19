@props(['trabajo'])

<form id="form-cancelar-trabajo-{{ $trabajo->id }}" action="{{ route('usuario.trabajos.cancelar', $trabajo) }}"
    method="POST" class="d-inline">
    @csrf
    @method('PATCH')

    {{-- Aquí guardaremos el motivo que escriba el usuario en el SweetAlert --}}
    <input type="hidden" name="motivo" id="motivo-cancelar-trabajo-{{ $trabajo->id }}">

    <button type="button" id="btn-cancelar-trabajo-{{ $trabajo->id }}" class="btn btn-outline-danger btn-sm px-2 py-1">
        Cancelar trabajo
    </button>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('btn-cancelar-trabajo-{{ $trabajo->id }}');
            const form = document.getElementById('form-cancelar-trabajo-{{ $trabajo->id }}');
            const input = document.getElementById('motivo-cancelar-trabajo-{{ $trabajo->id }}');

            if (!btn || !form || !input) return;

            btn.addEventListener('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: '¿Cancelar trabajo?',
                    text: 'Si cancelas este trabajo, el profesional será notificado.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cancelar',
                    cancelButtonText: 'No, mantener',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                    input: 'textarea',
                    inputLabel: 'Motivo de la cancelación (opcional)',
                    inputPlaceholder: 'Escribe aquí el motivo (puede quedar vacío)...',
                    inputAttributes: {
                        'aria-label': 'Motivo de la cancelación'
                    },
                    // Si quisieras obligar a poner motivo:
                    // inputValidator: (value) => {
                    //     if (!value) {
                    //         return 'Por favor, indica un motivo.';
                    //     }
                    //     return null;
                    // }
                }).then((result) => {
                    if (result.isConfirmed) {
                        input.value = result.value || '';
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
