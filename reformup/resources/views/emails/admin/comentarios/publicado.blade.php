@component('mail::message')
# Tu comentario ha sido publicado

Hola {{ $cliente->nombre ?? ($cliente->name ?? $cliente->email) }},

Tu comentario ha sido **publicado** y ya es visible en la plataforma.

@isset($solicitud)
## Detalles de la solicitud

**Título:** {{ $solicitud->titulo }}

@isset($solicitud->descripcion)
**Descripción:**

{{ strip_tags($comentario->descripcion) }}
@endisset
@endisset

@isset($trabajo)
**Trabajo asociado:** #{{ $trabajo->presupuesto->solicitud->titulo }}
@endisset

---

## Detalles de tu comentario

- **Puntuación:** {{ $comentario->puntuacion }} / 5  
- **Opinión:**
@if ($comentario->opinion)
{{ strip_tags($comentario->opinion) }}
@else
_Sin texto de opinión, solo puntuación._
@endif

@isset($perfilPro)
---

## Profesional valorado

**{{ $perfilPro->empresa }}**

{{ $perfilPro->email_empresa }}

@isset($perfilPro->telefono_empresa)
Teléfono: {{ $perfilPro->telefono_empresa }}
@endisset
@endisset

---

Gracias por ayudar a otros usuarios con tu valoración.  
**{{ config('app.name') }}**
@endcomponent
