@component('mail::message')
    # Trabajo cancelado

    Hola {{ $cliente->nombre ?? ($cliente->name ?? $cliente->email) }},

    El profesional ha cancelado el trabajo asociado a tu solicitud.

    @isset($presupuesto)
        - Presupuesto: **#{{ $presupuesto->id }}**
    @endisset

    - ID del trabajo: **{{ $trabajo->id }}**
    - Estado actual: **{{ $trabajo->estado }}**

    @isset($trabajo->dir_obra)
        - Dirección de la obra: {{ $trabajo->dir_obra }}
    @endisset

    @isset($motivo)
        **Motivo indicado por el profesional:**

        > {{ $motivo }}
    @endisset

    Si tienes dudas, puedes contactar con el profesional o crear una nueva solicitud en la plataforma.

    Gracias,
    {{ config('app.name') }}
@endcomponent
