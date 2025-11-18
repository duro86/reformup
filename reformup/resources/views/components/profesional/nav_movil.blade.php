@props([
    // panel | solicitudes | presupuestos | trabajos | comentarios | perfil
    'active' => 'panel',
])

<div class="d-lg-none border-bottom mb-3">
    <div class="container py-2">
        <div class="d-flex flex-wrap gap-2 justify-content-center">

            @php
                $items = [
                    'panel' => ['label' => 'Panel', 'route' => route('profesional.dashboard')],
                    'solicitudes' => ['label' => 'Solicitudes', 'route' => route('profesional.solicitudes.index')],
                    'presupuestos' => ['label' => 'Presupuestos', 'route' => route('profesional.presupuestos.index')],
                    'trabajos' => ['label' => 'Trabajos', 'route' => '#'],
                    'comentarios' => ['label' => 'Comentarios', 'route' => '#'],
                    'perfil' => ['label' => 'Mi perfil', 'route' => route('profesional.perfil')],
                ];
            @endphp

            @foreach ($items as $key => $item)
                @php
                    $isActive = $active === $key;
                    $classes = $isActive ? 'btn btn-sm btn-success' : 'btn btn-sm btn-outline-success';
                @endphp

                <a href="{{ $item['route'] }}" class="{{ $classes }}">
                    {{ $item['label'] }}
                </a>
            @endforeach

            {{-- Siempre mostramos botón al panel usuario,
               porque en tu flujo todo profesional es también usuario --}}
            <a href="{{ route('usuario.dashboard') }}" class="btn btn-sm btn-outline-primary">
                Panel usuario
            </a>

        </div>
    </div>
</div>
