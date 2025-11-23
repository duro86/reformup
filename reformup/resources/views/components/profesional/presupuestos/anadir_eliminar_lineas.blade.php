{{-- JS para añadir / eliminar líneas --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const contenedor = document.getElementById('lineas-presupuesto');
            const btnAdd = document.getElementById('btn-add-linea');

            if (!contenedor || !btnAdd) return;

            // Añadir nueva línea clonando la primera
            btnAdd.addEventListener('click', () => {
                const firstRow = contenedor.querySelector('.linea-item');
                if (!firstRow) return;

                const row = firstRow.cloneNode(true);

                // Limpiar inputs
                row.querySelectorAll('input').forEach(input => {
                    input.value = '';
                    input.classList.remove('is-invalid');
                });

                contenedor.appendChild(row);
            });

            // Eliminar línea con delegación de eventos
            contenedor.addEventListener('click', (e) => {
                const btn = e.target.closest('.btn-remove-linea');
                if (!btn) return;

                const filas = contenedor.querySelectorAll('.linea-item');
                const fila = btn.closest('.linea-item');

                if (!fila) return;

                if (filas.length > 1) {
                    // Si hay más de 1, se elimina la fila
                    fila.remove();
                } else {
                    // Si es la única, solo limpiamos sus campos (no la quitamos)
                    fila.querySelectorAll('input').forEach(input => {
                        input.value = '';
                        input.classList.remove('is-invalid');
                    });
                }
            });
        });
    </script>
@endpush