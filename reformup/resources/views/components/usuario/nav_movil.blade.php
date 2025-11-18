@props([
    // panel | solicitudes | presupuestos | trabajos | comentarios | perfil
    'active' => 'panel',
])

@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    $tienePerfilProfesional = $user && $user->perfil_Profesional()->exists();
@endphp

<div class="d-lg-none border-bottom mb-3">
    <div class="container py-2">
        <div class="d-flex flex-wrap gap-2 justify-content-center">

            @php
                $items = [
                    'panel' => ['label' => 'Panel', 'route' => route('usuario.dashboard')],
                    'solicitudes' => ['label' => 'Solicitudes', 'route' => route('usuario.solicitudes.index')],
                    'presupuestos' => ['label' => 'Presupuestos', 'route' => route('usuario.presupuestos.index')],
                    'trabajos' => ['label' => 'Trabajos', 'route' => '#'],
                    'comentarios' => ['label' => 'Comentarios', 'route' => '#'],
                    'perfil' => ['label' => 'Mi perfil', 'route' => route('usuario.perfil')],
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

            {{-- Botón para ir al panel profesional si tiene perfil --}}
            @if ($tienePerfilProfesional)
                <a href="{{ route('profesional.dashboard') }}" class="btn btn-sm btn-outline-primary">
                    Panel profesional
                </a>
            @endif

        </div>
    </div>
</div>
