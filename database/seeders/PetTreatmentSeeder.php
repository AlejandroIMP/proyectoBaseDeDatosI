<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\PetTreatment;
use App\Models\Treatment;
use Illuminate\Database\Seeder;

class PetTreatmentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('es_ES');
        $treatmentIds = Treatment::pluck('id');

        $instructions = [
            'Administrar según indicaciones del veterinario.',
            'Reposo y observación durante 48 horas.',
            'Aplicar dosis única, sin efectos secundarios esperados.',
            'Control de seguimiento en dos semanas.',
            'Mantener en ayunas 12 horas antes del procedimiento.',
        ];

        $pets = Pet::inRandomOrder()->limit(40)->get();

        foreach ($pets as $pet) {
            $count = $faker->numberBetween(1, 2);

            for ($i = 0; $i < $count; $i++) {
                PetTreatment::create([
                    'pet_id' => $pet->id,
                    'treatment_id' => $faker->randomElement($treatmentIds),
                    'instructions' => $faker->randomElement($instructions),
                    'treated_at' => $faker->dateTimeBetween($pet->registered_at->format('Y-m-d'), 'now')->format('Y-m-d'),
                ]);
            }
        }
    }
}
