<?php

namespace App\Mail\Usuario;

use App\Models\Solicitud;
use App\Models\User;
use App\Models\Perfil_Profesional;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnvioSolicitudMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;
    public $cliente;
    public $profesional;

    /**
     * Create a new message instance.
     */
    public function __construct(Solicitud $solicitud, User $cliente, Perfil_Profesional $profesional)
    {
        $this->solicitud   = $solicitud;
        $this->cliente     = $cliente;
        $this->profesional = $profesional;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Has recibido una nueva solicitud en ReformUp')
                    ->view('emails.usuario.solicitudes.envio_solicitud');
    }
}
