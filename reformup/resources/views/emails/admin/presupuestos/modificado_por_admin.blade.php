@component('mail::message')
# @if($isProfesional)
Hola {{ $perfilPro->empresa ?? 'profesional' }}
@else
Hola {{ $cliente->nombre ?? $cliente->name ?? 'usuario' }}
@endif

Te informamos de que **uno de los presupuestos asociados a una solicitud en ReformUp** ha sido
revisado y modificado por el equipo de administración de la plataforma.

@isset($solicitud)
@component('mail::panel')
**Solicitud:**  
@isset($solicitud->titulo)
“{{ $solicitud->titulo }}”
@else
Referencia #{{ $solicitud->id }}
@endisset

**Estado actual de la solicitud:** {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
@endcomponent
@endisset

A continuación te mostramos un resumen de los cambios realizados en el presupuesto:

@component('mail::panel')
**Estado anterior:** {{ $oldEstado ?? '-' }}  
**Estado actual:** {{ $presupuesto->estado }}

**Importe anterior:**  
@isset($oldTotal)
{{ number_format($oldTotal, 2, ',', '.') }} €
@else
No disponible
@endisset  

**Importe actual:**  
{{ number_format($presupuesto->total, 2, ',', '.') }} €

@isset($oldNotas)
**Notas anteriores:**  
"{{ $oldNotas }}"
@endisset

@isset($presupuesto->notas)
**Notas actuales:**  
"{{ $presupuesto->notas }}"
@endisset
@endcomponent

@if($isProfesional)
Este ajuste puede deberse a una corrección interna, aclaración de condiciones o actualización de la información
aportada por el cliente.  
Te recomendamos revisar el presupuesto en tu panel de profesional de ReformUp.
@else
Estos cambios se han hecho para que el presupuesto se ajuste mejor a la realidad del trabajo o a las condiciones
acordadas.  
Puedes revisar el presupuesto actualizado entrando en tu área de cliente en ReformUp.
@endif

Si consideras que alguna modificación no es correcta o quieres hacer una aclaración, puedes responder a este correo
y nuestro equipo revisará el caso.

Saludos,  
El equipo de **ReformUp**
@endcomponent
