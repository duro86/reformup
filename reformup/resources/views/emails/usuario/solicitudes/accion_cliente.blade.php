{{-- resources/views/emails/usuario/solicitudes/accion_cliente.blade.php --}}
@component('mail::message')
@php
    $accionHumana = $tipoAccion === 'eliminada' ? 'eliminado' : 'cancelado';
@endphp

# Solicitud {{ $accionHumana }} por el cliente

Hola {{ $perfilPro->empresa ?? 'profesional' }},

El cliente **{{ $cliente->nombre ?? $cliente->name }} {{ $cliente->apellidos ?? '' }}**
ha {{ $accionHumana }} la siguiente solicitud:

@component('mail::panel')
**Solicitud:**
@if($solicitud->titulo)
“{{ $solicitud->titulo }}”
@else
Solicitud #{{ $solicitud->id }}
@endif

**Estado actual en el sistema:** {{ ucfirst($solicitud->estado) }}
@endcomponent

@if($motivo)
**Motivo indicado por el cliente:**

> {{ $motivo }}
@endif

Puedes revisar el resto de detalles desde tu área de profesional.

@component('mail::button', ['url' => route('home')])
Ir a ReformUp
@endcomponent

Un saludo,  
El equipo de {{ config('app.name') }}
@endcomponent
