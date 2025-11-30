<?php

namespace App\Mail\Admin;

use App\Models\Trabajo;
use App\Models\User;
use App\Models\Perfil_Profesional;
use App\Models\Presupuesto;
use App\Models\Solicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrabajoCanceladoPorAdminMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $trabajo;
    public $cliente;
    public $perfilPro;
    public $presupuesto;
    public $solicitud;

    public $oldEstado;
    public $oldFechaIni;
    public $oldFechaFin;

    public $paraProfesional;
    public $esEliminacion;   // 👈 NUEVO

    public function __construct(
        Trabajo $trabajo,
        ?User $cliente = null,
        ?Perfil_Profesional $perfilPro = null,
        ?Presupuesto $presupuesto = null,
        ?Solicitud $solicitud = null,
        ?string $oldEstado = null,
        $oldFechaIni = null,
        $oldFechaFin = null,
        bool $paraProfesional = false,
        bool $esEliminacion = false    //  NUEVO PARÁMETRO, por defecto false
    ) {
        $this->trabajo         = $trabajo;
        $this->cliente         = $cliente;
        $this->perfilPro       = $perfilPro;
        $this->presupuesto     = $presupuesto;
        $this->solicitud       = $solicitud;

        $this->oldEstado       = $oldEstado;
        $this->oldFechaIni     = $oldFechaIni;
        $this->oldFechaFin     = $oldFechaFin;

        $this->paraProfesional = $paraProfesional;
        $this->esEliminacion   = $esEliminacion;
    }

    public function build()
    {
        
        if ($this->esEliminacion) {
            $subject = $this->paraProfesional
                ? 'Un trabajo asignado a tu empresa ha sido eliminado por el administrador'
                : 'Tu trabajo en ReformUp ha sido eliminado por el administrador';
        } else {
            $subject = $this->paraProfesional
                ? 'Un trabajo asignado a tu empresa ha sido cancelado'
                : 'Tu trabajo en ReformUp ha sido cancelado';
        }

        return $this
            ->subject($subject)
            ->markdown('emails.admin.trabajos.cancelado_por_admin');
    }
}
