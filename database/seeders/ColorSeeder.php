<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['name' => 'Negro', 'hex_color' => '#000000'],
            ['name' => 'Blanco', 'hex_color' => '#FFFFFF'],
            ['name' => 'Café', 'hex_color' => '#6F4E37'],
            ['name' => 'Gris', 'hex_color' => '#808080'],
            ['name' => 'Dorado', 'hex_color' => '#FFD700'],
            ['name' => 'Naranja', 'hex_color' => '#FFA500'],
            ['name' => 'Marrón claro', 'hex_color' => '#C4A484'],
            ['name' => 'Atigrado', 'hex_color' => null],
            ['name' => 'Manchado', 'hex_color' => null],
            ['name' => 'Blanco y negro', 'hex_color' => null],
        ];

        foreach ($colors as $item) {
            Color::create($item);
        }
    }
}
