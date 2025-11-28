@extends('layouts.main')

@section('title', 'Panel usuario - ReformUp')

@section('content')

    {{-- Navbar principal --}}
    <x-navbar />

    {{-- Sidebar usuario --}}
    <x-usuario.usuario_sidebar />
    {{-- Bienvenida --}}
    <x-usuario.user_bienvenido />
    {{-- NAV SUPERIOR SOLO MÓVIL/TABLET --}}
    <x-usuario.nav_movil active="panel" />

    {{-- Contenido principal respetando el sidebar --}}
    <div class="container-fluid main-content-with-sidebar">
        <div class="container py-4">

            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        Bienvenido tu panel <strong>usuario</strong>: resumen de solicitudes, presupuestos, trabajos y
                        comentarios.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


{{-- Si es usuario y no tiene empresa --}}
@if (!$perfilProfesional && !$isProfesional)
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 2000;">
        <div id="toastTestReformup" class="toast text-bg-primary border-0 shadow fade" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <a href="{{ route('registro.pro.empresa') }}" class="nav-link text-white">
                        <span>Si tienes una empresa, <strong>¡regístrala!</strong></span>
                    </a>
                    <i class="bi bi-building-check fs-5"></i>
                </div>

                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Cerrar"></button>
            </div>
        </div>
    </div>
@endif

{{-- Mostramos Script en la parte inferior derecha Registrar Empresa--}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const el = document.getElementById('toastTestReformup');
        if (!el) return;

        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            // FORZAMOS opciones: 10 segundos y autohide
            const toast = new bootstrap.Toast(el, {
                autohide: true,
                delay: 10000 // 10 segundos
            });
            toast.show();
        } else {
            // Fallback: se muestra y ya
            el.classList.add('show');
        }
    });
</script>
