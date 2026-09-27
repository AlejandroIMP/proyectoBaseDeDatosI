<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'Administrador' => [
                'manage_pets', 'manage_adoptions', 'manage_appointments', 'manage_donations',
                'manage_treatments', 'manage_rescues', 'manage_users', 'view_reports',
            ],
            'Veterinario' => ['manage_pets', 'manage_treatments', 'view_reports'],
            'Recepcionista' => ['manage_adoptions', 'manage_appointments', 'manage_donations'],
            'Voluntario' => ['manage_rescues'],
        ];

        foreach ($map as $roleName => $permissionNames) {
            $role = Role::where('name', $roleName)->first();
            $permissionIds = Permission::whereIn('name', $permissionNames)->pluck('id');
            $role->permissions()->syncWithoutDetaching($permissionIds);
        }
    }
}
