@component('mail::message')
    @php
        $fechaIni = $trabajo->fecha_ini ? $trabajo->fecha_ini->format('d/m/Y H:i') : 'Sin inicio registrado';
        $fechaFin = $trabajo->fecha_fin ? $trabajo->fecha_fin->format('d/m/Y H:i') : 'Sin fin registrado';
    @endphp

    # Tu trabajo ha finalizado

    Hola {{ $cliente->nombre ?? ($cliente->name ?? $cliente->email) }},

    El profesional ha marcado tu trabajo como **finalizado**.

    @isset($presupuesto)
        - Presupuesto asociado: **#{{ $presupuesto->solicitud->titulo }}**
    @endisset

    - ID del trabajo: **{{ $trabajo->$presupuesto->solicitud->titulo }}**
    - Fecha de inicio: **{{ $fechaIni }}**
    - Fecha de fin: **{{ $fechaFin }}**
    @isset($trabajo->dir_obra)
        - Dirección de la obra: {{ $trabajo->dir_obra }}
    @endisset


    Si estás satisfecho con el resultado, te invitamos a dejar una valoración del profesional
    en la plataforma. Tu opinión ayuda a otros usuarios.

    ---

    **Profesional**

    @if ($perfilPro)
        {{ $perfilPro->empresa }}

        {{ $perfilPro->email_empresa }}

        @isset($perfilPro->telefono_empresa)
            **Teléfono:** {{ $perfilPro->telefono_empresa }}
        @endisset
    @endif

    Gracias por utilizar {{ config('app.name') }}.
@endcomponent
