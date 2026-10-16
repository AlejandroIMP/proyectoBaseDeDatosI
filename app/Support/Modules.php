<?php

namespace App\Support;

use App\Models\Adoption;
use App\Models\AdoptionFollowUp;
use App\Models\AdoptionStatus;
use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Breed;
use App\Models\Color;
use App\Models\Contract;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Occupation;
use App\Models\Permission;
use App\Models\Person;
use App\Models\Pet;
use App\Models\PetHistory;
use App\Models\PetStatus;
use App\Models\PetTreatment;
use App\Models\Rescue;
use App\Models\Residence;
use App\Models\Role;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\Treatment;
use App\Models\TreatmentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Registry of the CRUD modules: drives the web routes, the navigation menu and the dashboard.
 *
 * Each slug maps to the Volt page resources/views/livewire/{slug}/index.blade.php and the route "crud.{slug}".
 */
class Modules
{
    /**
     * @return array<string, array<string, array{label: string, model: class-string<Model>}>>
     */
    public static function groups(): array
    {
        return [
            'Mascotas' => [
                'pets' => ['label' => 'Mascotas', 'model' => Pet::class],
                'pet-histories' => ['label' => 'Historial', 'model' => PetHistory::class],
                'pet-treatments' => ['label' => 'Tratamientos aplicados', 'model' => PetTreatment::class],
                'rescues' => ['label' => 'Rescates', 'model' => Rescue::class],
                'appointments' => ['label' => 'Citas', 'model' => Appointment::class],
            ],
            'Adopciones' => [
                'adoptions' => ['label' => 'Adopciones', 'model' => Adoption::class],
                'adoption-follow-ups' => ['label' => 'Seguimientos', 'model' => AdoptionFollowUp::class],
                'contracts' => ['label' => 'Contratos', 'model' => Contract::class],
            ],
            'Personas' => [
                'people' => ['label' => 'Personas', 'model' => Person::class],
                'donors' => ['label' => 'Donantes', 'model' => Donor::class],
                'donations' => ['label' => 'Donaciones', 'model' => Donation::class],
            ],
            'Catálogos' => [
                'species' => ['label' => 'Especies', 'model' => Species::class],
                'breeds' => ['label' => 'Razas', 'model' => Breed::class],
                'colors' => ['label' => 'Colores', 'model' => Color::class],
                'pet-statuses' => ['label' => 'Estados de mascota', 'model' => PetStatus::class],
                'shelters' => ['label' => 'Refugios', 'model' => Shelter::class],
                'residences' => ['label' => 'Viviendas', 'model' => Residence::class],
                'occupations' => ['label' => 'Ocupaciones', 'model' => Occupation::class],
                'adoption-statuses' => ['label' => 'Estados de adopción', 'model' => AdoptionStatus::class],
                'treatment-types' => ['label' => 'Tipos de tratamiento', 'model' => TreatmentType::class],
                'treatments' => ['label' => 'Tratamientos', 'model' => Treatment::class],
                'appointment-types' => ['label' => 'Tipos de cita', 'model' => AppointmentType::class],
            ],
            'Seguridad' => [
                'users' => ['label' => 'Usuarios', 'model' => User::class],
                'roles' => ['label' => 'Roles', 'model' => Role::class],
                'permissions' => ['label' => 'Permisos', 'model' => Permission::class],
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function slugs(): array
    {
        return array_keys(array_merge(...array_values(static::groups())));
    }
}
