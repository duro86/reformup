@component('mail::message')
# ¡Han aceptado tu presupuesto!

Hola {{ $perfilPro->empresa ?? 'profesional' }},

El cliente
**{{ $cliente->nombre ?? ($cliente->name ?? $cliente->email) }}
{{ $cliente->apellidos ?? '' }}**
ha aceptado tu presupuesto para la solicitud:

> **“{{ $solicitud->titulo }}”**  
> (ID solicitud: #{{ $solicitud->id }})

---

### Detalles del presupuesto

- Referencia del presupuesto: **#{{ $presupuesto->id }}**
- Estado actual: **{{ ucfirst($presupuesto->estado) }}**
- Importe total:
  @if(!is_null($presupuesto->total))
  **{{ number_format($presupuesto->total, 2, ',', '.') }} €**
  @else
  _No indicado_
  @endif

---

Se ha creado automáticamente un **trabajo en estado "previsto"** asociado a este presupuesto.  
Cuando acuerdes la fecha de inicio con el cliente, podrás actualizar el trabajo a "en curso" y más adelante a "finalizado".

@component('mail::button', ['url' => config('app.url')])
Ir a mi panel profesional
@endcomponent

Gracias por trabajar con **ReformUp**.

@endcomponent
