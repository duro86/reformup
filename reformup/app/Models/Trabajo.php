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
    use SoftDeletes;

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
     * Una vez finalizado un trabajo, el cliente puede dejar uno o más comentarios
     * o valoraciones asociados a ese trabajo.  
     * Esta relación se establece mediante la clave foránea `trabajo_id`
     * en la tabla `comentarios`.
     * 
     * Ejemplo de uso:
     * $comentarios = $trabajo->comentarios;
    */
    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'trabajo_id');
    }

    /**
     * Relación polimórfica uno a muchos con el modelo Trabajo.
     * 
     * Permite asociar archivos (imágenes, documentos, PDFs, etc.) a cada trabajo
     * sin necesidad de crear tablas específicas para cada tipo de entidad.
     * 
     * Los campos `model_type` y `model_id` de la tabla `medios` indican a qué
     * modelo pertenece cada archivo.
    */
    public function medios() {
        return $this->morphMany(Medio::class, 'model');
    }
}
