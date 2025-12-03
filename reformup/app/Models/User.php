<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Perfil_Profesional;
use App\Models\Solicitud;
use App\Models\Comentario;
use App\Notifications\ResetPasswordReformUp;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    /**
     * Atributos que se pueden asignar de forma masiva (mas asignable).
     * 
     * Laravel protege los modelos contra la asignación masiva no deseada
     * (mass assignment vulnerability). La propiedad `$fillable` define
     * explícitamente qué campos pueden recibir valores al crear o actualizar
     * un usuario mediante métodos como `User::create()` o `$user->update()`.
     * asignación masiva, evitando modificaciones accidentales o maliciosas.
     * 
     * Campos:
     * - nombre, apellidos: datos personales del usuario.
     * - email: correo único para autenticación.
     * - password: contraseña encriptada (bcrypt).
     * - telefono, ciudad, provincia, cp, direccion: datos de contacto.
     * - avatar: ruta o URL del archivo de imagen del perfil.
     */
    protected $fillable = [
        'nombre',
        'apellidos',
        'email',
        'password',
        'telefono',
        'ciudad',
        'provincia',
        'cp',
        'direccion',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación uno a uno entre un usuario y su perfil profesional.
     * 
     * Cada usuario puede tener (opcionalmente) un único perfil profesional
     * registrado en la tabla `perfiles_profesionales`, donde el campo `user_id`
     * actúa como clave foránea que enlaza con `users.id`.
     * 
     * Ejemplo de uso:
     * $perfil = $user->perfil_Profesional;
     */
    public function perfil_Profesional()
    {
        return $this->hasOne(Perfil_Profesional::class, 'user_id');
    }

    /**
     * Relación uno a muchos entre un usuario (cliente) y las solicitudes que crea.
     * 
     * Un mismo cliente puede generar múltiples solicitudes de presupuesto,
     * por eso esta relación usa `hasMany` con la clave foránea `cliente_id`
     * en la tabla `solicitudes`.
     * 
     * Ejemplo de uso:
     * $solicitudes = $user->solicitudes;
     */
    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'cliente_id');
    }

    /**
     * Relación uno a muchos entre un usuario (cliente) y sus comentarios o reseñas.
     * 
     * Cada cliente puede dejar varias reseñas asociadas a distintos trabajos,
     * y se relacionan mediante el campo `cliente_id` en la tabla `comentarios`.
     * 
     * Ejemplo de uso:
     * $comentarios = $user->comentarios;
     */
    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'cliente_id');
    }

    /**
     * Resetear contraseña
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordReformUp($token));
    }
}
