<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Oficio;
use Illuminate\Support\Str;

class OficiosSeeder extends Seeder
{
    public function run()
    {
        $oficios = [
            [
                'nombre' => 'albañil',
                'descripcion' => 'Especialista en construcción y reparación de estructuras de concreto y mampostería.'
            ],
            [
                'nombre' => 'electricista',
                'descripcion' => 'Profesional encargado de la instalación, mantenimiento y reparación de sistemas eléctricos.'
            ],
            [
                'nombre' => 'fontanero',
                'descripcion' => 'Experto en instalación y reparación de tuberías y sistemas de agua y calefacción.'
            ],
            [
                'nombre' => 'carpintero',
                'descripcion' => 'Trabaja la madera para fabricar, instalar y reparar muebles, puertas y estructuras.'
            ],
            [
                'nombre' => 'pintor',
                'descripcion' => 'Encargado de pintar y decorar interiores y exteriores de edificaciones.'
            ],
            [
                'nombre' => 'escayolista',
                'descripcion' => 'Se especializa en trabajos de yeso, molduras y acabados en paredes y techos.'
            ],
            [
                'nombre' => 'instalador_climatizacion',
                'descripcion' => 'Profesional que instala y mantiene sistemas de aire acondicionado y climatización.'
            ],
            [
                'nombre' => 'cerrajero',
                'descripcion' => 'Especialista en cerraduras, llaves y seguridad para puertas y ventanas.'
            ],
            [
                'nombre' => 'arquitecto',
                'descripcion' => 'Diseña y planifica edificaciones y proyectos arquitectónicos.'
            ],
            [
                'nombre' => 'ingeniero_civil',
                'descripcion' => 'Encargado del diseño, construcción y mantenimiento de infraestructuras civiles.'
            ],
        ];

        foreach ($oficios as $oficio) {
            Oficio::create([
                'nombre' => $oficio['nombre'],
                'descripcion' => $oficio['descripcion'],
                'slug' => Str::slug($oficio['nombre'], '-') // Genera slug tipo "instalador-climatizacion",
        ]);
    }
}
}
