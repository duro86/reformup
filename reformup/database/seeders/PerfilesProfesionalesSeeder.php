<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perfil_Profesional;

class PerfilesProfesionalesSeeder extends Seeder
{
    public function run()
    {
        Perfil_Profesional::create([
            'user_id' => 1,
            'empresa' => 'Empresa A',
            'cif' => 'A12345678',
            'email_empresa' => 'empresaA@email.com',
            'bio' => 'Profesional con más de 10 años en reformas y construcción.',
            'web' => 'https://www.empresaA.com',
            'telefono_empresa' => '123456789',
            'ciudad' => 'Ciudad A',
            'dir_empresa' => 'Calle A 1',
            'puntuacion_media' => 4.5,
            'trabajos_realizados' => 1,
            'visible' => true,
        ]);

        Perfil_Profesional::create([
            'user_id' => 3,
            'empresa' => 'Empresa B',
            'cif' => 'B98765432',
            'email_empresa' => 'empresaB@email.com',
            'bio' => 'Profesional especializado en reformas eléctricas y mantenimiento.',
            'web' => 'https://www.empresaB.com',
            'telefono_empresa' => '987654321',
            'ciudad' => 'Ciudad B',
            'dir_empresa' => 'Calle B 2',
            'puntuacion_media' => 4.8,
            'trabajos_realizados' => 3,
            'visible' => true,
        ]);
    }
}


