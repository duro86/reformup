<?php

namespace App\Mail;

use App\Models\Perfil_Profesional;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProfesionalSuspendido extends Mailable
{
    use Queueable, SerializesModels;

    public $perfil;
    public $user;

    public function __construct(Perfil_Profesional $perfil, User $user)
    {
        $this->perfil = $perfil;
        $this->user   = $user;
    }

    public function build()
    {
        return $this->subject('Información sobre tu perfil profesional, tu perfil ha sido suspendido')
            ->view('emails.profesional_suspendido');
    }
}