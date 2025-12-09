@props([
    'provinciaId' => 'provincia',
    'ciudadId' => 'ciudad',
    'oldProvincia' => null,
    'oldCiudad' => null,
])

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Mapa de provincias con sus municipios asociados
        // Así evitamos que el usuario escriba municipios mal
        const ciudadesPorProvincia = {
            'Huelva': [
                'Huelva',
                'Lepe',
                'Isla Cristina',
                'Ayamonte',
                'Punta Umbría',
                'Moguer',
                'Almonte',
            ],
            'Sevilla': [
                'Sevilla',
                'Dos Hermanas',
                'Alcalá de Guadaíra',
                'Utrera',
                'Mairena del Aljarafe',
                'Camas',
            ],
        };

        // Select de provincia y municipio (dinámicos por props)
        const provinciaSelect = document.getElementById(@json($provinciaId));
        const ciudadSelect    = document.getElementById(@json($ciudadId));

        // Si alguno de los dos no existe, abortamos
        if (!provinciaSelect || !ciudadSelect) {
            return;
        }

        // Valores antiguos en caso de validación con errores
        const oldProvincia = @json($oldProvincia);
        const oldCiudad    = @json($oldCiudad);

        // Función que carga los municipios según la provincia seleccionada
        function cargarCiudades(provincia, ciudadSeleccionada = null) {

            // Limpiamos las opciones anteriores
            ciudadSelect.innerHTML = '';

            // Si no hay provincia válida, mostramos mensaje por defecto
            if (!provincia || !ciudadesPorProvincia[provincia]) {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Selecciona primero una provincia';
                ciudadSelect.appendChild(option);
                return;
            }

            // Opción placeholder de municipio
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Selecciona un municipio';
            ciudadSelect.appendChild(placeholder);

            // Recorremos los municipios de la provincia
            ciudadesPorProvincia[provincia].forEach(function (ciudad) {
                const option = document.createElement('option');
                option.value = ciudad;
                option.textContent = ciudad;

                // Si venimos de una recarga con errores, respetamos la ciudad anterior
                if (ciudadSeleccionada && ciudadSeleccionada === ciudad) {
                    option.selected = true;
                }

                ciudadSelect.appendChild(option);
            });
        }

        // Al cambiar de provincia, recargamos los municipios
        provinciaSelect.addEventListener('change', function () {
            cargarCiudades(this.value);
        });

        // Precarga automática si hay valores antiguos
        const provinciaInicial = oldProvincia || provinciaSelect.value;
        const ciudadInicial    = oldCiudad || null;

        if (provinciaInicial) {
            cargarCiudades(provinciaInicial, ciudadInicial);
        }
    });
</script>
@endpush
