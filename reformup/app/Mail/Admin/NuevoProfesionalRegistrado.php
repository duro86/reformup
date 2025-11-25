<?php

namespace App\Mail\Admin;

use App\Models\User;
use App\Models\Perfil_Profesional;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevoProfesionalRegistrado extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $perfil;

    public function __construct(User $user, Perfil_Profesional $perfil)
    {
        $this->user   = $user;
        $this->perfil = $perfil;
    }

    public function build()
    {
        return $this->subject('Nuevo profesional pendiente de revisión')
                    ->markdown('emails.nuevo_profesional');
    }
}
