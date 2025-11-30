
@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;

    $user = Auth::user();
    $nombre = $user?->nombre ?? 'Usuario';

    // Avatar por defecto
    $defaultAvatarUser = asset('img/User/avatarUser/avatar_default.webp');

    // Avatar real si el perfil tiene uno en storage
    if ($user?->avatar) {
        $avatarUrl = Storage::url($user->avatar); // /storage/...
    } else {
        $avatarUrl = $defaultAvatarUser;
    }
@endphp

<div class="w-100 border-bottom bg-user-primary text-white">
    <div class="container-fluid">
        <div class="d-flex flex-column flex-sm-row align-items-end justify-content-end py-2 gap-2">
            <div class="text-start">
                <div class="small text-light mb-1 fade-zoom">
                    Panel usuario
                </div>
                <div class="fw-semibold">
                    Bienvenido, {{ $nombre }}
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <img src="{{ $avatarUrl }}"
                     alt="avatar usuario"
                     class="rounded-circle"
                     style="width: 36px; height: 36px; object-fit: cover;">

                @if($user?->hasRole('profesional') && $user?->perfil_Profesional)
                    <a href="{{ route('profesional.dashboard') }}"
                       class="btn btn-outline-light btn-sm rounded-pill px-3 py-1">
                        Ir a Panel profesional
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
