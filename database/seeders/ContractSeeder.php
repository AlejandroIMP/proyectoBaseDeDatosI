<?php

namespace Database\Seeders;

use App\Models\Adoption;
use App\Models\AdoptionStatus;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('es_ES');
        $userIds = User::pluck('id');
        $deliveredStatusId = AdoptionStatus::where('name', 'Entregada')->value('id');

        $deliveredAdoptions = Adoption::where('adoption_status_id', $deliveredStatusId)->with('pet')->get();

        foreach ($deliveredAdoptions as $adoption) {
            Contract::create([
                'user_id' => $faker->randomElement($userIds),
                'adoption_id' => $adoption->id,
                'name' => 'Contrato de adopción - '.$adoption->pet->name,
                'document_path' => $faker->optional(0.7)->passthrough('documents/contracts/contrato-'.$adoption->id.'.pdf'),
            ]);
        }
    }
}
