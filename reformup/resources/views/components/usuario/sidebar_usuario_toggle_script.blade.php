@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar   = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle');

    if (!sidebar || !toggleBtn) return;

    const icon = toggleBtn.querySelector('i');

    function isMobile() {
        return window.innerWidth <= 991; // mismo breakpoint que en SCSS
    }

    toggleBtn.addEventListener('click', function() {
        if (isMobile()) {
            // En móvil mostramos/ocultamos completamente
            const opened = sidebar.classList.toggle('sidebar-open');

            if (opened) {
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-left');
            } else {
                icon.classList.remove('bi-chevron-left');
                icon.classList.add('bi-chevron-right');
            }
        } else {
            // En escritorio usamos la versión "recogida"
            const collapsed = sidebar.classList.toggle('sidebar-collapsed');

            if (collapsed) {
                icon.classList.remove('bi-chevron-left');
                icon.classList.add('bi-chevron-right');
            } else {
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-left');
            }
        }
    });
});
</script>
@endpush
