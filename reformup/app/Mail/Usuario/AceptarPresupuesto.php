<?php

namespace App\Mail\Usuario;

use App\Models\Presupuesto;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\Perfil_Profesional;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AceptarPresupuesto extends Mailable
{
    use Queueable, SerializesModels;

    public $presupuesto;
    public $solicitud;
    public $cliente;
    public $perfilPro;

    public function __construct(
        Presupuesto $presupuesto,
        Solicitud $solicitud,
        User $cliente,
        Perfil_Profesional $perfilPro
    ) {
        $this->presupuesto = $presupuesto;
        $this->solicitud   = $solicitud;
        $this->cliente     = $cliente;
        $this->perfilPro   = $perfilPro;
    }

    public function build()
    {
        return $this->subject('Han aceptado tu presupuesto en ReformUp')
            ->markdown('emails.usuario.presupuesto.aceptar_presupuesto');
    }
}
