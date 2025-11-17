<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perfil_Profesional extends Model
{
    //Modelo para Perfiles Profesionales
    //use SoftDeletes;

    protected $table = 'perfiles_profesionales';

    /**
     * Atributos que se pueden asignar de forma masiva (mas asignable).
     * 
     * Define los campos que pueden ser rellenados mediante asignación masiva en operaciones como `Perfil_Profesional::create()` o `$perfil->update()`.
     * Campos:
     * - user_id: ID del usuario propietario del perfil (relación 1:1 con users).
     * - empresa: nombre comercial o razón social del profesional o empresa.
     * - cif: identificador fiscal único del profesional o empresa.
     * - email_empresa: correo de contacto público o profesional.
     * - bio: descripción breve o presentación del profesional.
     * - web: URL del sitio web o portafolio.
     * - telefono_empresa: número de contacto profesional.
     * - ciudad,provincia: ubicación principal del profesional.
     * - dir_empresa: dirección física de la empresa o despacho.
     * - puntuacion_media: valor numérico promedio de reseñas recibidas.
     * - trabajos_realizados: contador de obras o proyectos completados.
     * - visible: indica si el perfil está activo o visible en el portal (booleano).
     * - avatar: imagen o logo de perfil (ruta o URL del archivo).
     */
    protected $fillable = [
        'user_id',
        'empresa',
        'cif',
        'email_empresa',
        'bio',
        'web',
        'telefono_empresa',
        'ciudad',
        'provincia',
        'dir_empresa',
        'puntuacion_media',
        'trabajos_realizados',
        'visible',
        'avatar'
    ];

    /**
     * Relación inversa uno a uno con el modelo User.
     * 
     * Cada perfil profesional pertenece a un único usuario en la tabla `users`.
     * La clave foránea `user_id` en `perfiles_profesionales` referencia `users.id`.
     * 
     * Ejemplo de uso:
     * $usuario = $perfil->user;
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación muchos a muchos entre perfiles profesionales y oficios.
     * 
     * Un profesional puede tener varios oficios (electricista, fontanero, etc.),
     * y cada oficio puede pertenecer a varios profesionales.
     * 
     * Esta relación usa la tabla pivote `profesional_oficio`,
     * con las claves `pro_id` (perfil profesional) y `oficio_id`.
     * 
     * Se incluye `withTimestamps()` para registrar automáticamente
     * las fechas de creación/actualización en la tabla intermedia.
     * 
     * Ejemplo de uso:
     * $oficios = $perfil->oficios;  
     * $perfil->oficios()->attach($oficioId);
     */
    public function oficios()
    {
        return $this->belongsToMany(Oficio::class, 'profesional_oficio', 'pro_id', 'oficio_id')->withTimestamps();
    }

    /**
     * Relación uno a muchos entre un perfil profesional y las solicitudes recibidas.
     * 
     * Cada perfil profesional puede recibir múltiples solicitudes de presupuesto
     * de clientes interesados. Estas solicitudes están vinculadas mediante
     * el campo `pro_id` en la tabla `solicitudes`.
     * 
     * Ejemplo de uso:
     * $solicitudes = $perfil->solicitudes;
     */
    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'pro_id');
    }

    /**
     * Relación uno a muchos entre un perfil profesional y los presupuestos generados.
     * 
     * Cada perfil puede emitir varios presupuestos en respuesta a solicitudes.
     * Se enlazan mediante la clave foránea `pro_id` en la tabla `presupuestos`.
     * 
     * Ejemplo de uso:
     * $presupuestos = $perfil->presupuestos;
     */
    public function presupuestos()
    {
        return $this->hasMany(Presupuesto::class, 'pro_id');
    }

    /**
     * Relación polimórfica uno a muchos para archivos multimedia (imágenes, documentos, etc.).
     * 
     * Permite que distintos modelos (Solicitud, Proyecto, Comentario, Perfil_Profesional, etc.)
     * puedan tener medios asociados en la tabla `medios`, sin necesidad de crear
     * una tabla separada para cada tipo de archivo.
     * 
     * Los campos `model_type` y `model_id` en la tabla `medios`
     * indican a qué modelo y registro pertenece cada archivo.
     * 
     * Ejemplo de uso:
     * $perfil->medios()->create(['ruta' => 'uploads/foto.jpg', 'tipo' => 'imagen']);
     */
    public function medios()
    {
        return $this->morphMany(Medio::class, 'model');
    }

    /**
     * Relación uno a muchos (indirecta) entre un perfil profesional y sus trabajos.
     * 
     * Cadena de relación:
     *   perfiles_profesionales (id)
     *      -> presupuestos (pro_id)
     *          -> trabajos (presu_id)
     *
     * Con esto puedes obtener todos los trabajos asociados a un profesional
     * pasando por los presupuestos que él mismo ha emitido.
     * 
     * Ejemplo de uso:
     * $trabajos = $perfil->trabajos;
     */
    public function trabajos()
    {
        return $this->hasManyThrough(
            Trabajo::class,      // Modelo final
            Presupuesto::class,  // Modelo intermedio
            'pro_id',            // FK en presupuestos que apunta a perfiles_profesionales
            'presu_id',          // FK en trabajos que apunta a presupuestos
            'id',                // PK en perfiles_profesionales
            'id'                 // PK en presupuestos
        );
    }
}
