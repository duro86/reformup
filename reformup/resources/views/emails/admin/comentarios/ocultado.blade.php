@component('mail::message')
# Tu comentario ha dejado de ser visible

Hola {{ $cliente->nombre ?? ($cliente->name ?? $cliente->email) }},

Hemos revisado tu comentario y actualmente **ya no está visible** en la plataforma.

@isset($solicitud)
## Detalles de la solicitud

**Título:** {{ $solicitud->titulo }}

@isset($solicitud->descripcion)
**Descripción:**

> {{ $solicitud->descripcion }}
@endisset
@endisset

@isset($trabajo)
**Trabajo asociado:** #{{ $trabajo->id }}
@endisset

---

## ¿Por qué se ha ocultado?

En ReformUp revisamos los comentarios para mantener un entorno útil y respetuoso para todos.  
Tu valoración se ha ocultado por alguna de estas razones habituales:

- Lenguaje inadecuado o poco respetuoso.  
- Inclusión de datos personales o sensibles.  
- Contenido que puede no ajustarse a nuestras normas de uso.

Esto **no afecta a tu cuenta**, ni a tu capacidad para seguir usando la plataforma o valorar otros trabajos.

---

## Detalles de tu comentario

- **Puntuación enviada:** {{ $comentario->puntuacion }} / 5  
- **Opinión:**
@if ($comentario->opinion)
> {{ $comentario->opinion }}
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

Si crees que se trata de un error o quieres más información, puedes responder a este correo y revisaremos tu caso.

Gracias por formar parte de **{{ config('app.name') }}**.

@endcomponent
