<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComentarioImagen extends Model
{
    protected $table = 'comentario_imagenes';

    protected $fillable = [
        'comentario_id',
        'ruta',
        'orden',
    ];

    public function comentario()
    {
        return $this->belongsTo(Comentario::class);
    }

}
