@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;

    $user = Auth::user();
    $perfil = $user?->perfil_Profesional;

    // Nombre a mostrar: empresa si hay perfil, si no nombre de usuario, si no "Profesional"
    $nombre = $perfil?->empresa ?? ($user?->nombre ?? 'Profesional');

    // Avatar por defecto
    $defaultAvatarPro = asset('img/User/avatarPro/avatarHombrePro.png');

    // Avatar real si el perfil tiene uno en storage
    if ($perfil?->avatar) {
        $avatarUrl = Storage::url($perfil->avatar); // /storage/...
    } else {
        $avatarUrl = $defaultAvatarPro;
    }
@endphp

<div class="w-100 border-bottom text-white bg-pro-primary">
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
                <img src="{{ $avatarUrl }}" alt="avatar profesional" class="rounded-circle"
                    style="width: 36px; height: 36px; object-fit: cover;">

                @if ($user?->hasRole('usuario'))
                    <a  href="{{ route('usuario.dashboard') }}"
                        class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1">
                        Ir a Panel usuario
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
