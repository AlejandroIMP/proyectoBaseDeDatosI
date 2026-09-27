<?php

namespace Database\Seeders;

use App\Models\Species;
use Illuminate\Database\Seeder;

class SpeciesSeeder extends Seeder
{
    public function run(): void
    {
        $species = [
            ['name' => 'Perro', 'description' => 'Canino doméstico'],
            ['name' => 'Gato', 'description' => 'Felino doméstico'],
            ['name' => 'Ave', 'description' => 'Especie aviar doméstica'],
            ['name' => 'Conejo', 'description' => 'Lagomorfo doméstico'],
            ['name' => 'Reptil', 'description' => 'Especie reptil doméstica'],
        ];

        foreach ($species as $item) {
            Species::create($item);
        }
    }
}
