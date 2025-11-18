@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();

    $nombre = $user?->nombre ?? 'Invitado';

    // Avatar usuario (si lo tienes guardado en storage)
    $avatarUrl = $user?->avatar ? asset('storage/' . $user->avatar) : null;

    $bgColor = '#E9F5DB'; // verde suave
@endphp

<div class="w-100 border-bottom" style="background-color: {{ $bgColor }};">
    <div class="container-fluid">
        <div class="d-flex flex-column flex-sm-row align-items-end justify-content-end py-2 gap-2">

            <div class="text-end">
                <div class="small text-muted mb-1">
                    Panel usuario
                </div>
                <div class="fw-semibold">
                    Bienvenido, {{ $nombre }}
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if ($avatarUrl)
                    <img src="{{ $avatarUrl }}"
                         alt="avatar usuario"
                         class="rounded-circle"
                         style="width: 36px; height: 36px; object-fit: cover;">
                @else
                    <i class="bi bi-person-circle" style="font-size: 1.9rem;"></i>
                @endif
            </div>

        </div>
    </div>
</div>
