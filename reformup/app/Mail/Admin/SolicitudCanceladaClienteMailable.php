<?php

namespace App\Mail\Admin;

use App\Models\Solicitud;
use App\Models\User;
use App\Models\Perfil_Profesional;
use App\Models\Presupuesto;
use App\Models\Trabajo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitudCanceladaClienteMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;
    public $cliente;
    public $perfilPro;
    public $presupuesto;
    public $trabajo;

    public function __construct(
        Solicitud $solicitud,
        User $cliente,
        ?Perfil_Profesional $perfilPro = null,
        ?Presupuesto $presupuesto = null,
        ?Trabajo $trabajo = null
    ) {
        $this->solicitud   = $solicitud;
        $this->cliente     = $cliente;
        $this->perfilPro   = $perfilPro;
        $this->presupuesto = $presupuesto;
        $this->trabajo     = $trabajo;
    }

    public function build()
    {
        return $this->subject('Tu solicitud ha sido cancelada')
            ->markdown('emails.admin.solicitudes.cancelada_cliente');
    }
}
