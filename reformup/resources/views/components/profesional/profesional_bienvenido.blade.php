@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    $perfil = $user?->perfil_Profesional;

    $nombre = $perfil?->empresa ?? ($user?->nombre ?? 'Profesional');

    // Avatar del perfil profesional
    $avatarUrl = $perfil?->avatar ? asset('storage/' . $perfil->avatar) : null;

    $bgColor = '#E5F0FF'; // azulito suave para diferenciar
@endphp

<div class="w-100 border-bottom" style="background-color: {{ $bgColor }};">
    <div class="container-fluid">
        <div class="d-flex flex-column flex-sm-row align-items-end justify-content-end py-2 gap-2">
            <div class="text-start">
                <div class="small text-muted mb-1 fade-zoom">
                    Panel profesional
                </div>
                <div class="fw-semibold">
                    Bienvenido, {{ $nombre }}
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if ($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="avatar profesional" class="rounded-circle"
                        style="width: 36px; height: 36px; object-fit: cover;">
                @else
                    <i class="bi bi-person-workspace" style="font-size: 1.9rem;"></i>
                @endif
                {{-- Botón para acceder al panel profesional --}}
                    <a href="{{ route('usuario.dashboard') }}"
                        class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1">
                        Ir a Panel usuario
                    </a>
            </div>
        </div>
    </div>
</div>
