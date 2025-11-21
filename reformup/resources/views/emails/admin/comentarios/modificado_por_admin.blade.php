@component('mail::message')
# Hola {{ $cliente->nombre ?? $cliente->name }}

Tu comentario sobre
@isset($trabajo)
**el trabajo #{{ $trabajo->id }}**
@endisset
@isset($comentario->trabajo->presupuesto->solicitud->titulo)
“**{{ $comentario->trabajo->presupuesto->solicitud->titulo }}**”
@endisset
ha sido revisado por el equipo de **ReformUp**.

Hemos detectado algunos aspectos que no encajaban del todo con las normas de uso de la plataforma
(lenguaje, tono o contenido) y hemos realizado pequeños ajustes para que pueda publicarse
manteniendo el sentido general de tu opinión.

@component('mail::panel')
**Puntuación actual:** {{ $comentario->puntuacion }} / 5

@isset($comentario->opinion)
**Texto actualizado del comentario:**

"{{ $comentario->opinion }}"
@endisset
@endcomponent

Queremos que ReformUp sea un espacio útil y respetuoso para todos:
clientes y profesionales.  
Gracias por tu comprensión y por usar nuestra plataforma.

Si no estás de acuerdo con la modificación, puedes responder a este correo para que lo revisemos.

@if($perfilPro)
Profesional valorado: **{{ $perfilPro->empresa }}**
@endif

Saludos,  
El equipo de **ReformUp**
@endcomponent

