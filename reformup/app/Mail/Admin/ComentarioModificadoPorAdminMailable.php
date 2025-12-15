<?php

namespace App\Mail\Admin;

use App\Models\Comentario;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\Trabajo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComentarioModificadoPorAdminMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $comentario;
    public $cliente;
    public $trabajo;
    public $perfilPro;
    public $oldOpinion;
    public $oldPuntuacion;
    public $solicitud;

    public function __construct(
        Comentario $comentario,
        User $cliente,
        ?Trabajo $trabajo = null,
        $perfilPro = null,
        ?string $oldOpinion = null,
        ?int $oldPuntuacion = null,
        Solicitud $solicitud
    ) {
        $this->comentario    = $comentario;
        $this->cliente       = $cliente;
        $this->trabajo       = $trabajo;
        $this->perfilPro     = $perfilPro;
        $this->oldOpinion    = $oldOpinion;
        $this->oldPuntuacion = $oldPuntuacion;
        $this->$solicitud = $solicitud;
    }

    public function build()
    {
        return $this
            ->subject('Tu comentario ha sido modificado por el equipo de ReformUp')
            ->markdown('emails.admin.comentarios.modificado_por_admin');
    }
}
