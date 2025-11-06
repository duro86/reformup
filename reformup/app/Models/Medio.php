<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medio extends Model
{

    protected $table = 'perfiles_profesionales';
    /**
     * Atributos que se pueden asignar de forma masiva (mass assignable).
     * 
     * Define los campos que pueden rellenarse al crear un registro de medio
     * (imagen, documento, vídeo, etc.) asociado a distintos modelos del sistema.
     * 
     * Este modelo implementa una relación polimórfica, lo que permite que
     * varias entidades (Solicitud, Proyecto, Comentario, Perfil_Profesional, etc.)
     * compartan el mismo sistema de archivos adjuntos.
     * Campos:
     * - ruta: ubicación o path del archivo dentro del storage.
     * - tipo: tipo de medio (‘imagen’, ‘pdf’, ‘video’, etc.).
     * - titulo: título descriptivo del archivo (opcional).
     * - orden: número para ordenar los archivos relacionados.
    */
    protected $fillable = ['ruta', 'tipo', 'titulo', 'orden'];

    /**
     * Relación polimórfica inversa con cualquier modelo que pueda tener medios.
     * 
     * Gracias al método `morphTo()`, un registro en la tabla `medios`
     * puede pertenecer a distintos modelos.  
     * Los campos `model_type` y `model_id` determinan esa relación dinámica.
     * 
     * Ejemplo de uso:
     * $medio->model; // devuelve la entidad (Solicitud, Perfil, Comentario...) a la que pertenece
    */
    public function model()
    {
        return $this->morphTo();
    }
}
