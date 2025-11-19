@component('mail::message')
    # Trabajo cancelado

    Hola {{ $perfil->empresa ?? 'Profesional' }},

    El cliente **{{ $cliente->name ?? $cliente->email }}** ha cancelado un trabajo asociado al presupuesto
    #{{ $presupuesto->id ?? '-' }}.

    - Título de la solicitud: **{{ optional($presupuesto->solicitud)->titulo ?? 'Sin título' }}**
    - ID del trabajo: **{{ $trabajo->id }}**
    - Dirección de obra: {{ $trabajo->dir_obra ?? 'No indicada' }}

    @isset($motivo)
        **Motivo indicado por el cliente:**

        > {{ $motivo }}
    @endisset

    Gracias,
    {{ config('app.name') }}
@endcomponent
