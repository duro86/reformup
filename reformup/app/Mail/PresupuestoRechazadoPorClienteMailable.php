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

    public function __construct($presupuesto, $cliente, $motivo)
    {
        $this->presupuesto = $presupuesto;
        $this->cliente     = $cliente;
        $this->motivo      = $motivo;
    }

    public function build()
    {
        return $this->subject('Un cliente ha rechazado tu presupuesto')
            ->markdown('emails.profesional.presupuesto_rechazado');
    }
}
