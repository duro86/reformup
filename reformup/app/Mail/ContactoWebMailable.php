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

    public function __construct($nombre, $email, $asuntoUsuario, $mensaje)
    {
        $this->nombre        = $nombre;
        $this->email         = $email;
        $this->asuntoUsuario = $asuntoUsuario;
        $this->mensaje       = $mensaje; // YA viene purificado
    }

    public function build()
    {
        return $this
            ->subject('Nuevo mensaje de contacto desde ReformUp')
            ->markdown('emails.contacto.web');
    }
}
