@php
    $nombre = $user->nombre ?? $user->name ?? '';
@endphp

<p>Hola {{ $nombre }},</p>

<p>
    Te informamos de que tu perfil profesional
    <strong>{{ $perfil->empresa }}</strong>
    ha sido <strong>publicado</strong> y ya es visible para los clientes en ReformUp.
</p>

<p>
    A partir de ahora podrán encontrarte según tus oficios y tu zona, y podrán enviarte solicitudes de reforma.
</p>

<p>
    Si detectas cualquier dato incorrecto, puedes acceder a tu panel y actualizar la información de tu empresa.
</p>

<p>Un saludo,<br>El equipo de ReformUp</p>
