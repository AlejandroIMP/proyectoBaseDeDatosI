<?php

namespace Database\Seeders;

use App\Models\TreatmentType;
use Illuminate\Database\Seeder;

class TreatmentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Vacunación',
            'Desparasitación',
            'Esterilización',
            'Cirugía',
            'Consulta general',
            'Tratamiento dental',
        ];

        foreach ($types as $name) {
            TreatmentType::create(['name' => $name]);
        }
    }
}
