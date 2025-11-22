<?php

namespace App\Mail\Admin;

use App\Models\Solicitud;
use App\Models\User;
use App\Models\Presupuesto;
use App\Models\Trabajo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitudModificadaPorAdminMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;
    public $cliente;
    public $presupuesto;
    public $trabajo;
    public $perfilPro;
    public $oldEstado;
    public $newEstado;

    /**
     * @param Solicitud        $solicitud
     * @param User|null        $cliente      Cliente (si el correo va para el cliente; null si es para el profesional)
     * @param Presupuesto|null $presupuesto  Presupuesto asociado (opcional)
     * @param Trabajo|null     $trabajo      Trabajo asociado (opcional)
     * @param mixed|null       $perfilPro    Perfil profesional (opcional)
     * @param string|null      $oldEstado    Estado anterior de la solicitud (opcional)
     * @param string|null      $newEstado    Estado nuevo de la solicitud (opcional)
     */
    public function __construct(
        Solicitud $solicitud,
        ?User $cliente = null,
        ?Presupuesto $presupuesto = null,
        ?Trabajo $trabajo = null,
        $perfilPro = null,
        ?string $oldEstado = null,
        ?string $newEstado = null
    ) {
        $this->solicitud   = $solicitud;
        $this->cliente     = $cliente;
        $this->presupuesto = $presupuesto;
        $this->trabajo     = $trabajo;
        $this->perfilPro   = $perfilPro;
        $this->oldEstado   = $oldEstado;
        $this->newEstado   = $newEstado;
    }

    public function build()
    {
        // Si hay cliente, asumimos que el correo es para él.
        // Si $cliente es null, asumimos que el correo es para el profesional.
        $subject = $this->cliente
            ? 'Tu solicitud ha sido modificada por el equipo de ReformUp'
            : 'Se ha modificado una solicitud asignada a tu perfil en ReformUp';

        return $this
            ->subject($subject)
            ->markdown('emails.admin.solicitudes.modificada_por_admin');
    }
}
