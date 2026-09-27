<?php

namespace Database\Seeders;

use App\Models\Donor;
use App\Models\Person;
use Illuminate\Database\Seeder;

class DonorSeeder extends Seeder
{
    public function run(): void
    {
        $donorPeople = Person::inRandomOrder()->limit(15)->get();

        foreach ($donorPeople as $person) {
            Donor::create([
                'name' => $person->name,
                'person_id' => $person->id,
            ]);
        }
    }
}
