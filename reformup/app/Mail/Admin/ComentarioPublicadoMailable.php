<?php

namespace App\Mail\Admin;

use App\Models\Comentario;
use App\Models\Trabajo;
use App\Models\User;
use App\Models\Perfil_Profesional;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComentarioPublicadoMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $comentario;
    public $cliente;
    public $trabajo;
    public $perfilPro;

    public function __construct(
        Comentario $comentario,
        User $cliente,
        ?Trabajo $trabajo,
        ?Perfil_Profesional $perfilPro
    ) {
        $this->comentario = $comentario;
        $this->cliente    = $cliente;
        $this->trabajo    = $trabajo;
        $this->perfilPro  = $perfilPro;
    }

    public function build()
    {
        return $this->subject('Tu comentario ha sido publicado')
            ->markdown('emails.admin.comentarios.publicado');
    }
}
