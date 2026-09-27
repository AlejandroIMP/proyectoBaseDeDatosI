<?php

namespace Database\Seeders;

use App\Models\Treatment;
use App\Models\TreatmentType;
use Illuminate\Database\Seeder;

class TreatmentSeeder extends Seeder
{
    public function run(): void
    {
        $treatments = [
            'Vacunación' => [
                ['name' => 'Vacuna antirrábica', 'requires_veterinarian' => true, 'cost' => 75.00],
                ['name' => 'Vacuna múltiple (polivalente)', 'requires_veterinarian' => true, 'cost' => 120.00],
            ],
            'Desparasitación' => [
                ['name' => 'Desparasitación interna', 'requires_veterinarian' => false, 'cost' => 45.00],
                ['name' => 'Desparasitación externa', 'requires_veterinarian' => false, 'cost' => 50.00],
            ],
            'Esterilización' => [
                ['name' => 'Esterilización canina/felina (hembra)', 'requires_veterinarian' => true, 'cost' => 350.00],
                ['name' => 'Castración (macho)', 'requires_veterinarian' => true, 'cost' => 280.00],
            ],
            'Cirugía' => [
                ['name' => 'Cirugía de emergencia', 'requires_veterinarian' => true, 'cost' => 900.00],
                ['name' => 'Extracción de tumor', 'requires_veterinarian' => true, 'cost' => 650.00],
            ],
            'Consulta general' => [
                ['name' => 'Consulta general', 'requires_veterinarian' => true, 'cost' => 100.00],
                ['name' => 'Chequeo post-rescate', 'requires_veterinarian' => true, 'cost' => 150.00],
            ],
            'Tratamiento dental' => [
                ['name' => 'Limpieza dental', 'requires_veterinarian' => true, 'cost' => 200.00],
                ['name' => 'Extracción dental', 'requires_veterinarian' => true, 'cost' => 300.00],
            ],
        ];

        foreach ($treatments as $typeName => $items) {
            $type = TreatmentType::where('name', $typeName)->first();

            foreach ($items as $item) {
                Treatment::create([
                    ...$item,
                    'treatment_type_id' => $type->id,
                ]);
            }
        }
    }
}
