<?php

namespace Database\Seeders;

use App\Models\Occupation;
use App\Models\Person;
use App\Models\Residence;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('es_ES');
        $occupationIds = Occupation::pluck('id');
        $residenceIds = Residence::pluck('id');
        $estadosCiviles = ['Soltero(a)', 'Casado(a)', 'Divorciado(a)', 'Viudo(a)', 'Unión libre'];

        for ($i = 0; $i < 40; $i++) {
            $documentType = $faker->randomElement(['DPI', 'PASSPORT']);
            $documentNumber = $documentType === 'DPI'
                ? $faker->unique()->numerify('#############')
                : strtoupper($faker->unique()->bothify('??#######'));

            Person::create([
                'name' => $faker->name(),
                'document_type' => $documentType,
                'document_number' => $documentNumber,
                'email' => $faker->unique()->safeEmail(),
                'current_pets_count' => $faker->numberBetween(0, 3),
                'estado_civil' => $faker->randomElement($estadosCiviles),
                'cantidad_hijos' => $faker->numberBetween(0, 5),
                'occupation_id' => $faker->randomElement($occupationIds),
                'residence_id' => $faker->randomElement($residenceIds),
                'income' => $faker->numberBetween(2500, 25000),
            ]);
        }
    }
}
