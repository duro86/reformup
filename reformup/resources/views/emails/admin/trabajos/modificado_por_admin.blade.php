@component('mail::message')

@if($paraProfesional)
# Hola {{ $perfilPro->empresa ?? 'profesional' }}

Se ha modificado un trabajo asociado a la solicitud
@isset($solicitud)
“{{ $solicitud->titulo ?? ('Solicitud #' . $solicitud->id) }}”
@endisset
en la plataforma **ReformUp**.
@else
# Hola {{ $cliente->nombre ?? $cliente->name ?? 'cliente' }}

Hemos actualizado la información de tu trabajo asociado a la solicitud
@isset($solicitud)
“{{ $solicitud->titulo ?? ('Solicitud #' . $solicitud->id) }}”
@endisset
en **ReformUp**.
@endif

@php
    // Normalizamos fechas antiguas a string (o null)
    $fechaIniOld = $oldFechaIni ? $oldFechaIni->format('d/m/Y H:i') : null;
    $fechaFinOld = $oldFechaFin ? $oldFechaFin->format('d/m/Y H:i') : null;

    $fechaIniNew = $trabajo->fecha_ini ? $trabajo->fecha_ini->format('d/m/Y H:i') : null;
    $fechaFinNew = $trabajo->fecha_fin ? $trabajo->fecha_fin->format('d/m/Y H:i') : null;

    // ¿Merece la pena mostrar “estado anterior”?
    $mostrarEstadoAnterior = $estadoHumanoOld && ($estadoHumanoOld !== $estadoHumanoNew);

    // ¿Han cambiado las fechas?
    $hayCambioFechaIni = $fechaIniOld && ($fechaIniOld !== $fechaIniNew);
    $hayCambioFechaFin = $fechaFinOld && ($fechaFinOld !== $fechaFinNew);
    $hayFechasAnteriores = $hayCambioFechaIni || $hayCambioFechaFin;

    // ¿Ha cambiado la dirección?
    $mostrarDirAnterior = $oldDirObra && ($oldDirObra !== $trabajo->dir_obra);
@endphp

@component('mail::panel')
**Trabajo #{{ $trabajo->id }}**

@if($mostrarEstadoAnterior)
**Estado anterior:** {{ ucfirst($estadoHumanoOld) }}  
@endif

**Estado actual:** {{ ucfirst($estadoHumanoNew) }}

@if($fechaIniNew || $fechaFinNew)
<br>
@endif

@if($fechaIniNew)
**Fecha inicio actual:** {{ $fechaIniNew }}  
@endif

@if($fechaFinNew)
**Fecha fin actual:** {{ $fechaFinNew }}  
@endif

@if($hayFechasAnteriores)
<br>
**Fechas anteriores (solo a modo informativo):**  
    @if($hayCambioFechaIni)
- Inicio anterior: {{ $fechaIniOld }}  
    @endif
    @if($hayCambioFechaFin)
- Fin anterior: {{ $fechaFinOld }}  
    @endif
@endif

@if($trabajo->dir_obra || $mostrarDirAnterior)
<br>
**Dirección de la obra**  

    @if($mostrarDirAnterior)
- Dirección anterior: {{ $oldDirObra }}  
    @endif

    @if($trabajo->dir_obra)
- Dirección actual: {{ $trabajo->dir_obra }}  
    @endif
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
