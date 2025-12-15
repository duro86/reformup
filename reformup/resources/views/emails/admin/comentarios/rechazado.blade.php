@component('mail::message')
# Tu comentario no ha podido ser publicado

Hola {{ $cliente->nombre ?? ($cliente->name ?? $cliente->email) }},

Tras revisar tu comentario, hemos decidido **no publicarlo** en la plataforma.

@isset($solicitud)
## Detalles de la solicitud

**Título:** {{ $solicitud->titulo }}

@isset($solicitud->descripcion)
**Descripción:**

{{ strip_tags($solicitud->descripcion) }}
@endisset
@endisset

@isset($trabajo)
**Trabajo asociado:** #{{ $trabajo->presupuesto->solicitud->titulo }}
@endisset

---

## Estado del comentario

- **Puntuación enviada:** {{ $comentario->puntuacion }} / 5  
- **Opinión:**
@if ($comentario->opinion)
{{ strip_tags($comentario->opinion) }}
@else
_Sin texto de opinión, solo puntuación._
@endif

Este comentario se ha marcado como **rechazado** y **no podrá ser editado ni volver a enviarse en su forma actual**.

---

## ¿Qué puedes hacer ahora?

Si crees que se trata de un error o deseas que revisemos el caso con más detalle,  
puedes responder directamente a este correo y nuestro equipo lo volverá a valorar.

@isset($perfilPro)
---

## Profesional valorado

**{{ $perfilPro->empresa }}**

{{ $perfilPro->email_empresa }}

@isset($perfilPro->telefono_empresa)
Teléfono: {{ $perfilPro->telefono_empresa }}
@endisset
@endisset

Gracias por tu comprensión y por formar parte de **{{ config('app.name') }}**.

@endcomponent
