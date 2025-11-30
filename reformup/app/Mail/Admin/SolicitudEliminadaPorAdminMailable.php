<?php

namespace App\Mail\Admin;

use App\Models\Solicitud;
use App\Models\User;
use App\Models\Perfil_Profesional;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitudEliminadaPorAdminMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var \App\Models\Solicitud
     */
    public $solicitud;

    /**
     * @var \App\Models\User|null  Cliente
     */
    public $cliente;

    /**
     * @var \App\Models\Perfil_Profesional|null
     */
    public $profesional;

    /**
     * @var array  Info sobre estados y qué se ha borrado
     */
    public $infoAccion;

    /**
     * @var bool  true si el destinatario es profesional, false si es cliente
     */
    public $esProfesional;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Solicitud              $solicitud
     * @param  \App\Models\User|null              $cliente
     * @param  \App\Models\Perfil_Profesional|null $profesional
     * @param  array                              $infoAccion
     * @param  bool                               $esProfesional
     * @return void
     */
    public function __construct(
        Solicitud $solicitud,
        ?User $cliente,
        ?Perfil_Profesional $profesional,
        array $infoAccion,
        bool $esProfesional
    ) {
        $this->solicitud    = $solicitud;
        $this->cliente      = $cliente;
        $this->profesional  = $profesional;
        $this->infoAccion   = $infoAccion;
        $this->esProfesional = $esProfesional;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Asunto distinto según destinatario
        $subject = $this->esProfesional
            ? 'Aviso sobre eliminación de una solicitud en ReformUp'
            : 'Aviso sobre eliminación de tu solicitud en ReformUp';

        return $this->subject($subject)
                    ->view('emails.admin.solicitudes.eliminada');
    }
}
