<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrador', 'description' => 'Acceso total al sistema'],
            ['name' => 'Veterinario', 'description' => 'Gestión de tratamientos y salud animal'],
            ['name' => 'Recepcionista', 'description' => 'Atención, citas y registro de adopciones'],
            ['name' => 'Voluntario', 'description' => 'Apoyo en rescates y cuidado de mascotas'],
        ];

        foreach ($roles as $item) {
            Role::create($item);
        }
    }
}
