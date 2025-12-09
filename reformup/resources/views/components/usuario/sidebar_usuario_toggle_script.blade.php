@push('scripts')
    <script>
        // Esperamos a que todo el DOM esté completamente cargado
        document.addEventListener('DOMContentLoaded', function() {

            // Cogemos el sidebar por su ID
            const sidebar = document.getElementById('sidebar');

            // Cogemos el botón que abre/cierra el sidebar
            const toggleBtn = document.getElementById('sidebar-toggle');

            // Si alguno de los dos no existe, salimos para evitar errores
            if (!sidebar || !toggleBtn) return;

            // Dentro del botón cogemos el icono <i>
            const icon = toggleBtn.querySelector('i');

            // Función que detecta si estamos en vista móvil
            const isMobile = () => window.innerWidth < 992;

            // Función que sincroniza el icono según el estado del sidebar
            function syncIcon() {

                // Si estamos en versión móvil
                if (isMobile()) {

                    // Si el sidebar está abierto
                    if (sidebar.classList.contains('sidebar-open')) {
                        icon.classList.remove('bi-chevron-right');
                        icon.classList.add('bi-chevron-left');
                    }
                    // Si el sidebar está cerrado
                    else {
                        icon.classList.remove('bi-chevron-left');
                        icon.classList.add('bi-chevron-right');
                    }

                }
                // Si estamos en versión escritorio
                else {

                    // Si el sidebar está colapsado
                    if (sidebar.classList.contains('sidebar-collapsed')) {
                        icon.classList.remove('bi-chevron-left');
                        icon.classList.add('bi-chevron-right');
                    }
                    // Si el sidebar está expandido
                    else {
                        icon.classList.remove('bi-chevron-right');
                        icon.classList.add('bi-chevron-left');
                    }
                }
            }

            // Ejecutamos la función nada más cargar la página
            syncIcon();

            // Evento click del botón del sidebar
            toggleBtn.addEventListener('click', function() {

                // En vista móvil abrimos/cerramos con sidebar-open
                if (isMobile()) {
                    sidebar.classList.toggle('sidebar-open');

                }
                // En escritorio usamos sidebar-collapsed
                else {
                    sidebar.classList.toggle('sidebar-collapsed');

                    // También lo aplicamos al body para reajustar el contenido
                    document.body.classList.toggle('sidebar-collapsed');
                }

                // Actualizamos el icono después del cambio
                syncIcon();
            });

            // Cuando se redimensiona la pantalla,
            // recalculamos qué icono debe mostrarse
            window.addEventListener('resize', syncIcon);
        });
    </script>
@endpush
