@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle');

            if (!sidebar || !toggleBtn) return;

            const icon = toggleBtn.querySelector('i');

            const isMobile = () => window.innerWidth < 992; // mismo breakpoint que el SCSS

            function syncIcon() {
                if (isMobile()) {
                    // En móvil el sidebar suele estar oculto salvo que tenga .sidebar-open
                    if (sidebar.classList.contains('sidebar-open')) {
                        icon.classList.remove('bi-chevron-right');
                        icon.classList.add('bi-chevron-left');
                    } else {
                        icon.classList.remove('bi-chevron-left');
                        icon.classList.add('bi-chevron-right');
                    }
                } else {
                    // En escritorio, por defecto expandido (chevron-left)
                    if (sidebar.classList.contains('sidebar-collapsed')) {
                        icon.classList.remove('bi-chevron-left');
                        icon.classList.add('bi-chevron-right');
                    } else {
                        icon.classList.remove('bi-chevron-right');
                        icon.classList.add('bi-chevron-left');
                    }
                }
            }

            // Estado inicial: solo ajustamos el icono, NO tocamos clases del sidebar
            syncIcon();

            toggleBtn.addEventListener('click', function() {
                if (isMobile()) {
                    // En móvil: abrir/cerrar off-canvas
                    sidebar.classList.toggle('sidebar-open');
                } else {
                    // En escritorio: modo recogido/expandido
                    sidebar.classList.toggle('sidebar-collapsed');
                }

                // Actualizar icono tras el cambio
                syncIcon();
            });

            // Si cambia el tamaño de ventana, reajustar icono
            window.addEventListener('resize', syncIcon);
        });
    </script>
@endpush
