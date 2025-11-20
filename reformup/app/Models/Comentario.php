<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comentario extends Model
{
    //
    //use SoftDeletes;

    protected $table = 'comentarios';

    /**
     * Atributos que se pueden asignar de forma masiva (mass assignable).
     * 
     * Define los campos que pueden asignarse al crear o actualizar un comentario.
     * Laravel solo permitirá modificar los atributos incluidos aquí, protegiendo
     * el modelo frente a asignaciones masivas no deseadas.
     * Campos:
     * - trabajo_id: ID del trabajo al que pertenece el comentario.
     * - cliente_id: ID del usuario que deja la reseña.
     * - puntuacion: valoración numérica (1 a 5 estrellas).
     * - estado: campo opcional para marcar validación o moderación.
     * - opinion: texto con la opinión o reseña del cliente.
     * - visible: indica si la reseña está publicada (booleano).
     * - fecha: fecha en que se realiza la valoración.
     */
    protected $fillable = [
        'trabajo_id',
        'cliente_id',
        'puntuacion',
        'estado',
        'opinion',
        'visible',
        'fecha'
    ];

    protected $casts = [
        'visible' => 'boolean',
        'fecha'   => 'datetime',
    ];

    public const ESTADOS = [
        'pendiente' => 'Pendientes',
        'publicado' => 'Publicados',
        'rechazado' => 'Rechazados',
    ];

    /**
     * Relación inversa con el trabajo asociado.
     * 
     * Cada comentario pertenece a un único trabajo, identificado mediante
     * la clave foránea `trabajo_id` en la tabla `comentarios`.
     * 
     * Ejemplo de uso:
     * $trabajo = $comentario->trabajo;
     */
    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class, 'trabajo_id');
    }
    /**
     * Relación inversa con el cliente (usuario que realiza la valoración).
     * 
     * Cada comentario es creado por un usuario (cliente), enlazado mediante
     * la clave foránea `cliente_id` hacia la tabla `users`.
     * 
     * Ejemplo de uso:
     * $cliente = $comentario->cliente;
     */
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    /**
     * Relación polimórfica uno a muchos con el modelo Comentario.
     * 
     * Permite asociar archivos (imágenes, documentos, PDFs, etc.) a cada comentario
     * sin necesidad de crear tablas específicas para cada tipo de entidad.
     * 
     * Los campos `model_type` y `model_id` de la tabla `medios` indican a qué
     * modelo pertenece cada archivo.
     */
    public function medios()
    {
        return $this->morphMany(Medio::class, 'model');
    }
}
