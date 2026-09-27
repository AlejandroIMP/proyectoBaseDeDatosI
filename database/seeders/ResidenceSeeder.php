<?php

namespace Database\Seeders;

use App\Models\Residence;
use Illuminate\Database\Seeder;

class ResidenceSeeder extends Seeder
{
    public function run(): void
    {
        $residences = [
            ['name' => 'Apartamento pequeño', 'animal_limit' => 1],
            ['name' => 'Apartamento grande', 'animal_limit' => 2],
            ['name' => 'Casa sin patio', 'animal_limit' => 2],
            ['name' => 'Casa con patio pequeño', 'animal_limit' => 3],
            ['name' => 'Casa con patio grande', 'animal_limit' => 5],
            ['name' => 'Finca o terreno amplio', 'animal_limit' => 10],
        ];

        foreach ($residences as $item) {
            Residence::create($item);
        }
    }
}
