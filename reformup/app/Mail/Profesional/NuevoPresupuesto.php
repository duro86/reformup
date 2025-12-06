<?php

namespace App\Mail\Profesional;

use App\Models\Presupuesto;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\Perfil_Profesional;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevoPresupuesto extends Mailable
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
        return $this->subject('Tienes un nuevo presupuesto en ReformUp')
            ->markdown('emails.profesional.nuevo_presupuesto');
    }
}
