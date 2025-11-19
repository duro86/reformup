<?php

namespace App\Mail;

use App\Models\Trabajo;
use App\Models\Presupuesto;
use App\Models\Perfil_Profesional;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrabajoCanceladoMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $trabajo;
    public $presupuesto;
    public $perfil;
    public $cliente;
    public $motivo;

    public function __construct(
        Trabajo $trabajo,
        ?Presupuesto $presupuesto,
        ?Perfil_Profesional $perfil,
        User $cliente,
        ?string $motivo = null
    ) {
        $this->trabajo     = $trabajo;
        $this->presupuesto = $presupuesto;
        $this->perfil      = $perfil;
        $this->cliente     = $cliente;
        $this->motivo      = $motivo;
    }

    public function build()
    {
        return $this->subject('Trabajo cancelado por el cliente')
            ->markdown('emails.trabajos.cancelado');
    }
}
