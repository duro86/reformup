<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Presupuesto;
use App\Models\Comentario;
use App\Models\Medio;

class Trabajo extends Model
{
    //
    //use SoftDeletes;

    protected $table = 'trabajos';

    /**
     * Atributos que se pueden asignar de forma masiva (mass assignable).
     * 
     * Define los campos permitidos al crear o actualizar un registro de trabajo.
     * Laravel protege los demás campos de ser modificados mediante asignación masiva.
     * Campos:
     * - presu_id: ID del presupuesto aceptado del cual deriva el trabajo.
     * - fecha_ini: fecha de inicio del trabajo (nullable).
     * - fecha_fin: fecha de finalización del trabajo (nullable).
     * - estado: estado actual del trabajo (‘pendiente’, ‘en_progreso’, ‘finalizado’, ‘cancelado’).
     * - dir_obra: dirección exacta o ubicación donde se realiza la obra.
     */
    protected $fillable = ['presu_id', 'fecha_ini', 'fecha_fin', 'estado', 'dir_obra'];

    protected $casts = [
        'fecha_ini' => 'datetime',
        'fecha_fin' => 'datetime'
    ];

    public const ESTADOS = [
        'previsto' => 'Previstos',
        'en_curso' => 'En curso',
        'finalizado' => 'Finalizados',
        'cancelado' => 'Cancelados',
    ];

    /**
     * Relación inversa con el presupuesto asociado.
     * 
     * Cada trabajo pertenece a un único presupuesto que fue previamente aceptado.
     * La clave foránea `presu_id` enlaza con la tabla `presupuestos`.
     * 
     * Ejemplo de uso:
     * $presupuesto = $trabajo->presupuesto;
     */
    public function presupuesto()
    {
        return $this->belongsTo(Presupuesto::class, 'presu_id');
    }

    /**
     * Relación uno a muchos con los comentarios o reseñas del cliente.
     *
     * La base de datos permite varios comentarios por trabajo (relación 1:N),
     * pero en la versión actual de la aplicación solo se utiliza un comentario
     * por trabajo, ya que el flujo de valoraciones está limitado a una reseña
     * por cliente y trabajo.
     */
    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'trabajo_id');
    }
}
