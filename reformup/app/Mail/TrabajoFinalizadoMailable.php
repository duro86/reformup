<?php

namespace App\Mail;

use App\Models\Trabajo;
use App\Models\Presupuesto;
use App\Models\User;
use App\Models\Solicitud;
use App\Models\Perfil_Profesional;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrabajoFinalizadoMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $trabajo;
    public $presupuesto;
    public $cliente;
    public $perfilPro;
    public $solicitud;

    public function __construct(Trabajo $trabajo, ?Presupuesto $presupuesto, User $cliente, Perfil_Profesional $perfilPro, Solicitud $solicitud)
    {
        $this->trabajo     = $trabajo;
        $this->presupuesto = $presupuesto;
        $this->cliente     = $cliente;
        $this->perfilPro   = $perfilPro;
        $this->solicitud   = $solicitud;
    }

    public function build()
    {
        return $this->subject('Tu trabajo ha sido finalizado')
            ->markdown('emails.trabajos.finalizado');
    }
}
