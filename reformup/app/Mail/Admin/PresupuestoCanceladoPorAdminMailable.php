<?php

namespace App\Mail\Admin;

use App\Models\Presupuesto;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\Perfil_Profesional;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PresupuestoCanceladoPorAdminMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $presupuesto;
    public $solicitud;
    public $cliente;
    public $perfilPro;
    public $esProfesional;
    public $tipoAccion; //  NUEVO: 'cancelado' | 'eliminado'

    public function __construct(
        Presupuesto $presupuesto,
        ?Solicitud $solicitud = null,       //  mejor nullable
        ?User $cliente = null,
        ?Perfil_Profesional $perfilPro = null,
        bool $esProfesional = false,
        string $tipoAccion = 'cancelado'    //  por defecto
    ) {
        $this->presupuesto   = $presupuesto;
        $this->solicitud     = $solicitud;
        $this->cliente       = $cliente;
        $this->perfilPro     = $perfilPro;
        $this->esProfesional = $esProfesional;
        $this->tipoAccion    = $tipoAccion;
    }

    public function build()
    {
        if ($this->tipoAccion === 'eliminado') {
            $subject = $this->esProfesional
                ? 'Se ha eliminado un presupuesto de una solicitud en ReformUp'
                : 'Tu presupuesto ha sido eliminado por el equipo de ReformUp';
        } else {
            $subject = $this->esProfesional
                ? 'Se ha cancelado un presupuesto de una solicitud en ReformUp'
                : 'Tu presupuesto ha sido cancelado por el equipo de ReformUp';
        }

        return $this
            ->subject($subject)
            ->markdown('emails.admin.presupuestos.cancelado_por_admin');
    }
}
