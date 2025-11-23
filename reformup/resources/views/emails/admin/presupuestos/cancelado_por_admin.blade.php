@component('mail::message')

@if(!$esProfesional)
# Hola {{ $cliente->nombre ?? $cliente->name ?? 'cliente' }}

Tu presupuesto asociado a la solicitud:

@isset($solicitud->titulo)
**“{{ $solicitud->titulo }}”**
@else
**Solicitud #{{ $solicitud->id }}**
@endisset

ha sido **cancelado por el equipo de ReformUp**.

@component('mail::panel')
**Importe del presupuesto:** {{ number_format($presupuesto->total, 2, ',', '.') }} €  

**Estado actual:** {{ ucfirst($presupuesto->estado) }}
@endcomponent

Esto puede deberse a una revisión interna o a cambios en la gestión de la solicitud.  
Si tienes dudas, puedes responder a este correo y lo revisaremos contigo.

@if($perfilPro)
Profesional asignado: **{{ $perfilPro->empresa }}**
@endif

Saludos,  
El equipo de **ReformUp**

@else
{{-- Versión para PROFESIONAL --}}
# Hola {{ $perfilPro->empresa ?? 'profesional' }}

Un presupuesto asociado a una de tus solicitudes ha sido **cancelado por el equipo de ReformUp**.

@isset($solicitud->titulo)
**Solicitud:** “{{ $solicitud->titulo }}”
@else
**Solicitud #{{ $solicitud->id }}**
@endisset

@component('mail::panel')
**Importe del presupuesto:** {{ number_format($presupuesto->total, 2, ',', '.') }} €  

**Estado actual:** {{ ucfirst($presupuesto->estado) }}

@isset($solicitud->cliente)
**Cliente:** {{ $solicitud->cliente->nombre ?? $solicitud->cliente->name }}
{{ $solicitud->cliente->apellidos ?? '' }}
@endisset
@endcomponent

Te recomendamos revisar la solicitud y tu listado de presupuestos en tu panel de profesional para decidir los siguientes pasos.

Saludos,  
El equipo de **ReformUp**
@endif

@endcomponent
