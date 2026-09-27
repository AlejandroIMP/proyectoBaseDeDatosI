<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\PetStatus;
use App\Models\Rescue;
use App\Models\User;
use Illuminate\Database\Seeder;

class RescueSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('es_ES');
        $userIds = User::pluck('id');
        $petStatusIds = PetStatus::pluck('id');

        $notes = [
            'Animal encontrado en la vía pública en condición vulnerable.',
            'Rescate reportado por vecinos del sector.',
            'Animal abandonado, se realizó rescate inmediato.',
            'Rescate producto de denuncia por maltrato animal.',
            'Animal encontrado herido, trasladado para atención veterinaria.',
        ];

        $pets = Pet::inRandomOrder()->limit(20)->get();

        foreach ($pets as $pet) {
            Rescue::create([
                'user_id' => $faker->randomElement($userIds),
                'pet_id' => $pet->id,
                'notes' => $faker->randomElement($notes),
                'pet_status_id' => $faker->randomElement($petStatusIds),
                'rescued_at' => $faker->dateTimeBetween($pet->birth_date->format('Y-m-d'), $pet->registered_at->format('Y-m-d'))->format('Y-m-d'),
            ]);
        }
    }
}
