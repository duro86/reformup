@component('mail::message')

{{-- Si el correo es para el CLIENTE --}}
@if($cliente)
# Hola {{ $cliente->nombre ?? $cliente->name }}

Tu solicitud en **ReformUp** ha sido revisada y modificada por nuestro equipo.

@isset($solicitud->titulo)
**Título de la solicitud:**  
“**{{ $solicitud->titulo }}**”
@endisset

@isset($solicitud->descripcion)
**Descripción actualizada:**  

{{ strip_tags($solicitud->descripcion) }}
@endisset

@component('mail::panel')
@isset($oldEstado)
**Estado anterior:** {{ ucfirst(str_replace('_', ' ', $oldEstado)) }}
@endisset

@isset($newEstado)
**Estado actual:** {{ ucfirst(str_replace('_', ' ', $newEstado)) }}
@else
**Estado actual:** {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
@endisset

@isset($solicitud->ciudad)
**Ubicación:** {{ $solicitud->ciudad }} ({{ $solicitud->provincia }})
@endisset
@endcomponent

Nuestro objetivo es mantener la plataforma ordenada y clara para clientes y profesionales.  
Si tienes cualquier duda sobre esta modificación, puedes responder a este correo y lo revisaremos contigo.

@if($perfilPro)
Profesional asignado: **{{ $perfilPro->empresa }}**
@endif

Saludos,  
El equipo de **ReformUp**

@else
{{-- Si el correo es para el PROFESIONAL --}}

# Hola {{ $perfilPro->empresa ?? 'profesional' }}

Se ha actualizado una solicitud vinculada a tu perfil en **ReformUp**.

@isset($solicitud->titulo)
**Título de la solicitud:**  
**{{ $solicitud->titulo }}**
@endisset

@if($solicitud->cliente)
**Cliente:** {{ $solicitud->cliente->nombre }} {{ $solicitud->cliente->apellidos }}  
Email: {{ $solicitud->cliente->email }}  
@isset($solicitud->cliente->telefono)
Teléfono: {{ $solicitud->cliente->telefono }}
@endisset
@endif

@component('mail::panel')
@isset($oldEstado)
**Estado anterior:** {{ ucfirst(str_replace('_', ' ', $oldEstado)) }}
@endisset

@isset($newEstado)
**Estado actual:** {{ ucfirst(str_replace('_', ' ', $newEstado)) }}
@else
**Estado actual:** {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
@endisset

@isset($solicitud->presupuesto_max)
**Presupuesto máximo estimado:** {{ number_format($solicitud->presupuesto_max, 2, ',', '.') }} €
@endisset
@endcomponent

Te recomendamos revisar los detalles de la solicitud en tu panel de profesional para valorar los siguientes pasos
(por ejemplo, ajustar un presupuesto o el estado del trabajo si ya existe).

@isset($trabajo)
Trabajo relacionado: **#{{ $trabajo->id }}**
@endisset

Gracias por trabajar con **ReformUp**.

Saludos,  
El equipo de **ReformUp**
@endif

@endcomponent
