# ReformUp
**Proyecto Final – Desarrollo de Aplicaciones Web (DAW)**

ReformUp es una plataforma web desarrollada como proyecto final del ciclo DAW. Su objetivo es conectar a clientes que desean realizar reformas en su vivienda o negocio con profesionales del sector (albañiles, fontaneros, electricistas, etc.), facilitando todo el proceso de forma clara, trazable y segura.

La aplicación permite publicar solicitudes de reforma, recibir y comparar presupuestos, contratar trabajos y valorar los resultados finales, centralizando todo el flujo en una única plataforma.

---

##  Funcionalidades

- Exploración pública de profesionales con búsqueda y filtros por oficio y ciudad.
- Sistema de autenticación basado en Laravel Sanctum (SPA / cookies).
- Gestión de roles y permisos mediante Spatie Laravel Permission (admin, cliente y profesional).
- Flujo completo de trabajo:
  
  **Solicitud → Presupuestos → Trabajo → Reseña**
- Gestión de contenidos multimedia (imágenes y vídeos de obras).
- Exportación de datos a PDF y Excel.
- Buscador avanzado y paginación.
- Envío de correos electrónicos transaccionales (entorno de desarrollo con Mailpit).
- API REST pública y privada (protegida con tokens Sanctum para uso externo o apps móviles).

---

##  Stack técnico

### Backend
- Laravel (PHP 8+)
- Arquitectura MVC
- Eloquent ORM
- Policies y Middlewares personalizados

### Frontend
- Vue 3
- Vite
- Bootstrap 5

### Base de datos
- MySQL

### Infraestructura (entorno de desarrollo)
- Docker Compose
  - MySQL
  - Mailpit
  - phpMyAdmin

### Autenticación y seguridad
- Laravel Sanctum
- Spatie Laravel Permission

---

##  Requisitos

### Opción recomendada
- Docker Desktop  
  - En Windows: WSL2 habilitado

### Opción alternativa (sin Docker)
- PHP 8.x
- Composer
- MySQL
- Node.js + npm

---

##  Datos de prueba

El proyecto incluye migraciones y seeders para generar datos de ejemplo:
- Usuarios (cliente, profesional y administrador)
- Profesionales y oficios
- Solicitudes, presupuestos, trabajos y comentarios

Esto permite reproducir el entorno completo sin depender de una base de datos previa.

---

##  API para profesionales (Sanctum)

La aplicación incluye una API privada para profesionales, pensada para futuras aplicaciones móviles.

El acceso se realiza mediante tokens personales generados desde el panel del profesional, permitiendo consultar los trabajos asociados a su perfil sin necesidad de iniciar sesión mediante formulario.

---

##  Repositorio del proyecto
https://github.com/duro86/reformup

---

##  Docker Hub
https://hub.docker.com/repository/docker/adurama436/reformup-app

