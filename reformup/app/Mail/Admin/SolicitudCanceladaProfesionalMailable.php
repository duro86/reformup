<?php

namespace App\Mail\Admin;

use App\Models\Solicitud;
use App\Models\Perfil_Profesional;
use App\Models\User;
use App\Models\Presupuesto;
use App\Models\Trabajo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitudCanceladaProfesionalMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;
    public $perfilPro;
    public $cliente;
    public $presupuesto;
    public $trabajo;

    public function __construct(
        Solicitud $solicitud,
        Perfil_Profesional $perfilPro,
        ?User $cliente = null,
        ?Presupuesto $presupuesto = null,
        ?Trabajo $trabajo = null
    ) {
        $this->solicitud   = $solicitud;
        $this->perfilPro   = $perfilPro;
        $this->cliente     = $cliente;
        $this->presupuesto = $presupuesto;
        $this->trabajo     = $trabajo;
    }

    public function build()
    {
        return $this->subject('Solicitud cancelada por el administrador')
            ->markdown('emails.admin.solicitudes.cancelada_profesional');
    }
}
