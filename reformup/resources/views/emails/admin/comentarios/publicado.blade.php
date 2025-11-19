@component('mail::message')
    # Tu comentario ha sido publicado

    Hola {{ $cliente->nombre ?? ($cliente->name ?? $cliente->email) }},

    Tu comentario sobre el trabajo
    @isset($trabajo)
        **#{{ $trabajo->id }}**
    @endisset
    ha sido **publicado** y ya es visible en la plataforma.

    - Puntuación: **{{ $comentario->puntuacion }} / 5**
    - Opinión:
    @if ($comentario->opinion)
        > {{ $comentario->opinion }}
    @else
        _Sin texto de opinión._
    @endif

    @isset($perfilPro)
        ---

        **Profesional**

        {{ $perfilPro->empresa }}

        {{ $perfilPro->email_empresa }}

        @isset($perfilPro->telefono_empresa)
            Teléfono: {{ $perfilPro->telefono_empresa }}
        @endisset
    @endisset

    Gracias por ayudar a otros usuarios con tu valoración.
    {{ config('app.name') }}
@endcomponent
