@if (session('success'))
    <script>
        Swal.fire({
            title: '¡Éxito!',
            text: @json(session('success')),
            icon: 'success',
            confirmButtonText: 'Aceptar'
        });
    </script>
@endif

@if (session('error'))
    <script>
        Swal.fire({
            title: 'Error',
            text: @json(session('error')),
            icon: 'error',
            confirmButtonText: 'Aceptar',
            didClose: () => {
                if (window.location.hash) {
                    const target = document.querySelector(window.location.hash);
                    if (target) target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    return;
                }
                const el = document.getElementById('contacto-form');
                if (el) el.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    </script>
@endif

@if (session('info'))
    <script>
        Swal.fire({
            title: 'Información',
            text: @json(session('info')),
            icon: 'info',
            confirmButtonText: 'Aceptar'
        });
    </script>
@endif

@if (isset($errors) && $errors->has('concepto'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: @json($errors->first('concepto')),
            });
        });
    </script>
@endif
