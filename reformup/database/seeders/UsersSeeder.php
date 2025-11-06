<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'nombre' => 'Juan',
            'apellidos' => 'Martinez',
            'email' => 'juan@email.com',
            'password' => bcrypt('123'), // asegurar que las contraseñas estén encriptadas
            'telefono' => '123456789',
            'ciudad' => 'Huelva',
        ]);

        User::create([
            'nombre' => 'Ana',
            'apellidos' => 'Gomez',
            'email' => 'ana@email.com',
            'password' => bcrypt('123'),
            'telefono' => '987654321',
            'ciudad' => 'Sevilla',
        ]);

        //Usuario a borrar profesional para pruebas
        User::create([
            'nombre' => 'Alvaro',
            'apellidos' => 'Gomez',
            'email' => 'alvaro@email.com',
            'password' => bcrypt('123'),
            'telefono' => '987654321',
            'ciudad' => 'Sevilla',
        ]);
    }
}

