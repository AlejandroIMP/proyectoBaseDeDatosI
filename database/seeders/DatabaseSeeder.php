<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Catálogos sin dependencias
            SpeciesSeeder::class,
            ColorSeeder::class,
            PetStatusSeeder::class,
            ShelterSeeder::class,
            ResidenceSeeder::class,
            AdoptionStatusSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            TreatmentTypeSeeder::class,
            OccupationSeeder::class,
            AppointmentTypeSeeder::class,

            // Catálogos con dependencias simples
            BreedSeeder::class,
            TreatmentSeeder::class,
            PermissionRoleSeeder::class,

            // Usuarios y personas
            UserSeeder::class,
            PersonSeeder::class,
            DonorSeeder::class,

            // Mascotas y su información asociada
            PetSeeder::class,
            PetHistorySeeder::class,
            PetTreatmentSeeder::class,

            // Procesos operativos
            AppointmentSeeder::class,
            RescueSeeder::class,
            AdoptionSeeder::class,
            AdoptionFollowUpSeeder::class,
            DonationSeeder::class,
            ContractSeeder::class,
        ]);
    }
}
