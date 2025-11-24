@component('mail::message')
    # Tu trabajo está en curso
    @php
        $fechaIni = $trabajo->fecha_ini ? $trabajo->fecha_ini->format('d/m/Y H:i') : 'Sin inicio registrado';
        $fechaFin = $trabajo->fecha_fin ? $trabajo->fecha_fin->format('d/m/Y H:i') : 'Sin fin registrado';
    @endphp

    Hola, {{ $cliente->nombre ?? ($cliente->name ?? $cliente->email) }},

    El profesional ha marcado tu trabajo como **en curso**.

    @isset($presupuesto)
        - Presupuesto asociado: **#{{ $presupuesto->id }}**
    @endisset

    - ID del trabajo: **{{ $trabajo->id }}**
    @isset($trabajo->dir_obra)
        - Dirección de la obra: {{ $trabajo->dir_obra }}
    @endisset

    En las próximas horas el profesional se pondrá en contacto contigo por teléfono
    para concretar la hora de comienzo del trabajo y los últimos detalles.

    -----

    **Profesional**

    @if ($perfilPro)
    {{ $perfilPro->empresa }}
    {{ $perfilPro->email_empresa }}
    @isset($perfilPro->telefono_empresa)
    #Teléfono: {{ $perfilPro->telefono_empresa }}
    @endisset
    @endif

    Gracias por utilizar {{ config('app.name') }}.
@endcomponent
