<?php

namespace Database\Seeders;

use App\Models\Occupation;
use Illuminate\Database\Seeder;

class OccupationSeeder extends Seeder
{
    public function run(): void
    {
        $occupations = [
            ['name' => 'Empleado de oficina', 'formalidad' => 'Formal', 'horarios' => 'Lunes a viernes 8:00-17:00'],
            ['name' => 'Comerciante', 'formalidad' => 'Informal', 'horarios' => 'Horario variable'],
            ['name' => 'Maestro', 'formalidad' => 'Formal', 'horarios' => 'Lunes a viernes 7:00-14:00'],
            ['name' => 'Médico', 'formalidad' => 'Formal', 'horarios' => 'Turnos rotativos'],
            ['name' => 'Estudiante', 'formalidad' => 'Informal', 'horarios' => 'Horario académico variable'],
            ['name' => 'Agricultor', 'formalidad' => 'Informal', 'horarios' => 'De amanecer a atardecer'],
            ['name' => 'Ama de casa', 'formalidad' => 'Informal', 'horarios' => 'Tiempo completo en el hogar'],
            ['name' => 'Ingeniero', 'formalidad' => 'Formal', 'horarios' => 'Lunes a viernes 8:00-18:00'],
            ['name' => 'Conductor', 'formalidad' => 'Informal', 'horarios' => 'Horario variable por rutas'],
            ['name' => 'Jubilado', 'formalidad' => 'Informal', 'horarios' => 'Sin horario fijo'],
        ];

        foreach ($occupations as $item) {
            Occupation::create($item);
        }
    }
}
