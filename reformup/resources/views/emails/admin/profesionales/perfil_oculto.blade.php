@php
    $nombre = $user->nombre ?? $user->name ?? '';
@endphp

<p>Hola {{ $nombre }},</p>

<p>
    Te informamos de que tu perfil profesional
    <strong>{{ $perfil->empresa }}</strong>
    ha sido <strong>ocultado temporalmente</strong> en ReformUp.
</p>

@if(!empty($motivo))
    <p><strong>Motivo facilitado por el administrador:</strong></p>
    <p>{{ $motivo }}</p>
@else
    <p>
        El equipo de administración está revisando la información de tu perfil
        o ha detectado algún dato pendiente de actualización.
    </p>
@endif

<p>
    Si necesitas más información o crees que se trata de un error,
    ponte en contacto con nosotros respondiendo a este correo.
</p>

<p>Un saludo,<br>El equipo de ReformUp</p>
