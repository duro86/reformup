@component('mail::message')
# Tu solicitud ha sido cancelada

Hola {{ $cliente->nombre ?? $cliente->name }},

Tu solicitud de presupuesto:

**“{{ $solicitud->titulo }}”**  
(ID #{{ $solicitud->id }})

ha sido **cancelada** por el equipo de ReformUp.

---

## Estado de tu solicitud

- Estado de la solicitud: **{{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}**

@isset($perfilPro)
- Profesional asociado: **{{ $perfilPro->empresa }}**
@endisset

---

## Presupuesto asociado

@isset($presupuesto)
- Referencia presupuesto: **#{{ $presupuesto->id }}**
- Estado del presupuesto: **{{ ucfirst(str_replace('_', ' ', $presupuesto->estado)) }}**
- Importe estimado:
    @if(!is_null($presupuesto->total))
    **{{ number_format($presupuesto->total, 2, ',', '.') }} €**
    @else
    _No indicado_
    @endif
@else
_No había ningún presupuesto asociado a esta solicitud._
@endisset

---

## Trabajo asociado

@isset($trabajo)
- Referencia trabajo: **#{{ $trabajo->id }}**
- Estado del trabajo: **{{ ucfirst(str_replace('_', ' ', $trabajo->estado)) }}**
- Dirección de la obra:
    @if($trabajo->dir_obra)
    {{ $trabajo->dir_obra }}
    @else
    _No indicada_
    @endif
@else
_No había ningún trabajo asociado a esta solicitud._
@endisset

---

Si necesitas más información o crees que se trata de un error, puedes responder a este correo
y revisaremos tu caso.

Gracias por usar **ReformUp**.

@endcomponent
