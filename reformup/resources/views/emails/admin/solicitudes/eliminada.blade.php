@php
    $estadoSolicitud = $infoAccion['estadoSolicitudOriginal'] ?? null;
    $estadoPresu     = $infoAccion['estadoPresupuesto'] ?? null;
    $estadoTrabajo   = $infoAccion['estadoTrabajo'] ?? null;
    $teniaPresu      = $infoAccion['teniaPresupuesto'] ?? false;
    $teniaTrabajo    = $infoAccion['teniaTrabajo'] ?? false;
    $teniaComentarios = $infoAccion['teniaComentarios'] ?? false;

    $tituloSolicitud = $solicitud->titulo ?? 'solicitud de reforma';
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud eliminada</title>
</head>
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">

    @if (!$esProfesional)
        {{-- CORREO PARA CLIENTE --}}
        <p>Hola {{ $cliente->nombre ?? 'cliente' }},</p>

        <p>
            Te informamos de que un administrador de <strong>ReformUp</strong> ha eliminado
            tu solicitud <strong>"{{ $tituloSolicitud }}"</strong>.
        </p>

        <p>A continuación te explicamos qué se ha borrado exactamente:</p>

        <ul>
            <li>
                <strong>Solicitud:</strong>
                se ha eliminado la solicitud completa de nuestra plataforma.
            </li>

            @if ($teniaPresu)
                <li>
                    <strong>Presupuesto asociado:</strong>
                    se ha eliminado el presupuesto que estaba ligado a esta solicitud
                    (estado: {{ $estadoPresu ?? 'no disponible' }}).
                </li>
            @else
                @if ($estadoSolicitud === 'abierta')
                    <li>
                        <strong>Presupuesto:</strong>
                        la solicitud se encontraba abierta y aún no se había generado ningún presupuesto.
                    </li>
                @endif
            @endif

            @if ($teniaTrabajo)
                <li>
                    <strong>Trabajo asociado:</strong>
                    se ha eliminado el trabajo creado a partir del presupuesto
                    (estado: {{ $estadoTrabajo ?? 'no disponible' }}).
                </li>

                @if ($teniaComentarios)
                    <li>
                        <strong>Comentarios y valoraciones:</strong>
                        se han eliminado también los comentarios y valoraciones vinculados a este trabajo.
                    </li>
                @endif
            @else
                <li>
                    <strong>Trabajo:</strong>
                    no existía ningún trabajo asociado en el momento de la eliminación.
                </li>
            @endif
        </ul>

        @if ($estadoTrabajo === 'en_curso')
            {{-- En teoría no debería llegar aquí, porque lo bloqueas en el controlador --}}
            <p style="color:#c00;">
                Nota: el trabajo asociado figuraba como "en curso". Si ves alguna incidencia en tu panel,
                contacta con nosotros para revisarlo.
            </p>
        @endif

        <p>
            Esta eliminación ha sido gestionada por el equipo de administración de ReformUp, normalmente
            por motivos de revisión, duplicidad o solicitud explícita de alguna de las partes.
        </p>

        <p>
            Si consideras que se trata de un error, puedes responder a este correo o ponerte en contacto con
            nuestro soporte indicando el título de la solicitud y tu email de registro.
        </p>

        <p>Un saludo,<br>
            <strong>Equipo ReformUp</strong>
        </p>

    @else
        {{-- CORREO PARA PROFESIONAL --}}
        <p>Hola {{ $profesional->empresa ?? 'profesional' }},</p>

        <p>
            Te informamos de que un administrador de <strong>ReformUp</strong> ha eliminado
            la solicitud de un cliente vinculada a tu perfil:
            <strong>"{{ $tituloSolicitud }}"</strong>.
        </p>

        <p>Esta acción afecta a los siguientes elementos:</p>

        <ul>
            <li>
                <strong>Solicitud del cliente:</strong>
                se ha eliminado de la plataforma, por lo que ya no aparecerá en tu listado
                de solicitudes ni trabajos.
            </li>

            @if ($teniaPresu)
                <li>
                    <strong>Presupuesto que enviaste:</strong>
                    se ha eliminado el presupuesto asociado a esta solicitud
                    (estado: {{ $estadoPresu ?? 'no disponible' }}).
                </li>
            @else
                @if ($estadoSolicitud === 'abierta')
                    <li>
                        <strong>Presupuesto:</strong>
                        la solicitud estaba abierta y todavía no habías enviado ningún presupuesto.
                    </li>
                @endif
            @endif

            @if ($teniaTrabajo)
                <li>
                    <strong>Trabajo generado:</strong>
                    se ha eliminado el trabajo vinculado a este presupuesto
                    (estado: {{ $estadoTrabajo ?? 'no disponible' }}).
                </li>

                @if ($teniaComentarios)
                    <li>
                        <strong>Comentarios y valoraciones del cliente:</strong>
                        se han eliminado también las valoraciones que el cliente hubiera dejado sobre este trabajo.
                    </li>
                @endif
            @else
                <li>
                    <strong>Trabajo:</strong>
                    no había ningún trabajo generado a partir de esta solicitud en el momento de la eliminación.
                </li>
            @endif
        </ul>

        <p>
            La eliminación ha sido gestionada por el equipo de administración de ReformUp, normalmente
            por motivos de revisión de contenido, incidencias en el proceso o cierre manual del expediente.
        </p>

        <p>
            Si crees que la eliminación no debería haberse realizado o necesitas más información,
            ponte en contacto con nosotros indicando el título de la solicitud y tu email de acceso.
        </p>

        <p>Un saludo,<br>
            <strong>Equipo ReformUp</strong>
        </p>
    @endif

</body>
</html>
