{{-- Nota: El botón de plegar/desplegar sidebar requiere JavaScript adicional para funcionar --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle');

            if (!sidebar || !toggleBtn) return;

            const icon = toggleBtn.querySelector('i');

            toggleBtn.addEventListener('click', function() {
                const collapsed = sidebar.classList.toggle('sidebar-collapsed');

                if (collapsed) {
                    // Sidebar recogido → flecha hacia la derecha (para indicar que se puede abrir)
                    icon.classList.remove('bi-chevron-left');
                    icon.classList.add('bi-chevron-right');
                } else {
                    // Sidebar abierto → flecha hacia la izquierda (para cerrarlo)
                    icon.classList.remove('bi-chevron-right');
                    icon.classList.add('bi-chevron-left');
                }
            });
        });
    </script>
@endpush