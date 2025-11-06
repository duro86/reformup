<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfesionalOficio extends Model
{
    protected $table = 'profesional_oficio';

    public $timestamps = false; // Normalmente una tabla pivote no tiene timestamps

    protected $fillable = [
        'pro_id',
        'oficio_id',
    ];
}

