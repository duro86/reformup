<?php

namespace App\Mail;

use App\Models\Perfil_Profesional;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProfesionalValidado extends Mailable
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
        return $this->subject('Tu perfil profesional ha sido validado')
            ->view('emails.profesional_validado');
    }
}
