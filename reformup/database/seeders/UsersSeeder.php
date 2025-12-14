<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // ADMIN
        $admin = User::create([
            'nombre' => 'Juan',
            'apellidos' => 'Martinez',
            'email' => 'juan@email.com',
            'password' => bcrypt('123456'), // asegurar que las contraseñas estén encriptadas
            'telefono' => '123456789',
            'ciudad' => 'Huelva',
        ]);
        $admin->assignRole('admin');

        // USUARIO NORMAL (cliente)
        $usuario = User::create([
            'nombre' => 'Ana',
            'apellidos' => 'Gomez',
            'email' => 'ana@email.com',
            'password' => bcrypt('123456'),
            'telefono' => '987654321',
            'ciudad' => 'Sevilla',
        ]);
        $usuario->assignRole('usuario');


        // PROFESIONAL
        $profesional = User::create([
            'nombre' => 'Alvaro',
            'apellidos' => 'Gomez',
            'email' => 'alvaro@email.com',
            'password' => bcrypt('123456'),
            'telefono' => '987654321',
            'ciudad' => 'Sevilla',
        ]);
        $profesional->assignRole('profesional');
    }
}

