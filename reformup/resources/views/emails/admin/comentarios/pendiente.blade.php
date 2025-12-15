@component('mail::message')
# Nuevo comentario pendiente de validación

Se ha recibido un nuevo comentario de un cliente y está pendiente de revisión.

@component('mail::panel')
**Cliente:** {{ $cliente->nombre ?? $cliente->name }} {{ $cliente->apellidos ?? '' }}

**Email:** {{ $cliente->email }}

**Trabajo:** #{{ $trabajo?->presupuesto?->solicitud?->titulo ?? 'Sin título' }}

@if ($trabajo->presupuesto?->solicitud?->titulo)
**Título solicitud:**  {{ $trabajo?->presupuesto?->solicitud?->titulo ?? 'Sin título' }}

@endif

**Puntuación:** {{ $comentario->puntuacion }} / 5

@if ($comentario->opinion)
**Opinión:**

{{ strip_tags($comentario->opinion) }}
@endif
@endcomponent

@if ($profesional)
**Profesional asociado:**

- Empresa: {{ $profesional->empresa }}
- Email empresa: {{ $profesional->email_empresa }}
- Teléfono: {{ $profesional->telefono_empresa }}
@endif

Puedes revisar y publicar/rechazar este comentario desde el panel de administración.

@component('mail::button', ['url' => url('/admin/comentarios')])
Ir a comentarios
@endcomponent

Gracias,  
{{ config('app.name') }}
@endcomponent
