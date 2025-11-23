<?php

namespace App\Mail\Admin;

use App\Models\Presupuesto;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\Perfil_Profesional;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PresupuestoModificadoPorAdminMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $presupuesto;
    public $solicitud;
    public $cliente;
    public $perfilPro;
    public $isProfesional;
    public $oldTotal;
    public $oldNotas;
    public $oldEstado;

    /**
     * @param bool $isProfesional  true si el correo va dirigido al profesional, false si va al cliente
     */
    public function __construct(
        Presupuesto $presupuesto,
        ?Solicitud $solicitud = null,
        ?User $cliente = null,
        ?Perfil_Profesional $perfilPro = null,
        bool $isProfesional = false,
        ?float $oldTotal = null,
        ?string $oldNotas = null,
        ?string $oldEstado = null
    ) {
        $this->presupuesto   = $presupuesto;
        $this->solicitud     = $solicitud;
        $this->cliente       = $cliente;
        $this->perfilPro     = $perfilPro;
        $this->isProfesional = $isProfesional;
        $this->oldTotal      = $oldTotal;
        $this->oldNotas      = $oldNotas;
        $this->oldEstado     = $oldEstado;
    }

    public function build()
    {
        $subject = $this->isProfesional
            ? 'Se ha modificado un presupuesto de tu solicitud en ReformUp'
            : 'Tu presupuesto ha sido modificado por el equipo de ReformUp';

        return $this
            ->subject($subject)
            ->markdown('emails.admin.presupuestos.modificado_por_admin');
    }
}
