<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Pet;
use App\Models\PetStatus;
use App\Models\Shelter;
use App\Models\Species;
use Illuminate\Database\Seeder;

class PetSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('es_ES');

        $speciesList = Species::with('breeds')->get();
        $colorIds = Color::pluck('id');
        $petStatusIds = PetStatus::pluck('id');
        $shelterIds = Shelter::pluck('id');

        $names = [
            'Rocky', 'Luna', 'Max', 'Bella', 'Toby', 'Nala', 'Simba', 'Coco',
            'Firulais', 'Pelusa', 'Manchas', 'Sombra', 'Canela', 'Kiara', 'Thor',
            'Milo', 'Lola', 'Zeus', 'Pipo', 'Chispa', 'Rex', 'Mia', 'Duke', 'Nube',
            'Pancho', 'Chiquita', 'Bruno', 'Fiona', 'Rambo', 'Estrella', 'Whiskers',
            'Copito', 'Tom', 'Michi', 'Bigotes', 'Dama', 'Capitán', 'Perla', 'Freddo',
            'Lucero',
        ];

        for ($i = 0; $i < 50; $i++) {
            $species = $speciesList->random();
            $breed = $species->breeds->random();

            $birthDate = $faker->dateTimeBetween('-15 years', '-2 months');
            $registeredAt = $faker->dateTimeBetween($birthDate, 'now');

            Pet::create([
                'name' => $faker->randomElement($names),
                'species_id' => $species->id,
                'breed_id' => $breed->id,
                'sex' => $faker->randomElement(['M', 'F']),
                'birth_date' => $birthDate->format('Y-m-d'),
                'estimated_age' => $faker->optional(0.7)->numberBetween(1, 180),
                'color_id' => $faker->randomElement($colorIds),
                'weight_kg' => $faker->optional(0.85)->randomFloat(2, 0.5, 45),
                'notes' => $faker->optional(0.4)->sentence(10),
                'pet_status_id' => $faker->randomElement($petStatusIds),
                'registered_at' => $registeredAt->format('Y-m-d'),
                'status' => $faker->boolean(90) ? 'active' : 'inactive',
                'shelter_id' => $faker->randomElement($shelterIds),
            ]);
        }
    }
}
