@component('mail::message')
# Presupuesto rechazado

El cliente **{{ $cliente->nombre ?? $cliente->name }}** ha rechazado tu presupuesto.

@component('mail::panel')
**Solicitud:** {{ $presupuesto->solicitud->titulo ?? 'Sin título' }}

@if($motivo)
**Motivo proporcionado por el cliente:**

> {{ $motivo }}
@endif
@endcomponent

Si lo deseas, puedes responder al cliente para ofrecerle alguna alternativa o aclaración.

Gracias por usar {{ config('app.name') }}.
@endcomponent
