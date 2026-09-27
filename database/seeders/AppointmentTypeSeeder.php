<?php

namespace Database\Seeders;

use App\Models\AppointmentType;
use Illuminate\Database\Seeder;

class AppointmentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Consulta general',
            'Vacunación',
            'Cirugía',
            'Revisión post-adopción',
            'Emergencia',
            'Esterilización',
        ];

        foreach ($types as $name) {
            AppointmentType::create(['name' => $name]);
        }
    }
}
