<?php

namespace Database\Seeders;

use App\Models\Shelter;
use Illuminate\Database\Seeder;

class ShelterSeeder extends Seeder
{
    public function run(): void
    {
        $shelters = [
            [
                'name' => 'Refugio Esperanza Animal',
                'address' => 'Zona 10, Ciudad de Guatemala',
                'phone' => '2234-5678',
                'email' => 'contacto@esperanzaanimal.org',
            ],
            [
                'name' => 'Refugio Patitas Felices',
                'address' => 'Zona 7, Quetzaltenango',
                'phone' => '7761-2345',
                'email' => 'info@patitasfelices.org',
            ],
            [
                'name' => 'Refugio Huellas de Amor',
                'address' => 'Zona 3, Escuintla',
                'phone' => '7889-9012',
                'email' => 'contacto@huellasdeamor.org',
            ],
        ];

        foreach ($shelters as $item) {
            Shelter::create($item);
        }
    }
}
