@component('mail::message')

@if($paraProfesional)
# Hola {{ $perfilPro->empresa ?? 'profesional' }}

Se ha modificado un trabajo asociado a la solicitud
@isset($solicitud)
“**{{ $solicitud->titulo ?? ('Solicitud #' . $solicitud->id) }}**”
@endisset
en la plataforma **ReformUp**.
@else
# Hola {{ $cliente->nombre ?? $cliente->name ?? 'cliente' }}

Hemos actualizado la información de tu trabajo asociado a la solicitud
@isset($solicitud)
“**{{ $solicitud->titulo ?? ('Solicitud #' . $solicitud->id) }}**”
@endisset
en **ReformUp**.
@endif

@component('mail::panel')
**Trabajo #{{ $trabajo->id }}**

@isset($oldEstado)
**Estado anterior:** {{ ucfirst(str_replace('_', ' ', $oldEstado)) }}  
@endisset

**Estado actual:** {{ ucfirst(str_replace('_', ' ', $trabajo->estado)) }}

@php
    $fechaIniOld = $oldFechaIni ? $oldFechaIni->format('d/m/Y H:i') : null;
    $fechaFinOld = $oldFechaFin ? $oldFechaFin->format('d/m/Y H:i') : null;
@endphp

@isset($trabajo->fecha_ini)
- Fecha inicio actual: {{ $trabajo->fecha_ini->format('d/m/Y H:i') }}
@endisset

@isset($trabajo->fecha_fin)
- Fecha fin actual: {{ $trabajo->fecha_fin->format('d/m/Y H:i') }}
@endisset

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

@if($oldDirObra || $trabajo->dir_obra)
---
**Dirección de la obra**

@isset($oldDirObra)
- Dirección anterior: {{ $oldDirObra }}
@endisset

@isset($trabajo->dir_obra)
- Dirección actual: {{ $trabajo->dir_obra }}
@endisset
@endif
@endcomponent

@isset($presupuesto)
**Presupuesto asociado:** #{{ $presupuesto->id }}
@if(!is_null($presupuesto->total))
 — Importe: {{ number_format($presupuesto->total, 2, ',', '.') }} €
@endif  
@endisset

@isset($perfilPro)
**Profesional:** {{ $perfilPro->empresa }}
@if($perfilPro->email_empresa)
 — {{ $perfilPro->email_empresa }}
@endif  
@endisset

@isset($cliente)
**Cliente:** {{ $cliente->nombre ?? $cliente->name }} {{ $cliente->apellidos ?? '' }}
@endisset

@if($paraProfesional)
Si tienes cualquier duda sobre este cambio, puedes responder a este correo
o revisar el detalle del trabajo desde tu panel de profesional.
@else
Si algo de esta actualización no te encaja, puedes responder a este correo
y lo revisaremos contigo.
@endif

Saludos,  
El equipo de **ReformUp**

@endcomponent
