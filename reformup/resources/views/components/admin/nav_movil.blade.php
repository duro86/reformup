{{-- resources/views/components/admin/nav_movil.blade.php --}}

@props([
    // panel | usuarios | profesionales | solicitudes | presupuestos | trabajos | comentarios | perfil
    'active' => 'panel',
])

<div class="d-lg-none border-bottom mb-3">
    <div class="container py-2">
        <div class="d-flex flex-wrap gap-2 justify-content-center">

            @php
                $items = [
                    'panel' => ['label' => 'Panel', 'route' => route('admin.dashboard')],
                    'usuarios' => ['label' => 'Usuarios', 'route' => route('admin.usuarios')],
                    'profesionales' => ['label' => 'Profesionales', 'route' => route('admin.profesionales')],
                    'solicitudes' => ['label' => 'Solicitudes', 'route' => route('admin.solicitudes')],
                    'presupuestos' => ['label' => 'Presupuestos', 'route' => route('admin.presupuestos')],
                    'trabajos' => ['label' => 'Trabajos', 'route' => route('admin.trabajos')],
                    'comentarios' => ['label' => 'Comentarios', 'route' => route('admin.comentarios')],
                    'oficios' => ['label' => 'Oficios', 'route' => route('admin.oficios')],

                    'perfil' => ['label' => 'Mi perfil', 'route' => route('admin.perfil')],
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

        </div>
    </div>
</div>
