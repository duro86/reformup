<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactoWebMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;
    public $email;
    public $asuntoUsuario;
    public $mensaje;

    /**
     * Crear una nueva instancia del mailable.
     */
    public function __construct(string $nombre, string $email, string $asuntoUsuario, string $mensaje)
    {
        $this->nombre        = $nombre;
        $this->email         = $email;
        $this->asuntoUsuario = $asuntoUsuario;
        $this->mensaje       = $mensaje;
    }

    /**
     * Construir el mensaje.
     */
    public function build()
    {
        // Asunto del correo que tú recibes
        $subject = 'Nuevo mensaje de contacto: ' . $this->asuntoUsuario;

        return $this
            ->subject($subject)
            // Para que cuando respondas desde tu correo, responda al usuario
            ->replyTo($this->email, $this->nombre)
            ->markdown('emails.contacto.web', [
                'nombre'        => $this->nombre,
                'email'         => $this->email,
                'asuntoUsuario' => $this->asuntoUsuario,
                'mensaje'       => $this->mensaje,
            ]);
    }
}
