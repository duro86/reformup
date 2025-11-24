<?php
namespace App\Mail\Admin;

use App\Models\Perfil_Profesional;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PerfilProfesionalPublicadoMailable extends Mailable
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
        return $this->subject('Tu perfil profesional ya es visible en ReformUp')
            ->view('emails.admin.profesionales.perfil_publicado');
    }
}
