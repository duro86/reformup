<div class="d-flex flex-column flex-sm-row justify-content-end align-items-center p-2" 
     style="background-color: #f8f9fa;">
    <span class="me-0 me-sm-2 mb-2 mb-sm-0 text-end w-100">Bienvenido, {{ Auth::user()->nombre ?? 'Invitado' }}</span>
    @if (Auth::user() && Auth::user()->avatar)
        <img src="{{ asset('img/admin/avatar_admin_dibujo.png') }}" alt="avatar" 
             class="rounded-circle mx-sm-3" 
             style="width: 35px; height: 35px; object-fit: cover;">
    @else
        <i class="bi bi-person-circle" style="font-size: 1.8rem;"></i>
    @endif
</div>



