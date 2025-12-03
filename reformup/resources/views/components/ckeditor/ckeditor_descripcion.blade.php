@props([
    // ID del textarea sobre el que se va a montar CKEditor
    'for' => 'descripcion',
])

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Construimos el selector a partir del id recibido por props
            const selector = '#{{ $for }}';
            const el = document.querySelector(selector);

            // Si no encontramos el textarea en el DOM, mostramos un aviso y salimos
            if (!el) {
                console.warn('CKEditor: no se encontró el elemento', selector);
                return;
            }

            // Comprobamos que la librería de CKEditor esté cargada
            if (typeof ClassicEditor === 'undefined') {
                console.error('CKEditor: ClassicEditor no está definido. ¿Se ha cargado la librería?');
                return;
            }

            // Inicializamos CKEditor sobre el textarea encontrado
            ClassicEditor
                .create(el, {
                    // Configuración básica de la barra de herramientas
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote',
                        '|', 'undo', 'redo'
                    ]
                })
                .then(editor => {
                    // Opcional: guardamos la instancia en window por si se necesita acceder
                    // más tarde (ej. para depuración o para manipulación desde consola)
                    window['editor_' + @json($for)] = editor;
                })
                .catch(error => {
                    // Si algo falla al crear el editor, lo mostramos en consola
                    console.error('Error cargando CKEditor:', error);
                });
        });
    </script>
@endpush
