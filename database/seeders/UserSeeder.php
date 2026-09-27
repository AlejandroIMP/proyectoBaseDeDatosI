<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Test User', 'email' => 'test@example.com', 'role' => 'Administrador'],
            ['name' => 'Admin Principal', 'email' => 'admin@refugio.test', 'role' => 'Administrador'],
            ['name' => 'Ana Gómez', 'email' => 'ana.gomez@refugio.test', 'role' => 'Veterinario'],
            ['name' => 'Carlos Pérez', 'email' => 'carlos.perez@refugio.test', 'role' => 'Veterinario'],
            ['name' => 'Lucía Ramírez', 'email' => 'lucia.ramirez@refugio.test', 'role' => 'Recepcionista'],
            ['name' => 'Mario López', 'email' => 'mario.lopez@refugio.test', 'role' => 'Recepcionista'],
            ['name' => 'José Martínez', 'email' => 'jose.martinez@refugio.test', 'role' => 'Voluntario'],
            ['name' => 'Elena Torres', 'email' => 'elena.torres@refugio.test', 'role' => 'Voluntario'],
            ['name' => 'Pedro Sánchez', 'email' => 'pedro.sanchez@refugio.test', 'role' => 'Voluntario'],
        ];

        foreach ($users as $item) {
            $user = User::create([
                'name' => $item['name'],
                'email' => $item['email'],
                'password' => 'password',
            ]);

            $role = Role::where('name', $item['role'])->first();
            $user->roles()->syncWithoutDetaching([$role->id]);
        }
    }
}
