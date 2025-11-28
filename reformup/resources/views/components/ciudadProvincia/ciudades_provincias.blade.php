@props([
    'provinciaId' => 'provincia',
    'ciudadId' => 'ciudad',
    'oldProvincia' => null,
    'oldCiudad' => null,
])

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
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

        const provinciaSelect = document.getElementById(@json($provinciaId));
        const ciudadSelect    = document.getElementById(@json($ciudadId));

        if (!provinciaSelect || !ciudadSelect) {
            return;
        }

        const oldProvincia = @json($oldProvincia);
        const oldCiudad    = @json($oldCiudad);

        function cargarCiudades(provincia, ciudadSeleccionada = null) {
            ciudadSelect.innerHTML = '';

            if (!provincia || !ciudadesPorProvincia[provincia]) {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Selecciona primero una provincia';
                ciudadSelect.appendChild(option);
                return;
            }

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Selecciona un municipio';
            ciudadSelect.appendChild(placeholder);

            ciudadesPorProvincia[provincia].forEach(function (ciudad) {
                const option = document.createElement('option');
                option.value = ciudad;
                option.textContent = ciudad;

                if (ciudadSeleccionada && ciudadSeleccionada === ciudad) {
                    option.selected = true;
                }

                ciudadSelect.appendChild(option);
            });
        }

        provinciaSelect.addEventListener('change', function () {
            cargarCiudades(this.value);
        });

        const provinciaInicial = oldProvincia || provinciaSelect.value;
        const ciudadInicial    = oldCiudad || null;

        if (provinciaInicial) {
            cargarCiudades(provinciaInicial, ciudadInicial);
        }
    });
</script>
@endpush
