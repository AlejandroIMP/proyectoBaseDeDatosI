<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\PetHistory;
use App\Models\PetStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class PetHistorySeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('es_ES');
        $petStatusIds = PetStatus::pluck('id');
        $userIds = User::pluck('id');

        $notes = [
            'Ingreso al refugio, evaluación inicial realizada.',
            'Cambio de estado tras revisión veterinaria.',
            'Actualización de condición general del animal.',
            'Traslado a otra área del refugio.',
            'Seguimiento rutinario de salud.',
        ];

        Pet::all()->each(function (Pet $pet) use ($faker, $petStatusIds, $userIds, $notes) {
            $count = $faker->numberBetween(1, 3);

            for ($i = 0; $i < $count; $i++) {
                PetHistory::create([
                    'pet_id' => $pet->id,
                    'pet_status_id' => $faker->randomElement($petStatusIds),
                    'notes' => $faker->randomElement($notes),
                    'recorded_at' => $faker->dateTimeBetween($pet->registered_at->format('Y-m-d'), 'now')->format('Y-m-d'),
                    'user_id' => $faker->randomElement($userIds),
                ]);
            }
        });
    }
}
