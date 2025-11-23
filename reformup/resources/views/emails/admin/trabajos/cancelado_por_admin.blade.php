@component('mail::message')

@if($paraProfesional)
# Hola {{ $perfilPro->empresa ?? 'profesional' }}

Te informamos de que un trabajo asignado a tu empresa en **ReformUp** ha sido cancelado por el equipo de administración.
@else
# Hola {{ $cliente->nombre ?? $cliente->name ?? 'cliente' }}

Te informamos de que tu trabajo en **ReformUp** ha sido cancelado por el equipo de administración.
@endif

@component('mail::panel')
@isset($solicitud)
**Solicitud:**  
“{{ $solicitud->titulo ?? ('Solicitud #' . $solicitud->id) }}”
@endisset

**Trabajo:** #{{ $trabajo->id }}

@php
    $estadoAnterior = $oldEstado ? ucfirst(str_replace('_', ' ', $oldEstado)) : null;
    $estadoActual   = ucfirst(str_replace('_', ' ', $trabajo->estado));
    $fechaIniOld    = $oldFechaIni ? $oldFechaIni->format('d/m/Y H:i') : null;
    $fechaFinOld    = $oldFechaFin ? $oldFechaFin->format('d/m/Y H:i') : null;
@endphp

@if($estadoAnterior)
- Estado anterior: {{ $estadoAnterior }}
@endif

- Estado actual: **{{ $estadoActual }}**

@if($trabajo->fecha_ini || $trabajo->fecha_fin)
---
**Fechas actuales:**

@if($trabajo->fecha_ini)
- Inicio: {{ $trabajo->fecha_ini->format('d/m/Y H:i') }}
@endif

@if($trabajo->fecha_fin)
- Fin: {{ $trabajo->fecha_fin->format('d/m/Y H:i') }}
@endif
@endif

@if($fechaIniOld || $fechaFinOld)
---
**Fechas anteriores (a efectos informativos):**

@if($fechaIniOld)
- Inicio anterior: {{ $fechaIniOld }}
@endif

@if($fechaFinOld)
- Fin anterior: {{ $fechaFinOld }}
@endif
@endif

@if($trabajo->dir_obra)
---
**Dirección de la obra:**  
{{ $trabajo->dir_obra }}
@endif
@endcomponent

@isset($presupuesto)
**Presupuesto asociado:** #{{ $presupuesto->id }}
@if(!is_null($presupuesto->total))
 — Importe: {{ number_format($presupuesto->total, 2, ',', '.') }} €
@endif  
@endisset

@isset($cliente)
**Cliente:** {{ $cliente->nombre ?? $cliente->name }} {{ $cliente->apellidos ?? '' }}
@endisset

@isset($perfilPro)
**Profesional:** {{ $perfilPro->empresa }}
@if($perfilPro->email_empresa)
 — {{ $perfilPro->email_empresa }}
@endif
@endisset

@if($paraProfesional)
Si consideras que se trata de un error o necesitas más información, puedes responder a este correo.
@else
Si crees que esta cancelación no es correcta o quieres más detalles, puedes responder a este correo y lo revisaremos contigo.
@endif

Saludos,  
El equipo de **ReformUp**

@endcomponent
