@component('mail::message')
@php
    $fechaIni = $trabajo->fecha_ini ? $trabajo->fecha_ini->format('d/m/Y H:i') : 'Sin inicio registrado';
    $fechaFin = $trabajo->fecha_fin ? $trabajo->fecha_fin->format('d/m/Y H:i') : 'Sin fin registrado';
@endphp

# Trabajo cancelado por el cliente

Hola {{ $perfilPro->empresa ?? $perfilPro->email_empresa }},

El cliente ha cancelado el trabajo asociado a uno de tus presupuestos.

- ID del trabajo: **{{ $trabajo->presupuesto->solicitud->titulo }}**
- Fecha de inicio: **{{ $fechaIni }}**
- Fecha de fin: **{{ $fechaFin }}**
@isset($trabajo->dir_obra)
- Dirección de la obra: {{ $trabajo->dir_obra }}
@endisset

---

## Datos del cliente

- Nombre: **{{ $cliente->nombre ?? ($cliente->name ?? $cliente->email) }}**
- Email: {{ $cliente->email }}

@isset($motivo)
**Motivo indicado por el cliente:**

> {{ $motivo }}
@endisset

---

Si necesitas seguir con la reforma, puedes contactar con el cliente para aclarar la situación.

Gracias,  
{{ config('app.name') }}
@endcomponent
