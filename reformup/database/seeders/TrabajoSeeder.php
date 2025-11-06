<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Trabajo;

class TrabajoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Trabajo::create([
            'presu_id' => 1, // id del presupuesto aceptado
            'fecha_ini' => now(),
            'fecha_fin' => now()->addDays(14),
            'estado' => 'en_curso',
            'dir_obra' => 'Calle Flor, 30, Sevilla',
        ]);

    }
}
