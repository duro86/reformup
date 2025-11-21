<?php

namespace App\Mail\Admin;

use App\Models\Comentario;
use App\Models\Trabajo;
use App\Models\User;
use App\Models\Perfil_Profesional;
use App\Models\Solicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComentarioOcultadoMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $comentario;
    public $cliente;
    public $trabajo;
    public $perfilPro;
    public $solicitud;

    public function __construct(
        Comentario $comentario,
        User $cliente,
        ?Trabajo $trabajo,
        ?Perfil_Profesional $perfilPro,
        ?Solicitud $solicitud = null
    ) {
        $this->comentario = $comentario;
        $this->cliente    = $cliente;
        $this->trabajo    = $trabajo;
        $this->perfilPro  = $perfilPro;
        $this->solicitud  = $solicitud;
    }

    public function build()
    {
        return $this->subject('Tu comentario ha dejado de ser visible en ReformUp')
            ->markdown('emails.admin.comentarios.ocultado');
    }
}
