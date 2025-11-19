<?php

namespace App\Mail\Admin;

use App\Models\Comentario;
use App\Models\Trabajo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComentarioPendienteMailable extends Mailable
{
    use Queueable, SerializesModels;

    public Comentario $comentario;
    public Trabajo $trabajo;
    public ?User $cliente;
    public $profesional; // Perfil_Profesional o null

    /**
     * Create a new message instance.
     */
    public function __construct(Comentario $comentario, Trabajo $trabajo, User $cliente, $profesional = null)
    {
        $this->comentario  = $comentario;
        $this->trabajo     = $trabajo;
        $this->cliente     = $cliente;
        $this->profesional = $profesional;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Nuevo comentario pendiente de revisión')
            ->markdown('emails.admin.comentarios.pendiente');
    }
}
