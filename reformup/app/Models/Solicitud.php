<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solicitud extends Model
{
    //Modelo para Solicitudes
    use SoftDeletes;
    /**
         * Atributos que se pueden asignar de forma masiva (mas asignable).
         * 
         * Define los campos permitidos al crear o actualizar una solicitud de presupuesto.
         * Laravel solo permitirá la asignación de los atributos listados aquí, protegiendo
         * contra modificaciones accidentales o no autorizadas.
         * Campos:
         * - pro_id: ID del profesional destinatario (si la solicitud es dirigida).
         * - cliente_id: ID del usuario que realiza la solicitud.
         * - titulo: resumen breve de la solicitud.
         * - descripcion: detalle de la necesidad o reforma solicitada.
         * - ciudad,provincia: ubicación donde se realizará el trabajo.
         * - dir_empresa: dirección exacta del inmueble o lugar de la obra.
         * - estado: estado actual de la solicitud (‘abierta’, ‘cerrada’, etc.).
         * - presupuesto_max: límite económico indicado por el cliente.
         * - fecha: fecha en la que se genera o actualiza la solicitud.
    */
    protected $fillable = [
        'pro_id', 'cliente_id', 'titulo', 'descripcion',
        'ciudad', 'provincia', 'dir_empresa', 'estado', 'presupuesto_max', 'fecha'
    ];

    /**
     * Relación inversa con el perfil profesional (profesional que recibe la solicitud).
     * 
     * Cada solicitud puede estar dirigida a un único profesional, mediante la clave
     * foránea `pro_id` que enlaza con `perfiles_profesionales.id`.
     * 
     * Ejemplo de uso:
     * $profesional = $solicitud->profesional;
    */
    public function profesional()
    {
        return $this->belongsTo(Perfil_Profesional::class, 'pro_id');
    }

    /**
     * Relación inversa con el cliente (usuario que crea la solicitud).
     * 
     * Cada solicitud pertenece a un único cliente, representado por el modelo `User`.
     * La clave foránea `cliente_id` conecta con `users.id`.
     * 
     * Ejemplo de uso:
     * $cliente = $solicitud->cliente;
    */
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    /**
     * Relación uno a muchos con los presupuestos.
     * 
     * Una solicitud puede recibir varios presupuestos por parte de distintos
     * profesionales. Cada presupuesto está vinculado mediante la clave
     * foránea `solicitud_id` en la tabla `presupuestos`.
     * 
     * Ejemplo de uso:
     * $presupuestos = $solicitud->presupuestos;
    */
    public function presupuestos()
    {
        return $this->hasMany(Presupuesto::class, 'solicitud_id');
    }

    /**
     * Relación polimórfica uno a muchos con el modelo Solicitud.
     * 
     * Permite asociar archivos (imágenes, documentos, PDFs, etc.) a cada solicitud
     * sin necesidad de crear tablas específicas para cada tipo de entidad.
     * 
     * Los campos `model_type` y `model_id` de la tabla `medios` indican a qué
     * modelo pertenece cada archivo.
    */
    public function medios() {
        return $this->morphMany(Medio::class, 'model');
    }
}
