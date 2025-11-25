@props([
    'for' => 'descripcion', // id del textarea
])

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selector = '#{{ $for }}';
            const el = document.querySelector(selector);

            if (!el) {
                console.warn('CKEditor: no se encontró el elemento', selector);
                return;
            }

            if (typeof ClassicEditor === 'undefined') {
                console.error('CKEditor: ClassicEditor no está definido. ¿Se ha cargado la librería?');
                return;
            }

            ClassicEditor
                .create(el, {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote',
                        '|', 'undo', 'redo'
                    ]
                })
                .then(editor => {
                    // Si quieres guardar la instancia:
                    window['editor_' + @json($for)] = editor;
                })
                .catch(error => {
                    console.error('Error cargando CKEditor:', error);
                });
        });
    </script>
@endpush
