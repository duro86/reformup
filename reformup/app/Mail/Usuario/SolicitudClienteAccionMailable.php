<?php
// app/Mail/Usuario/SolicitudClienteAccionMailable.php
namespace App\Mail\Usuario;

use App\Models\Solicitud;
use App\Models\User;
use App\Models\Perfil_Profesional;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitudClienteAccionMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;
    public $cliente;
    public $perfilPro;
    public $motivo;
    public $tipoAccion; // 'cancelada' | 'eliminada'

    public function __construct(
        Solicitud $solicitud,
        User $cliente,
        ?Perfil_Profesional $perfilPro,
        ?string $motivo,
        string $tipoAccion // 'cancelada' o 'eliminada'
    ) {
        $this->solicitud  = $solicitud;
        $this->cliente    = $cliente;
        $this->perfilPro  = $perfilPro;
        $this->motivo     = $motivo;
        $this->tipoAccion = $tipoAccion;
    }

    public function build()
    {
        $subject = match ($this->tipoAccion) {
            'eliminada' => 'Una solicitud ha sido eliminada por el cliente',
            default     => 'Una solicitud ha sido cancelada por el cliente',
        };

        return $this
            ->subject($subject)
            ->markdown('emails.usuario.solicitudes.accion_cliente');
    }
}
