@php
    $estadoSolicitud = $infoAccion['estadoSolicitudOriginal'] ?? null;
    $estadoPresu = $infoAccion['estadoPresupuesto'] ?? null;
    $estadoTrabajo = $infoAccion['estadoTrabajo'] ?? null;

    $teniaPresu = $infoAccion['teniaPresupuesto'] ?? false;
    $teniaTrabajo = $infoAccion['teniaTrabajo'] ?? false;
    $teniaComentarios = $infoAccion['teniaComentarios'] ?? false;
@endphp

@if (!$esProfesional)
    {{-- VERSIÓN PARA CLIENTE --}}
    <p>Hola {{ $cliente->nombre ?? 'cliente' }},</p>

    <p>
        Te informamos de que el equipo de administración de <strong>ReformUp</strong> ha
        eliminado una de tus solicitudes registradas en la plataforma.
    </p>

    @if ($estadoSolicitud)
        <p>
            La solicitud se encontraba en estado
            <strong>{{ $estadoSolicitud }}</strong>
            en el momento de la revisión.
        </p>
    @endif

    @if ($teniaPresu)
        <p>
            Esta solicitud tenía un <strong>presupuesto asociado</strong>
            @if ($estadoPresu)
                en estado <strong>{{ $estadoPresu }}</strong>
            @endif
            que también ha sido eliminado.
        </p>
    @endif

    @if ($teniaTrabajo)
        <p>
            A partir de ese presupuesto se había generado un <strong>trabajo</strong>
            @if ($estadoTrabajo)
                en estado <strong>{{ $estadoTrabajo }}</strong>
            @endif
            que ha sido igualmente eliminado por el administrador.
        </p>
    @endif

    @if ($teniaComentarios)
        <p>
            Además, se han eliminado los <strong>comentarios y valoraciones asociados</strong> a ese trabajo,
            para mantener la coherencia de la información en la plataforma.
        </p>
    @endif

    <p>
        Esta acción se ha realizado para mantener tu historial de solicitudes limpio y coherente,
        evitando registros duplicados, incompletos o que ya no son relevantes.
    </p>

    <p>
        Si tienes cualquier duda o crees que esta eliminación no debería haberse realizado,
        puedes ponerte en contacto con nosotros respondiendo a este correo
        o a través de la sección de <strong>Contacto</strong> de la web.
    </p>

    <p>
        Gracias por confiar en <strong>ReformUp</strong>.
    </p>

    <p>
        Un saludo,<br>
        <strong>Equipo de administración de ReformUp</strong>
    </p>
@else
    {{-- VERSIÓN PARA PROFESIONAL --}}
    <p>Hola {{ $profesional->empresa ?? 'profesional' }},</p>

    <p>
        Te informamos de que el equipo de administración de <strong>ReformUp</strong> ha
        eliminado una solicitud que estaba vinculada a tu perfil profesional.
    </p>

    @if ($estadoSolicitud)
        <p>
            La solicitud se encontraba en estado
            <strong>{{ $estadoSolicitud }}</strong>
            en el momento de la revisión.
        </p>
    @endif

    @if ($teniaPresu)
        <p>
            El <strong>presupuesto</strong> que habías generado para esta solicitud
            @if ($estadoPresu)
                (en estado <strong>{{ $estadoPresu }}</strong>)
            @endif
            se ha eliminado de la plataforma.
        </p>
    @endif

    @if ($teniaTrabajo)
        <p>
            El <strong>trabajo asociado</strong> a dicho presupuesto
            @if ($estadoTrabajo)
                (en estado <strong>{{ $estadoTrabajo }}</strong>)
            @endif
            también ha sido eliminado por el administrador.
        </p>
    @endif

    @if ($teniaComentarios)
        <p>
            Del mismo modo, se han eliminado los <strong>comentarios y valoraciones asociados</strong> a ese trabajo,
            para mantener la consistencia de la información en ReformUp.
        </p>

        @if (!empty($descripcionActualizada))
            <p><strong>Descripción actualizada:</strong></p>
            <div>
                {!! $descripcionActualizada !!}
            </div>
        @endif
    @endif

    <p>
        Esta acción se ha llevado a cabo para mantener un entorno de trabajo claro,
        sin solicitudes obsoletas o inconsistentes en tu panel.
    </p>

    <p>
        Si necesitas más información sobre esta eliminación o no estás de acuerdo con la decisión,
        puedes ponerte en contacto con nosotros respondiendo a este correo
        o a través de la sección de <strong>Contacto</strong> de la plataforma.
    </p>

    <p>
        Gracias por seguir colaborando con <strong>ReformUp</strong>.
    </p>

    <p>
        Un saludo,<br>
        <strong>Equipo de administración de ReformUp</strong>
    </p>
@endif
