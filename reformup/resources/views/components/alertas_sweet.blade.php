
@if(session('success'))
    <script>
        Swal.fire({
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'Aceptar'
        });
    </script>
@endif

@if(session('error'))
    <script>
        Swal.fire({
            title: 'Error',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonText: 'Aceptar'
        });
    </script>
@endif

@if(session('info'))
    <script>
        Swal.fire({
            title: 'Información',
            text: @json(session('info')),
            icon: 'info',
            confirmButtonText: 'Aceptar'
        });
    </script>
@endif

@if ($errors->has('concepto'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: @json($errors->first('concepto')),
            });
        });
    </script>
@endif