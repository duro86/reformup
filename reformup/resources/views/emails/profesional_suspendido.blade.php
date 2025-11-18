<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil profesional suspendido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #222;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .mail-wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 16px;
        }
        .card {
            background: #ffffff;
            border-radius: 8px;
            padding: 24px 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }
        h1 {
            font-size: 20px;
            margin-top: 0;
            color: #c53030;
        }
        p {
            line-height: 1.5;
            margin: 0 0 12px 0;
        }
        .footer {
            margin-top: 16px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="mail-wrapper">
    <div class="card">
        <h1>Información sobre tu perfil profesional</h1>

        <p>Hola {{ $user->nombre }},</p>

        <p>
            Te informamos de que tu perfil profesional
            <strong>{{ $perfil->empresa }}</strong> ha sido marcado actualmente como
            <strong>no visible</strong> en la plataforma ReformUp.
        </p>

        <p>
            Esto significa que, de momento, los usuarios no podrán encontrarte en el buscador
            de profesionales ni enviarte nuevas solicitudes.
        </p>

        <p>
            Si crees que se trata de un error o quieres más información sobre el motivo,
            contacta con el equipo de soporte respondiendo a este correo o a través del
            área de ayuda de la web.
        </p>

        <p>
            Nuestro objetivo es mantener la plataforma segura y confiable tanto para usuarios
            como para profesionales.
        </p>

        <p>Gracias por tu comprensión.</p>
    </div>

    <div class="footer">
        Este es un mensaje automático, por favor, no respondas a este correo si tu cliente de correo no lo permite.
    </div>
</div>
</body>
</html>
