@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;

    $user = Auth::user();
    $nombre = $user?->nombre ?? 'Admin';

    if ($user?->avatar) {
        $avatarUrl = Storage::disk('public')->url($user->avatar);
        // dd($avatarUrl);   // si quieres ver qué URL genera exactamente
    } else {
        $avatarUrl = asset('img/admin/avatar_admin_dibujo.png');
    }

    $bgColor = '#FFF3CD'; // amarillito suave de "barra de control"
@endphp

<div class="w-100 border-bottom" style="background-color: {{ $bgColor }};">
    <div class="container-fluid">
        <div class="d-flex flex-column flex-sm-row align-items-end justify-content-end py-2 gap-2">

            <div class="text-start">
                <div class="small text-muted mb-1">
                    Panel administrador
                </div>
                <div class="fw-semibold">
                    Bienvenido, {{ $nombre }}
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if ($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="avatar admin" class="rounded-circle"
                        style="width: 36px; height: 36px; object-fit: cover;">
                @else
                    <i class="bi bi-shield-lock" style="font-size: 1.9rem;"></i>
                @endif
            </div>

        </div>
    </div>
</div>
