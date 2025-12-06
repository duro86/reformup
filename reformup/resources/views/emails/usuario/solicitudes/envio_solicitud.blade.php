<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva solicitud en ReformUp</title>
</head>
<body style="font-family: Arial, sans-serif; font-size:14px; color:#333;">
    <p>Hola {{ $profesional->empresa }},</p>

    <p>
        Has recibido una nueva solicitud a través de <strong>ReformUp</strong>.
    </p>

    <p>
        <strong>Cliente:</strong>
        {{ $cliente->nombre ?? 'Cliente' }}
        {{ $cliente->apellidos ?? '' }}
    </p>

    <p>
        <strong>Título de la solicitud:</strong><br>
        {{ $solicitud->titulo }}
    </p>

    @if($solicitud->ciudad || $solicitud->provincia)
        <p>
            <strong>Ubicación:</strong><br>
            {{ $solicitud->ciudad }}
            @if($solicitud->provincia)
                ({{ $solicitud->provincia }})
            @endif
        </p>
    @endif

    <p>
        Puedes entrar en tu panel de profesional para ver todos los detalles y responder al cliente.
    </p>

    <p style="margin-top:20px;">
        Un saludo,<br>
        <strong>ReformUp</strong>
    </p>
</body>
</html>
