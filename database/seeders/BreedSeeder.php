<?php

namespace Database\Seeders;

use App\Models\Breed;
use App\Models\Species;
use Illuminate\Database\Seeder;

class BreedSeeder extends Seeder
{
    public function run(): void
    {
        $breeds = [
            'Perro' => [
                'Mestizo' => 'Cruce de razas caninas',
                'Labrador' => 'Raza canina de tamaño grande',
                'Pastor Alemán' => 'Raza canina de trabajo',
                'Poodle' => 'Raza canina de pelo rizado',
                'Chihuahua' => 'Raza canina de tamaño pequeño',
            ],
            'Gato' => [
                'Mestizo' => 'Cruce de razas felinas',
                'Siamés' => 'Raza felina de pelo corto',
                'Persa' => 'Raza felina de pelo largo',
                'Angora' => 'Raza felina de pelo sedoso',
            ],
            'Ave' => [
                'Canario' => 'Ave cantora pequeña',
                'Periquito' => 'Ave pequeña de colores',
                'Loro' => 'Ave de gran tamaño y colorido',
            ],
            'Conejo' => [
                'Mestizo' => 'Cruce de razas de conejo',
                'Holandés' => 'Raza de conejo de tamaño medio',
                'Angora' => 'Raza de conejo de pelo largo',
            ],
            'Reptil' => [
                'Iguana' => 'Reptil de gran tamaño',
                'Tortuga' => 'Reptil de caparazón duro',
                'Gecko' => 'Reptil pequeño doméstico',
            ],
        ];

        foreach ($breeds as $speciesName => $items) {
            $species = Species::where('name', $speciesName)->first();

            foreach ($items as $name => $description) {
                Breed::create([
                    'species_id' => $species->id,
                    'name' => $name,
                    'description' => $description,
                ]);
            }
        }
    }
}
