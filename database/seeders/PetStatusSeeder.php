<?php

namespace Database\Seeders;

use App\Models\PetStatus;
use Illuminate\Database\Seeder;

class PetStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            'Disponible',
            'En tratamiento',
            'En cuarentena',
            'Reservado',
            'Adoptado',
            'Fallecido',
        ];

        foreach ($statuses as $name) {
            PetStatus::create(['name' => $name]);
        }
    }
}
