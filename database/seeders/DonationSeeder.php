<?php

namespace Database\Seeders;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\Pet;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('es_ES');
        $donorIds = Donor::pluck('id');
        $petIds = Pet::pluck('id');
        $contributionTypes = ['cash', 'transfer', 'in_kind'];

        for ($i = 0; $i < 30; $i++) {
            Donation::create([
                'donor_id' => $faker->randomElement($donorIds),
                'amount' => $faker->randomFloat(2, 50, 2000),
                'contribution_type' => $faker->randomElement($contributionTypes),
                'pet_id' => $faker->optional(0.4)->randomElement($petIds),
            ]);
        }
    }
}
