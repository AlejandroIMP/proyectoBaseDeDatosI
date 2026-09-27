<?php

namespace Database\Seeders;

use App\Models\Adoption;
use App\Models\AdoptionFollowUp;
use App\Models\AdoptionStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdoptionFollowUpSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('es_ES');
        $userIds = User::pluck('id');
        $deliveredStatusId = AdoptionStatus::where('name', 'Entregada')->value('id');

        $notes = [
            'La mascota se ha adaptado correctamente a su nuevo hogar.',
            'Familia reporta buena convivencia y salud del animal.',
            'Se recomienda seguimiento adicional en próximas semanas.',
            'Visita de seguimiento sin observaciones relevantes.',
            'Adoptante confirma cumplimiento de cuidados básicos.',
        ];

        $deliveredAdoptions = Adoption::where('adoption_status_id', $deliveredStatusId)->get();

        foreach ($deliveredAdoptions as $adoption) {
            $count = $faker->numberBetween(1, 3);

            for ($i = 0; $i < $count; $i++) {
                AdoptionFollowUp::create([
                    'adoption_id' => $adoption->id,
                    'followed_at' => $faker->dateTimeBetween($adoption->delivered_at->format('Y-m-d'), 'now')->format('Y-m-d'),
                    'notes' => $faker->randomElement($notes),
                    'user_id' => $faker->randomElement($userIds),
                ]);
            }
        }
    }
}
