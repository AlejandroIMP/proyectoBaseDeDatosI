<?php

namespace Database\Seeders;

use App\Models\AdoptionStatus;
use Illuminate\Database\Seeder;

class AdoptionStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            'Pendiente',
            'En revisión',
            'Aprobada',
            'Rechazada',
            'Entregada',
            'Cancelada',
        ];

        foreach ($statuses as $name) {
            AdoptionStatus::create(['name' => $name]);
        }
    }
}
