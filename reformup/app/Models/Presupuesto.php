<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presupuesto extends Model
{
    //use SoftDeletes;

    protected $table = 'presupuestos';

    /**
     * Atributos que se pueden asignar de forma masiva (mas asignable).
     * 
     * Define los campos permitidos al crear o actualizar un presupuesto.  
     * Laravel usará esta lista para proteger el modelo frente a la 
     * vulnerabilidad de “mass assignment”, permitiendo solo modificar 
     * los atributos indicados.
     * Campos:
     * - pro_id: ID del perfil profesional que emite el presupuesto.
     * - solicitud_id: ID de la solicitud a la que responde.
     * - total: importe total propuesto.
     * - notas: comentarios o aclaraciones del profesional (opcional).
     * - estado: situación actual del presupuesto (‘enviado’, ‘aceptado’, ‘rechazado’, etc.).
     * - docu_pdf: ruta del documento PDF generado (opcional).
     * - fecha: fecha de emisión o actualización del presupuesto.
     */
    protected $fillable = ['pro_id', 'solicitud_id', 'total', 'notas', 'estado', 'docu_pdf', 'fecha'];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public const ESTADOS = [
        'enviado'   => 'Enviados',
        'aceptado'  => 'Aceptados',
        'rechazado' => 'Rechazados',
    ];

    /**
     * Relación inversa con el perfil profesional.
     * 
     * Cada presupuesto pertenece a un único profesional que lo emite.
     * La clave foránea `pro_id` enlaza con la tabla `perfiles_profesionales`.
     * 
     * Ejemplo de uso:
     * $profesional = $presupuesto->profesional;
     */
    public function profesional()
    {
        return $this->belongsTo(Perfil_Profesional::class, 'pro_id');
    }

    /**
     * Relación inversa con la solicitud asociada.
     * 
     * Un presupuesto siempre está vinculado a una solicitud concreta 
     * (la petición realizada por un cliente).  
     * La clave foránea `solicitud_id` apunta a la tabla `solicitudes`.
     * 
     * Ejemplo de uso:
     * $solicitud = $presupuesto->solicitud;
     */
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }

    /**
     * Relación uno a uno con el trabajo generado a partir del presupuesto.
     * 
     * Una vez que un presupuesto es aceptado por el cliente, se crea 
     * un registro en la tabla `trabajos` asociado a ese presupuesto.  
     * Esta relación se establece mediante la clave foránea `presu_id`
     * en la tabla `trabajos`.
     * 
     * Ejemplo de uso:
     * $trabajo = $presupuesto->trabajo;
     */
    public function trabajo()
    {
        return $this->hasOne(Trabajo::class, 'presu_id');
    }
}
