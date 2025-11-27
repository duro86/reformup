<?php

namespace App\Mail\Admin;

use App\Models\Trabajo;
use App\Models\User;
use App\Models\Perfil_Profesional;
use App\Models\Presupuesto;
use App\Models\Solicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrabajoModificadoPorAdminMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $trabajo;
    public $cliente;
    public $perfilPro;
    public $presupuesto;
    public $solicitud;

    public $oldEstado;
    public $oldDirObra;
    public $oldFechaIni;
    public $oldFechaFin;

    public $paraProfesional;

    // Nuevos campos “comodín”
    public $newEstado;
    public $estadoHumanoOld;
    public $estadoHumanoNew;

    public function __construct(
        Trabajo $trabajo,
        ?User $cliente = null,
        ?Perfil_Profesional $perfilPro = null,
        ?Presupuesto $presupuesto = null,
        ?Solicitud $solicitud = null,
        ?string $oldEstado = null,
        ?string $oldDirObra = null,
        $oldFechaIni = null,
        $oldFechaFin = null,
        bool $paraProfesional = false
    ) {
        $this->trabajo       = $trabajo;
        $this->cliente       = $cliente;
        $this->perfilPro     = $perfilPro;
        $this->presupuesto   = $presupuesto;
        $this->solicitud     = $solicitud;

        $this->oldEstado     = $oldEstado;
        $this->oldDirObra    = $oldDirObra;
        $this->oldFechaIni   = $oldFechaIni;
        $this->oldFechaFin   = $oldFechaFin;

        $this->paraProfesional = $paraProfesional;

        // --------- COMODÍN DE ESTADOS ---------
        $this->newEstado = $trabajo->estado;

        $mapEstados = [
            'previsto'   => 'previsto',
            'en_curso'   => 'en curso',
            'finalizado' => 'finalizado',
            'cancelado'  => 'cancelado',
        ];

        $this->estadoHumanoOld = $oldEstado
            ? ($mapEstados[$oldEstado] ?? str_replace('_', ' ', $oldEstado))
            : null;

        $this->estadoHumanoNew = $mapEstados[$this->newEstado]
            ?? str_replace('_', ' ', $this->newEstado);
    }

    public function build()
    {
        // Asunto genérico pero usando el estado nuevo
        $baseSubject = 'Estado del trabajo actualizado a: ' . ucfirst($this->estadoHumanoNew);

        $subject = $this->paraProfesional
            ? 'ReformUp · ' . $baseSubject . ' (trabajo de tu empresa)'
            : 'ReformUp · ' . $baseSubject;

        return $this
            ->subject($subject)
            ->markdown('emails.admin.trabajos.modificado_por_admin');
    }
}
