<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Comentario;

class ComentarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Comentario::create([
            'trabajo_id' => 1,      // id del trabajo realizado
            'cliente_id' => 2,      // cliente que comenta
            'opinion' => 'Trabajo bien hecho, muy profesional y rápido.',
            'puntuacion' => 5,
            'fecha' => now(),
            'estado' => 'pendiente',
            'visible' => true
        ]);

    }
}
