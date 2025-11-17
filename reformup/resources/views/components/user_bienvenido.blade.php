@php
    $user   = Auth::user();
    $roles  = $user ? $user->getRoleNames() : collect();

    $isAdmin       = $roles->contains('admin');
    $isProfesional = $roles->contains('profesional');

    // Fondo distinto si es profesional
    $bgColor = $isProfesional ? '#E9F5DB' : '#f8f9fa';

    // Decidimos qué avatar usar
    $avatarUrl = null;

    // 1) Si es profesional e tiene perfil con avatar, usamos el de la empresa
    if ($isProfesional && $user) {
        $perfilProfesional = $user->perfil_Profesional()->first();
        if ($perfilProfesional && $perfilProfesional->avatar) {
            $avatarUrl = asset('storage/' . $perfilProfesional->avatar);
        }
    }

    // 2) Si no hemos encontrado avatar profesional, usamos el avatar de usuario (si existe)
    if (! $avatarUrl && $user && $user->avatar) {
        // Asumiendo que guardas la ruta relativa en storage (imagenes/avatarUser/...)
        $avatarUrl = asset('storage/' . $user->avatar);
    }

    // 3) Si sigue sin avatar y es admin, usamos el dibujo fijo de admin
    if (! $avatarUrl && $isAdmin) {
        $avatarUrl = asset('img/admin/avatar_admin_dibujo.png');
    }
@endphp

<div class="d-flex flex-column flex-sm-row justify-content-end align-items-center p-2"
     style="background-color: {{ $bgColor }};">
    <span class="me-0 me-sm-2 mb-2 mb-sm-0 text-end w-100">
        Bienvenido, {{ $user->nombre ?? 'Invitado' }}
    </span>

    @if ($avatarUrl)
        <img src="{{ $avatarUrl }}"
             alt="avatar"
             class="rounded-circle mx-sm-3"
             style="width: 35px; height: 35px; object-fit: cover;">
    @else
        <i class="bi bi-person-circle" style="font-size: 1.8rem;"></i>
    @endif
</div>
