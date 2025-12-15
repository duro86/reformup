@component('mail::message')
# Tu presupuesto ha sido cancelado

Hola {{ $cliente->nombre ?? ($cliente->name ?? $cliente->email) }},

El profesional **{{ $perfilPro->empresa ?? 'el profesional' }}** ha cancelado el presupuesto
asociado a tu solicitud:

> **“{{ $solicitud->titulo }}”**  

---

### Estado de tu solicitud

@if($solicitud->estado === 'abierta')
En este momento, tu solicitud vuelve a estar **abierta**, a la espera de nuevos presupuestos.
@else
El estado actual de tu solicitud es: **{{ ucfirst($solicitud->estado) }}**.
@endif

- Estado del presupuesto: **{{ ucfirst($presupuesto->estado) }}**

---

Puedes entrar en tu panel de usuario para:

- Revisar esta solicitud
- Volver a contactar con profesionales
- Cerrar la solicitud si ya no necesitas el servicio

@component('mail::button', ['url' => config('app.url')])
Ir a mi panel de usuario
@endcomponent

Gracias por usar **ReformUp**.

@endcomponent
