<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Solicitud;

class SolicitudSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Solicitud::create([
            'pro_id' => 1, // id del profesional (de la tabla perfiles_profesionales)
            'cliente_id' => 1, // id del user que hace la solicitud
            'titulo' => 'Reforma de baño',
            'descripcion' => 'Necesito renovar el azulejo y cambiar la fontanería.',
            'ciudad' => 'Huelva',
            'provincia' => 'Huelva',
            'dir_empresa' => 'Calle Flor, 25',
            'estado' => 'abierta',
            'presupuesto_max' => 2000,
            'fecha' => now(),
        ]);

    }
}
