<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PresupuestoRechazadoPorClienteMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $presupuesto;
    public $cliente;
    public $motivo;
    public $solicitud;

    public function __construct($presupuesto, $cliente, $motivo, $solicitud)
    {
        $this->presupuesto = $presupuesto;
        $this->cliente     = $cliente;
        $this->motivo      = $motivo;
        $this->solicitud      = $solicitud;
    }

    public function build()
    {
        return $this->subject('Un cliente ha rechazado tu presupuesto')
            ->markdown('emails.profesional.presupuesto_rechazado');
    }
}
