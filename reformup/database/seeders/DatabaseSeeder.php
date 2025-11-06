<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Llamamos a los seeders
        $this->call([
            UsersSeeder::class,
            OficiosSeeder::class,
            PerfilesProfesionalesSeeder::class,
            ProfesionalOficioSeeder::class,
            SolicitudSeeder::class,
            PresupuestoSeeder::class,
            TrabajoSeeder::class,
            ComentarioSeeder::class,
        ]);
    }
}
