@component('mail::message')
@php
    $verbo = $tipoAccion === 'eliminado' ? 'eliminado' : 'cancelado';
@endphp

@if($esProfesional)
# Presupuesto {{ $verbo }}

Hola {{ $perfilPro->empresa ?? 'profesional' }},

Te informamos de que el presupuesto **#{{ $presupuesto->id }}**
relacionado con la solicitud
@if($solicitud?->titulo)
"{{ $solicitud->titulo }}"
@else
# Sin nombre
@endif
ha sido **{{ $verbo }}** por el administrador de ReformUp.

@if($tipoAccion === 'eliminado')
Además, el presupuesto y, en su caso, el trabajo asociado han sido eliminados del sistema.
@endif

@else
# Tu presupuesto ha sido {{ $verbo }}

Hola {{ $cliente->nombre ?? 'cliente' }},

Tu presupuesto **#{{ $presupuesto->id }}**
para la solicitud
@if($solicitud?->titulo)
"{{ $solicitud->titulo }}"
@else
# Sin titulo
@endif
ha sido **{{ $verbo }}** por el equipo de ReformUp.

@if($tipoAccion === 'eliminado')
El presupuesto y, si existía, el trabajo asociado han sido eliminados del sistema.
Podrás solicitar o aceptar nuevos presupuestos desde tu área privada.
@endif

@endif

@component('mail::button', ['url' => route('home')])
Ir a ReformUp
@endcomponent

Un saludo,  
{{ config('app.name') }}
@endcomponent
