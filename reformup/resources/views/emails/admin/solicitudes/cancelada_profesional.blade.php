@component('mail::message')
# Una solicitud asignada ha sido cancelada

Hola {{ $perfilPro->empresa }},

La solicitud de presupuesto:

**“{{ $solicitud->titulo }}”**  

ha sido **cancelada por el administrador** de ReformUp.

---

## Datos del cliente

@isset($cliente)
- Cliente: **{{ $cliente->nombre ?? $cliente->name }} {{ $cliente->apellidos ?? '' }}**
- Email: {{ $cliente->email }}
@else
_No se han podido recuperar los datos del cliente._
@endisset

---

## Presupuesto asociado

@isset($presupuesto)
- Estado del presupuesto: **{{ ucfirst(str_replace('_', ' ', $presupuesto->estado)) }}**
- Importe estimado:
    @if(!is_null($presupuesto->total))
    **{{ number_format($presupuesto->total, 2, ',', '.') }} €**
    @else
    _No indicado_
    @endif
@else
_No había un presupuesto asociado (o no se ha llegado a crear)._
@endisset

---

## Trabajo asociado

@isset($trabajo)
- Estado del trabajo: **{{ ucfirst(str_replace('_', ' ', $trabajo->estado)) }}**
- Dirección de la obra:
    @if($trabajo->dir_obra)
    {{ $trabajo->dir_obra }}
    @else
    _No indicada_
    @endif
@else
_No se ha generado ningún trabajo asociado a esta solicitud._
@endisset

---

Esta cancelación implica que ya no se esperan más acciones sobre esta solicitud, ni por tu parte
ni por parte del cliente.

Gracias por tu colaboración en **ReformUp**.

@endcomponent
