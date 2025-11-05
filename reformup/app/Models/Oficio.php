<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Oficio extends Model
{
    /**
     * Modelo Oficio
     * 
     * Representa cada oficio o especialidad disponible en la plataforma,
     * como fontanero, electricista, albañil, pintor, etc.
     * 
     * Campos rellenables:
     * - nombre: nombre del oficio (ej. "Fontanero")
     * - slug: versión amigable del nombre para URLs (ej. "fontanero")
     * - descripcion: texto descriptivo del oficio
    */
    protected $fillable = ['nombre', 'slug', 'descripcion'];

    /**
     * Relación muchos a muchos con los perfiles profesionales.
     * 
     * Un oficio puede ser desempeñado por múltiples profesionales,
     * y a su vez, un profesional puede tener varios oficios asignados.
     * 
     * Esta relación utiliza la tabla pivote `profesional_oficio`,
     * donde:
     * - `oficio_id` referencia a este modelo (Oficio)
     * - `pro_id` referencia a Perfil_Profesional
     * 
     * Se incluye `withTimestamps()` para registrar automáticamente las
     * fechas de creación y actualización en la tabla intermedia.
     * 
     * Ejemplo de uso:
     * $oficio->profesionales; // obtiene todos los profesionales con ese oficio
     * $oficio->profesionales()->attach($perfilId); // asigna un profesional a un oficio
    */
    public function profesionales()
    {
        return $this->belongsToMany(Perfil_Profesional::class, 'profesional_oficio', 'oficio_id', 'pro_id')->withTimestamps();
    }
}
