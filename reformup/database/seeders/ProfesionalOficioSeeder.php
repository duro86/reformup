<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfesionalOficioSeeder extends Seeder
{
    public function run()
    {
        DB::table('profesional_oficio')->insert([
            ['pro_id' => 1, 'oficio_id' => 1],
            ['pro_id' => 1, 'oficio_id' => 5],
            ['pro_id' => 2, 'oficio_id' => 2],
            ['pro_id' => 2, 'oficio_id' => 1]
        ]);
    }
}
