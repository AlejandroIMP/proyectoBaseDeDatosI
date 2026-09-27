<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'manage_pets', 'description' => 'Crear, editar y eliminar mascotas'],
            ['name' => 'manage_adoptions', 'description' => 'Gestionar solicitudes de adopción'],
            ['name' => 'manage_appointments', 'description' => 'Gestionar citas'],
            ['name' => 'manage_donations', 'description' => 'Gestionar donaciones y donantes'],
            ['name' => 'manage_treatments', 'description' => 'Gestionar tratamientos médicos'],
            ['name' => 'manage_rescues', 'description' => 'Registrar rescates de animales'],
            ['name' => 'manage_users', 'description' => 'Gestionar usuarios y roles del sistema'],
            ['name' => 'view_reports', 'description' => 'Ver reportes y estadísticas'],
        ];

        foreach ($permissions as $item) {
            Permission::create($item);
        }
    }
}
