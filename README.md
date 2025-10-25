# reformup
Proyecto final daw ReformUp
Plataforma web que conecta clientes que quieren realizar reformas del hogar con profesionales (albañiles, fontaneros, electricistas…). Permite publicar solicitudes, recibir y comparar presupuestos, contratar trabajos y valorar resultados, con flujo transparente y trazable.

# Funcionalidades

Exploración pública de profesionales con búsqueda y filtros (oficio, ciudad).

Autenticación con Laravel Sanctum (SPA/cookies).

Roles y permisos con Spatie (admin, cliente, profesional).

Ciclo Solicitud → Presupuestos → Trabajo → Reseña.

Gestión de medios (fotos/vídeos) de obras.

Exportación PDF/Excel, paginación, buscador.

Emails transaccionales (desarrollo con Mailpit).

# Stack técnico

Backend: Laravel (PHP 8+), MVC, Policies/Middlewares, Eloquent.

Frontend: Vue 3 + Vite + Bootstrap 5.

BD: MySQL.

Infra (dev): Docker Compose (Nginx, PHP-FPM, MySQL, Mailpit, phpMyAdmin).

Auth: Laravel Sanctum + Spatie Laravel-Permission.

# Requisitos

Docker Desktop (WSL2 en Windows).

(Opcional) PHP 8.x y Composer si quieres ejecutar Laravel fuera de Docker.