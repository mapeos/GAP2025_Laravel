<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParticipacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $participaciones = [
            [
                'curso_id' => 1,
                'persona_id' => 3, // nombre Luz
                'rol_participacion_id' => 2, //  = Profesor
                'estado' => 'activo',
            ],
            [
                'curso_id' => 2,
                'persona_id' => 4, // nombre Benito
                'rol_participacion_id' => 3, //  Alumno
                'estado' => 'pendiente',
            ],

        ];

        foreach ($participaciones as $participacion) {
            DB::table('participacion')->insert($participacion);
        }
    }
}
