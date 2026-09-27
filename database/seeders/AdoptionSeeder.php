<?php

namespace Database\Seeders;

use App\Models\Adoption;
use App\Models\AdoptionStatus;
use App\Models\Person;
use App\Models\Pet;
use App\Models\PetStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdoptionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('es_ES');
        $personIds = Person::pluck('id');
        $userIds = User::pluck('id');
        $statuses = AdoptionStatus::pluck('id', 'name');
        $adoptedPetStatusId = PetStatus::where('name', 'Adoptado')->value('id');

        // Estados de adopción ponderados: la mayoría entregadas o aprobadas.
        $weightedStatuses = [
            'Entregada', 'Entregada', 'Entregada', 'Entregada',
            'Aprobada', 'Aprobada', 'Aprobada',
            'Pendiente', 'Pendiente',
            'En revisión',
            'Rechazada',
            'Cancelada',
        ];

        $pets = Pet::inRandomOrder()->limit(25)->get();

        foreach ($pets as $pet) {
            $statusName = $faker->randomElement($weightedStatuses);
            $requestedAt = $faker->dateTimeBetween($pet->registered_at->format('Y-m-d'), 'now');

            $approvedAt = null;
            $deliveredAt = null;

            if (in_array($statusName, ['Aprobada', 'Entregada'])) {
                $approvedAt = $faker->dateTimeBetween($requestedAt, 'now');
            }

            if ($statusName === 'Entregada') {
                $deliveredAt = $faker->dateTimeBetween($approvedAt, 'now');
            }

            Adoption::create([
                'pet_id' => $pet->id,
                'person_id' => $faker->randomElement($personIds),
                'requested_at' => $requestedAt->format('Y-m-d'),
                'approved_at' => $approvedAt?->format('Y-m-d'),
                'delivered_at' => $deliveredAt?->format('Y-m-d'),
                'notes' => $faker->optional(0.5)->sentence(10),
                'document' => $faker->optional(0.6)->passthrough('documents/adoptions/solicitud-'.$pet->id.'.pdf'),
                'adoption_status_id' => $statuses[$statusName],
                'user_id' => $faker->randomElement($userIds),
            ]);

            if ($statusName === 'Entregada') {
                $pet->update(['pet_status_id' => $adoptedPetStatusId]);
            }
        }
    }
}
