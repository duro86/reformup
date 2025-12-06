@component('mail::message')
# Nuevo presupuesto disponible

Hola {{ $cliente->nombre ?? ($cliente->name ?? $cliente->email) }},

El profesional **{{ $perfilPro->empresa ?? 'un profesional de ReformUp' }}** ha enviado un presupuesto
para tu solicitud:

> **“{{ $solicitud->titulo }}”**  
> (ID solicitud: #{{ $solicitud->id }})

---

### Detalles del presupuesto

- Referencia del presupuesto: **#{{ $presupuesto->id }}**
- Importe total:
  @if(!is_null($presupuesto->total))
  **{{ number_format($presupuesto->total, 2, ',', '.') }} €**
  @else
  _No indicado_
  @endif
- Estado: **{{ ucfirst($presupuesto->estado) }}**

@if($presupuesto->notas)
---
### Notas del profesional

> {{ $presupuesto->notas }}
@endif

---

Para ver el presupuesto completo y gestionarlo, entra en tu panel de usuario de ReformUp.

@component('mail::button', ['url' => config('app.url')])
Ir a mi panel
@endcomponent

Gracias por usar **ReformUp**.

@endcomponent
