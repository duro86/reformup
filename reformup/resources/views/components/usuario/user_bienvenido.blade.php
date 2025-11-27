@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    $nombre = $user?->nombre ?? 'Invitado';
    $defaultAvatar = asset('img\User\avatarUser\avatar_user_Hombre.webp');

    $isProfesional = $user?->hasRole('profesional');
    $perfilProfesional = $isProfesional ? $user->perfil_Profesional()->first() : null;
@endphp

<div class="w-100 border-bottom bg-light">
    <div class="container-fluid">
        <div class="d-flex flex-column flex-sm-row align-items-end justify-content-end py-2 gap-2">
            <div class="text-end">
                <div class="small text-muted mb-1">
                    Panel usuario
                </div>
                <div class="fw-semibold">
                    Bienvenido, {{ $nombre }} {{ $user->apellidos }}
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">

                @if ($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="avatar" class="rounded-circle"
                        style="width:40px;height:40px;object-fit:cover">
                @else
                    <img src="{{ $defaultAvatar }}" alt="avatar por defecto" class="rounded-circle"
                        style="width:40px;height:40px;object-fit:cover">
                @endif

                {{-- Botón para acceder al panel profesional --}}
                @if ($isProfesional && $perfilProfesional)
                    <a href="{{ route('profesional.dashboard') }}"
                        class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1">
                        Ir a Panel profesional
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>


