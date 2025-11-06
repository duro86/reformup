<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Presupuesto;

class PresupuestoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Presupuesto::create([
            'solicitud_id' => 1, // id de la solicitud recién creada
            'pro_id' => 1,       // profesional que lo responde
            'total' => 1800,
            'notas' => 'Incluye materiales y mano de obra, plazo de 2 semanas.',
            'estado' => 'aceptado',
            'fecha' => now(),
        ]);

    }
}
